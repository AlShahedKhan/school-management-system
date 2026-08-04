<x-school.list-header
    title="Expense Management"
    breadcrumb-parent="School"
    breadcrumb-current="Expense"
>
    <x-slot:search>
        @include('school.hrm.expense.partials.search-desktop')
    </x-slot:search>

    <x-slot:actions>
        @include('school.hrm.expense.partials.export')
    </x-slot:actions>

    <x-slot:mobile-search>
        @include('school.hrm.expense.partials.search-mobile')
    </x-slot:mobile-search>
</x-school.list-header>

@include('school.partials.export-dropdown')