<div class="reveal mt-10 grid gap-6 md:grid-cols-2">
    @foreach ([
        ['gazebo-bumdes-meohai.jpg', 'Unit Usaha Desa', 'Gazebo BUMDes Meohai', 'Titik kumpul warga & unit usaha di tepi sawah desa.'],
        ['kantor-desa-linonggasai.jpg', 'Pemerintahan Desa', 'Kantor Desa Linonggasai', 'Pusat administrasi & koordinasi program L-DCH.'],
    ] as [$image, $kicker, $title, $text])
        <div class="group overflow-hidden rounded-2xl border border-forest-100">
            <div class="aspect-[4/3] overflow-hidden">
                <img src="{{ asset('images/'.$image) }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" alt="{{ $title }}">
            </div>
            <div class="bg-white p-5">
                <div class="text-xs font-bold uppercase tracking-widest text-clay-500">{{ $kicker }}</div>
                <h4 class="mt-1 font-display font-bold text-forest-700">{{ $title }}</h4>
                <p class="mt-1 text-xs text-forest-500">{{ $text }}</p>
            </div>
        </div>
    @endforeach
</div>
<div class="reveal mt-8 flex flex-wrap justify-center gap-3 text-sm font-semibold text-forest-600">
    @foreach (['📍 Desa Linonggasai', 'Kec. Wonggeduku Barat', 'Kab. Konawe', 'Sulawesi Tenggara'] as $chip)
        <span class="rounded-full border border-forest-100 bg-white px-4 py-2">{{ $chip }}</span>
    @endforeach
</div>
