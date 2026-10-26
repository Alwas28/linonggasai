@props(['subtitle' => 'Digital Creative Hub', 'dark' => false, 'href' => null])

<a href="{{ $href ?? route('home') }}" {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    <img src="{{ asset('images/logo-bumdes-meohai.png') }}" class="h-10 w-10 shrink-0 rounded-full bg-white p-0.5 shadow-sm" alt="Logo BUMDes Meohai">
    <div class="leading-tight">
        <div class="font-display text-sm font-extrabold tracking-tight {{ $dark ? 'text-cream' : 'text-forest-700' }}">L-DCH LINONGGASAI</div>
        <div class="text-[10px] font-semibold uppercase tracking-widest {{ $dark ? 'text-harvest-300' : 'text-harvest-600' }}">{{ $subtitle }}</div>
    </div>
</a>
