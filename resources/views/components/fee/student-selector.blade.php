@props([
    'id' => 'food_student_list',
    'label' => 'Select Students',
    'countId' => 'food_student_count',
])

<label class="mb-1 block text-[10px] font-medium text-slate-600">{{ $label }}</label>
<div id="{{ $id }}_all_wrapper" class="flex items-center gap-1.5 mb-1 hidden">
    <input type="checkbox" id="{{ $id }}_all" onchange="toggleStudentSelectionAll(this)" class="w-4 h-4 cursor-pointer accent-blue-600">
    <label for="{{ $id }}_all" class="text-[11px] font-semibold text-slate-500 cursor-pointer select-none">Select All</label>
</div>
<div class="border border-slate-200 overflow-x-auto custom-scrollbar mb-1 max-h-[160px] overflow-y-auto">
    <table class="w-full text-left border-collapse text-xs">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
                <th class="p-2 font-semibold text-slate-600 w-8">Sl</th>
                <th class="p-2 font-semibold text-slate-600">ID Number</th>
                <th class="p-2 font-semibold text-slate-600">Student Name</th>
                <th class="p-2 font-semibold text-slate-600 text-center w-12">
                    <span id="{{ $id }}_select_label">Select</span>
                </th>
            </tr>
        </thead>
        <tbody id="{{ $id }}" class="divide-y divide-slate-100 bg-white text-slate-700">
            <tr>
                <td colspan="4" class="p-4 text-center text-slate-400">Please select academic details first.</td>
            </tr>
        </tbody>
    </table>
</div>
<div id="{{ $countId }}" class="mt-1 text-[10px] text-gray-400 hidden"></div>
