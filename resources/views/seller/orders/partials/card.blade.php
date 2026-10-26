<a href="{{ route('seller.orders.show', $order) }}" class="card block p-4 transition hover:border-harvest-300">
    <div class="flex items-center justify-between gap-2">
        <span class="text-xs font-bold text-forest-500">{{ $order->order_code }} · {{ $order->created_at->diffForHumans() }}</span>
        <x-badge :status="$order->order_status" />
    </div>
    <div class="mt-3 flex items-center gap-3">
        <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-forest-50 text-2xl" aria-hidden="true">📦</div>
        <div class="min-w-0 flex-1">
            <div class="truncate font-display font-bold">
                {{ $order->items->first()?->product_name ?? '-' }}
                <span class="text-sm font-semibold text-forest-500">× {{ $order->items->first()?->quantity }}</span>
                @if ($order->items->count() > 1)
                    <span class="text-sm font-semibold text-forest-500">+{{ $order->items->count() - 1 }} lainnya</span>
                @endif
            </div>
            <div class="truncate text-xs text-forest-500">{{ $order->buyer_name }} · {{ $order->payment_status->label() }}</div>
        </div>
        <div class="text-sm font-bold">{{ $order->formattedTotal() }}</div>
    </div>
</a>
