@extends('layouts.public')

@section('title', $page?->title ?? 'Lokasi')

@section('content')
    @include('public.partials.page-header', ['kicker' => 'Lokasi Desa', 'fallbackTitle' => 'Linonggasai, Wonggeduku Barat'])

    <section class="mx-auto max-w-6xl px-6 pb-20 pt-4">
        @include('public.partials.location-cards')
    </section>
@endsection
