<x-school.list-header
    title="Teacher Registration"
    breadcrumb-current="Teacher Registration"
>
    <x-slot:search>
        @include('school.teacher.partials.search-desktop')
    </x-slot:search>

    <x-slot:actions>
        @include('school.teacher.partials.export')
    </x-slot:actions>

    <x-slot:mobile-search>
        @include('school.teacher.partials.search-mobile')
    </x-slot:mobile-search>
</x-school.list-header>

@include('school.partials.export-dropdown')
