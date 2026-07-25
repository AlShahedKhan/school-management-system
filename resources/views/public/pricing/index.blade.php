@extends('layouts.public')

@section('title', ($brandAssets['brandTitle'] ?? 'Astha Academics') . ' | ' . public_trans('public.packages.eyebrow'))

@section('content')
    <div class="public-pricing-shell bg-[#f7f9fc]">
        <x-public.pricing-section
            :packages="$packages"
            :show-empty-state="true"
            section-class="mx-auto w-full max-w-[1280px] px-4 py-14 sm:px-6 sm:py-16 lg:px-8 lg:py-20"
        />
    </div>
@endsection
