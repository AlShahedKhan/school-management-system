@props([
    'items' => [],
])

<section
    {{ $attributes->class([
        'min-w-0 border border-gray-200 bg-white p-2.5 shadow-md sm:p-4',
    ]) }}
    aria-labelledby="recent-admissions-title"
>
    <header class="mb-3 flex min-h-8 items-center">
        <h3 id="recent-admissions-title" class="!m-0 truncate !text-[15px] !font-normal leading-tight text-gray-800 sm:!text-xl">
            Recent Added New Student
        </h3>
    </header>

    <x-school.data-table
        :empty="count($items) === 0"
        :empty-colspan="12"
        empty-message="No recent admissions found."
        min-width="1460px"
        frame-class="school-data-table-frame border-0 bg-white p-0 shadow-none"
    >
        <x-slot:columns>
            <colgroup>
                <col style="width: 4%;">
                <col style="width: 5%;">
                <col style="width: 10%;">
                <col style="width: 13%;">
                <col style="width: 10%;">
                <col style="width: 12%;">
                <col style="width: 8%;">
                <col style="width: 8%;">
                <col style="width: 8%;">
                <col style="width: 8%;">
                <col style="width: 9%;">
                <col style="width: 7%;">
            </colgroup>
        </x-slot:columns>

        <x-slot:head>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Sl</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Photo</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Id Number</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Student Name</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Mobile Number</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Father Name</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Class</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Group</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Section</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Session</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Student Type</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Status</x-table.th>
        </x-slot:head>

        @foreach ($items as $item)
            <x-table.row unstyled class="hover:bg-gray-50">
                <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left">
                    {{ $loop->iteration }}
                </x-table.td>

                <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left">
                    <img
                        src="{{ $item['photo'] }}"
                        alt="{{ $item['name'] }}"
                        class="h-6 w-6 rounded-full object-cover"
                    >
                </x-table.td>

                <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                    <x-school.table-cell-scroll :title="$item['student_id']">
                        <span class="font-mono text-xs text-slate-600">{{ $item['student_id'] }}</span>
                    </x-school.table-cell-scroll>
                </x-table.td>

                <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                    <x-school.table-cell-scroll :title="$item['name']">
                        <span class="text-xs font-semibold text-slate-700">{{ $item['name'] }}</span>
                    </x-school.table-cell-scroll>
                </x-table.td>

                <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                    <x-school.table-cell-scroll :title="$item['mobile']">
                        <a href="tel:{{ $item['mobile'] }}" class="text-xs text-blue-500" style="text-decoration: none !important;">
                            {{ $item['mobile'] }}
                        </a>
                    </x-school.table-cell-scroll>
                </x-table.td>

                <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                    <x-school.table-cell-scroll :title="$item['father_name']">
                        <span class="text-xs text-slate-600">{{ $item['father_name'] }}</span>
                    </x-school.table-cell-scroll>
                </x-table.td>

                <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                    <x-school.table-cell-scroll :title="$item['class']">
                        <span class="text-xs text-slate-600">{{ $item['class'] }}</span>
                    </x-school.table-cell-scroll>
                </x-table.td>

                <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                    <x-school.table-cell-scroll :title="$item['group']">
                        <span class="text-xs text-slate-600">{{ $item['group'] }}</span>
                    </x-school.table-cell-scroll>
                </x-table.td>

                <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                    <x-school.table-cell-scroll :title="$item['section']">
                        <span class="text-xs text-slate-600">{{ $item['section'] }}</span>
                    </x-school.table-cell-scroll>
                </x-table.td>

                <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                    <x-school.table-cell-scroll :title="$item['session']">
                        <span class="text-xs text-slate-600">{{ $item['session'] }}</span>
                    </x-school.table-cell-scroll>
                </x-table.td>

                <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                    <x-school.table-cell-scroll :title="$item['student_type']">
                        <span class="text-xs text-slate-600">{{ $item['student_type'] }}</span>
                    </x-school.table-cell-scroll>
                </x-table.td>

                <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                    <x-school.table-cell-scroll :title="$item['status']">
                        <span class="{{ $item['status_class'] }} rounded-full px-2 py-0.5 text-[10px] font-semibold">
                            {{ $item['status'] }}
                        </span>
                    </x-school.table-cell-scroll>
                </x-table.td>

            </x-table.row>
        @endforeach
    </x-school.data-table>
</section>
