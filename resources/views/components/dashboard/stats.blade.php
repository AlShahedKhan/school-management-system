@props([
    'cards' => [],
])

<div
    {{ $attributes->class([
        'mt-4 grid grid-cols-2 gap-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5',
    ]) }}
>
    @foreach ($cards as $card)
        <x-dashboard.stat-card
            :label="$card['label']"
            :value="$card['value']"
            :icon="$card['icon']"
            :icon-style="$card['icon_style']"
        />
    @endforeach
</div>
