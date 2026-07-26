<x-school.data-table
    :empty="$teachers->isEmpty()"
    :empty-colspan="11"
    empty-message="No teachers found."
    :show-footer="$teachers->hasPages()"
>
    <x-slot:columns>
            <colgroup>
                <col style="width: 4%;">
                <col style="width: 7%;">
                <col style="width: 14%;">
                <col style="width: 10%;">
                <col style="width: 12%;">
                <col style="width: 11%;">
                <col style="width: 10%;">
                <col style="width: 11%;">
                <col style="width: 10%;">
                <col style="width: 8%;">
                <col style="width: 110px;">
            </colgroup>
    </x-slot:columns>

    <x-slot:head>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">SL</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Photo</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Name</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">ID Number</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Designation</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Mobile Number</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-right font-semibold">Salary</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Salary Start Date</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Pay Date</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Status</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold min-w-[110px]">Action</x-table.th>
    </x-slot:head>

    <tbody id="teacherTableBody">
    @foreach ($teachers as $teacher)
        <x-table.row unstyled class="hover:bg-gray-50">
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">{{ $loop->iteration }}</x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                <img
                    src="{{ $teacher->photo ? asset('storage/' . $teacher->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($teacher->name) }}"
                    alt="{{ $teacher->name }}"
                    class="h-6 w-6 rounded-full object-cover inline-block">
            </x-table.td>
            <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                <x-school.table-cell-scroll :title="$teacher->name">
                    {{ $teacher->name }}
                </x-school.table-cell-scroll>
            </x-table.td>
            <x-table.td unstyled class="h-8 border border-gray-300 px-3 font-mono">
                <x-school.table-cell-scroll :title="$teacher->id_number">
                    {{ $teacher->id_number }}
                </x-school.table-cell-scroll>
            </x-table.td>
            <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                <x-school.table-cell-scroll :title="$teacher->designation">
                    {{ $teacher->designation }}
                </x-school.table-cell-scroll>
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3">
                <x-school.table-cell-scroll :title="$teacher->mobile">
                    <a
                        href="tel:{{ $teacher->mobile }}"
                        class="inline-block text-blue-500"
                        style="text-decoration: none !important;">
                        {{ $teacher->mobile }}
                    </a>
                </x-school.table-cell-scroll>
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-right font-medium">
                ৳{{ number_format($teacher->salary_amount ?? 0, 2) }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                {{ $teacher->salary_start_date ? \Carbon\Carbon::parse($teacher->salary_start_date)->format('Y-m-d') : '-' }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                {{ $teacher->pay_date ?? '-' }}
            </x-table.td>
            <x-table.td unstyled class="h-8 border border-gray-300 px-3 text-center">
                @if(($teacher->status?->value ?? $teacher->status) === 'Hold')
                    <span class="px-2 py-0.5 text-[10px] font-semibold bg-red-100 text-red-700 rounded-full">Hold</span>
                @else
                    <span class="px-2 py-0.5 text-[10px] font-semibold bg-green-100 text-green-700 rounded-full">Active</span>
                @endif
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center min-w-[110px]">
                <x-action.group>
                    <x-action.button
                        variant="edit"
                        label="Edit {{ $teacher->name }}"
                        onclick="editTeacher({{ $teacher->id }})"
                    />
                    <x-action.button
                        variant="status"
                        label="Toggle Status for {{ $teacher->name }}"
                        onclick="toggleTeacherStatus({{ $teacher->id }}, '{{ $teacher->status?->value ?? $teacher->status ?? 'Active' }}', '{{ addslashes($teacher->name) }}', '{{ addslashes($teacher->designation ?? '') }}', '{{ $teacher->id_number }}')"
                    />
                    <x-action.button
                        variant="delete"
                        label="Delete {{ $teacher->name }}"
                        onclick="deleteTeacher({{ $teacher->id }})"
                    />
                </x-action.group>
            </x-table.td>
        </x-table.row>
    @endforeach
    </tbody>

    <x-slot:footer>
        {{ $teachers->links() }}
    </x-slot:footer>
</x-school.data-table>
