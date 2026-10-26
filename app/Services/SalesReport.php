<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Sales report built from the order records sellers keep after a WhatsApp deal.
 *
 * A sale is an order with status "completed"; its date is the date the order
 * was recorded. The website never processes money, so these figures are only
 * as complete as the sellers' own records.
 */
class SalesReport
{
    /**
     * Preset periods selectable in the report filter.
     *
     * @var array<string, string>
     */
    public const PERIODS = [
        '7-hari' => '7 hari terakhir',
        '30-hari' => '30 hari terakhir',
        'bulan-ini' => 'Bulan ini',
        'bulan-lalu' => 'Bulan lalu',
        'tahun-ini' => 'Tahun ini',
        'kustom' => 'Pilih tanggal',
    ];

    public function __construct(
        public readonly CarbonImmutable $from,
        public readonly CarbonImmutable $to,
        public readonly string $period = 'kustom',
        public readonly ?int $storeId = null,
    ) {}

    public static function fromRequest(Request $request, ?int $storeId = null): self
    {
        $period = $request->string('periode', '30-hari')->toString();
        $today = CarbonImmutable::today();

        [$from, $to] = match ($period) {
            '7-hari' => [$today->subDays(6), $today],
            'bulan-ini' => [$today->startOfMonth(), $today],
            'bulan-lalu' => [$today->subMonthNoOverflow()->startOfMonth(), $today->subMonthNoOverflow()->endOfMonth()],
            'tahun-ini' => [$today->startOfYear(), $today],
            'kustom' => [
                self::parseDate($request->string('dari')->toString()) ?? $today->subDays(29),
                self::parseDate($request->string('sampai')->toString()) ?? $today,
            ],
            default => [$today->subDays(29), $today],
        };

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to, $from];
        }

        // Keep the report bounded to at most two years.
        if ($from->diffInDays($to) > 731) {
            $from = $to->subDays(731);
        }

        return new self(
            $from->startOfDay(),
            $to->endOfDay(),
            array_key_exists($period, self::PERIODS) ? $period : '30-hari',
            $storeId,
        );
    }

    private static function parseDate(string $value): ?CarbonImmutable
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        try {
            return CarbonImmutable::createFromFormat('Y-m-d', $value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    public function label(): string
    {
        return $this->from->translatedFormat('d M Y').' – '.$this->to->translatedFormat('d M Y');
    }

    /**
     * Orders recorded in the period (any status), optionally for one store.
     *
     * @return Builder<Order>
     */
    public function ordersInPeriod(): Builder
    {
        return Order::query()
            ->whereBetween('orders.created_at', [$this->from, $this->to])
            ->when($this->storeId, fn (Builder $query) => $query->where('orders.store_id', $this->storeId));
    }

    /**
     * @return Builder<Order>
     */
    public function completedOrders(): Builder
    {
        return $this->ordersInPeriod()->where('orders.order_status', OrderStatus::Completed);
    }

    /**
     * @return array{orders: int, revenue: float, items: int, average: float, confirmed_paid: float, unconfirmed_paid: float, cancelled: int, recorded: int}
     */
    public function summary(): array
    {
        $completed = $this->completedOrders()->toBase()
            ->selectRaw('count(*) as orders, coalesce(sum(total_amount), 0) as revenue')
            ->first();

        $confirmedPaid = (float) $this->completedOrders()
            ->where('payment_status', PaymentStatus::ConfirmedPaid)
            ->sum('total_amount');

        $items = (int) $this->itemsQuery()->sum('order_items.quantity');
        $orders = (int) $completed->orders;
        $revenue = (float) $completed->revenue;

        return [
            'orders' => $orders,
            'revenue' => $revenue,
            'items' => $items,
            'average' => $orders > 0 ? $revenue / $orders : 0.0,
            'confirmed_paid' => $confirmedPaid,
            'unconfirmed_paid' => $revenue - $confirmedPaid,
            'cancelled' => $this->ordersInPeriod()->where('order_status', OrderStatus::Cancelled)->count(),
            'recorded' => $this->ordersInPeriod()->count(),
        ];
    }

    /**
     * Revenue per day, or per month for periods longer than three months.
     *
     * @return Collection<int, array{label: string, revenue: float, orders: int}>
     */
    public function timeline(): Collection
    {
        $daily = $this->completedOrders()->toBase()
            ->selectRaw('date(created_at) as day, sum(total_amount) as revenue, count(*) as orders')
            ->groupByRaw('date(created_at)')
            ->get()
            ->keyBy(fn (object $row): string => substr((string) $row->day, 0, 10));

        $monthly = $this->from->diffInDays($this->to) > 92;
        $buckets = [];

        foreach (CarbonPeriod::create($this->from, $this->to) as $day) {
            $key = $monthly ? $day->format('Y-m') : $day->format('Y-m-d');
            $buckets[$key] ??= [
                'label' => $monthly ? $day->translatedFormat('M y') : $day->translatedFormat('d M'),
                'revenue' => 0.0,
                'orders' => 0,
            ];

            $row = $daily->get($day->format('Y-m-d'));
            if ($row) {
                $buckets[$key]['revenue'] += (float) $row->revenue;
                $buckets[$key]['orders'] += (int) $row->orders;
            }
        }

        return collect(array_values($buckets));
    }

    /**
     * @return Collection<int, object{store_id: int, name: string, slug: string, orders: int, revenue: float}>
     */
    public function byStore(): Collection
    {
        return $this->completedOrders()->toBase()
            ->join('stores', 'stores.id', '=', 'orders.store_id')
            ->selectRaw('orders.store_id, stores.name, stores.slug, stores.user_id, count(*) as orders, sum(orders.total_amount) as revenue')
            ->groupBy('orders.store_id', 'stores.name', 'stores.slug', 'stores.user_id')
            ->orderByDesc('revenue')
            ->get();
    }

    /**
     * @return Collection<int, object{product_name: string, store_name: string, quantity: int, revenue: float}>
     */
    public function byProduct(int $limit = 10): Collection
    {
        return $this->itemsQuery()
            ->join('stores', 'stores.id', '=', 'orders.store_id')
            ->selectRaw('order_items.product_name, stores.name as store_name, sum(order_items.quantity) as quantity, sum(order_items.subtotal) as revenue')
            ->groupBy('order_items.product_name', 'stores.name')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, object{name: string, icon: ?string, quantity: int, revenue: float}>
     */
    public function byCategory(): Collection
    {
        return $this->itemsQuery()
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->selectRaw("coalesce(categories.name, 'Lainnya') as name, categories.icon, sum(order_items.quantity) as quantity, sum(order_items.subtotal) as revenue")
            ->groupBy('categories.name', 'categories.icon')
            ->orderByDesc('revenue')
            ->get();
    }

    /**
     * Order items of completed orders in the period.
     */
    private function itemsQuery(): QueryBuilder
    {
        return DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.order_status', OrderStatus::Completed->value)
            ->whereBetween('orders.created_at', [$this->from, $this->to])
            ->when($this->storeId, fn (QueryBuilder $query) => $query->where('orders.store_id', $this->storeId));
    }

    /**
     * Stream the completed orders of the period as CSV (opens in Excel).
     *
     * @return StreamedResponse
     */
    public function downloadCsv(string $filename)
    {
        $orders = $this->completedOrders()->with(['store', 'items'])->oldest()->lazy();

        return response()->streamDownload(function () use ($orders): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Kode Pesanan', 'Tanggal', 'Toko', 'Pembeli', 'Produk', 'Jumlah', 'Harga Satuan', 'Subtotal', 'Total Pesanan', 'Status Pembayaran'], ';');

            foreach ($orders as $order) {
                foreach ($order->items as $item) {
                    fputcsv($handle, [
                        $order->order_code,
                        $order->created_at->format('Y-m-d H:i'),
                        $order->store->name,
                        $order->buyer_name,
                        $item->product_name,
                        $item->quantity,
                        (float) $item->unit_price,
                        (float) $item->subtotal,
                        (float) $order->total_amount,
                        $order->payment_status->label(),
                    ], ';');
                }
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * @return array<string, string>
     */
    public function queryParameters(): array
    {
        return $this->period === 'kustom'
            ? ['periode' => 'kustom', 'dari' => $this->from->format('Y-m-d'), 'sampai' => $this->to->format('Y-m-d')]
            : ['periode' => $this->period];
    }
}
