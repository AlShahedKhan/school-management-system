@props([
    'slides' => [],
])

<figure
    {{ $attributes->class([
        'relative mt-3 aspect-[11/5] w-full overflow-hidden bg-[#ff6f79] shadow-sm',
        'md:aspect-[12/5] lg:aspect-[16/6] xl:aspect-[16/5] xl:max-h-[360px]',
    ]) }}
    data-dashboard-hero
>
    @foreach ($slides as $slide)
        <img
            src="{{ $slide['src'] }}"
            alt="{{ $slide['alt'] }}"
            @class([
                'dashboard-hero-slide absolute inset-0 h-full w-full object-cover object-center',
                'is-active' => $loop->first,
            ])
            data-dashboard-hero-slide
        >
    @endforeach
</figure>
