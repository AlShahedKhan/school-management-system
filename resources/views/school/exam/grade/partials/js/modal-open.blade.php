<script>
    function openGradeModal() {
        resetGradeForm();
        document.getElementById('gradeModalTitle').textContent = 'Add Exam Grade';
        toggleStep(1);
        document.getElementById('gradeModal').classList.remove('hidden');
    }

    function closeGradeModal() {
        document.getElementById('gradeModal').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const closeBtn = document.getElementById('closeGradeModal');
        if (closeBtn) closeBtn.addEventListener('click', closeGradeModal);
    });

    function resetGradeForm() {
        document.getElementById('gradeForm').reset();
        document.getElementById('edit_id').value = '';
        document.getElementById('gradeRowsContainer').innerHTML = '';
        document.getElementById('full_mark').value = '100';
        document.getElementById('custom_full_mark').value = '';
        document.getElementById('customFullMarkInput').classList.add('hidden');
        document.querySelectorAll('#gradeForm .text-red-500').forEach(e => e.classList.add('hidden'));
    }

    function toggleCustomFullMarkInput() {
        const container = document.getElementById('customFullMarkInput');
        const input = document.getElementById('custom_full_mark');
        const isHidden = container.classList.toggle('hidden');

        if (!isHidden) {
            input.focus();
        }
    }

    function addCustomFullMark() {
        const input = document.getElementById('custom_full_mark');
        const select = document.getElementById('full_mark');
        const error = document.getElementById('full_mark_error');
        const value = Number(input.value);

        if (!Number.isFinite(value) || value <= 0) {
            error.textContent = 'Please enter a valid full mark.';
            error.classList.remove('hidden');
            input.focus();
            return;
        }

        const optionValue = String(value);
        const exists = Array.from(select.options).some(option => option.value === optionValue);

        if (!exists) {
            select.add(new Option(`${optionValue} Mark Grade`, optionValue));
        }

        select.value = optionValue;
        input.value = '';
        error.classList.add('hidden');
        document.getElementById('customFullMarkInput').classList.add('hidden');
    }

    function toggleStep(step) {
        const s1 = document.getElementById('step1');
        const s2 = document.getElementById('step2');
        const nextBtn = document.getElementById('nextBtn');
        const backBtn = document.getElementById('backBtn');
        const saveBtn = document.getElementById('saveBtn');

        if (step === 1) {
            s1.classList.remove('hidden');
            s2.classList.add('hidden');
            nextBtn.classList.remove('hidden');
            backBtn.classList.add('hidden');
            saveBtn.classList.add('hidden');
        } else {
            s1.classList.add('hidden');
            s2.classList.remove('hidden');
            nextBtn.classList.add('hidden');
            backBtn.classList.remove('hidden');
            saveBtn.classList.remove('hidden');

            if (document.getElementById('gradeRowsContainer').children.length === 0) {
                addGradeRow();
            }
        }
    }

    function validateStep1() {
        const fm = document.getElementById('full_mark').value;
        if (!fm) {
            document.getElementById('full_mark_error').textContent = 'Please select total mark.';
            document.getElementById('full_mark_error').classList.remove('hidden');
            return;
        }
        document.getElementById('full_mark_error').classList.add('hidden');
        toggleStep(2);
    }

    function addGradeRow(data = null) {
        const container = document.getElementById('gradeRowsContainer');
        const rowId = Date.now() + Math.random();
        const rowHtml = `
        <div class="grid grid-cols-1 sm:grid-cols-9 gap-2 items-center bg-white p-3 border border-gray-100 shadow-sm grade-row" id="row_${rowId}">
            <div class="col-span-2">
                <label class="sm:hidden text-[9px] text-gray-400">Minimum mark</label>
                <input type="number" step="0.01" name="mark_from" value="${data?.mark_from || ''}" class="w-full border border-gray-200 py-1 px-2 text-[11px] h-[30px]" style="border-radius: 0;" placeholder="80">
                <div class="mt-1 hidden text-[10px] text-red-500 grade-field-error"></div>
            </div>
            <div class="col-span-2">
                <label class="sm:hidden text-[9px] text-gray-400">Maximum mark</label>
                <input type="number" step="0.01" name="mark_to" value="${data?.mark_to || ''}" class="w-full border border-gray-200 py-1 px-2 text-[11px] h-[30px]" style="border-radius: 0;" placeholder="100">
                <div class="mt-1 hidden text-[10px] text-red-500 grade-field-error"></div>
            </div>
            <div class="col-span-2">
                <label class="sm:hidden text-[9px] text-gray-400">Letter name</label>
                <input type="text" name="grade_name" value="${data?.grade_name || ''}" class="w-full border border-gray-200 py-1 px-2 text-[11px] h-[30px]" style="border-radius: 0;" placeholder="e.g. A+">
                <div class="mt-1 hidden text-[10px] text-red-500 grade-field-error"></div>
            </div>
            <div class="col-span-2">
                <label class="sm:hidden text-[9px] text-gray-400">Point no</label>
                <input type="number" step="0.01" name="grade_point" value="${data?.grade_point || ''}" class="w-full border border-gray-200 py-1 px-2 text-[11px] h-[30px]" style="border-radius: 0;" placeholder="5.00">
                <div class="mt-1 hidden text-[10px] text-red-500 grade-field-error"></div>
            </div>
            <div class="col-span-1 flex justify-end">
                <button type="button" onclick="document.getElementById('row_${rowId}').remove()" class="text-red-400 hover:text-red-600 transition-colors">
                    <i class="far fa-trash-alt"></i>
                </button>
            </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', rowHtml);
    }

    async function editGrade(id) {
        try {
            const res = await axios.get('/api/school-exam-grades/' + id);
            const item = res.data;

            document.getElementById('edit_id').value = item.id;
            document.getElementById('gradeModalTitle').textContent = 'Edit Exam Grade';
            document.getElementById('gradeRowsContainer').innerHTML = '';
            addGradeRow(item);
            const fullMark = String(Number(item.full_mark));
            const fullMarkSelect = document.getElementById('full_mark');
            if (!Array.from(fullMarkSelect.options).some(option => option.value === fullMark)) {
                fullMarkSelect.add(new Option(`${fullMark} Mark Grade`, fullMark));
            }
            fullMarkSelect.value = fullMark;

            toggleStep(2);
            document.getElementById('backBtn').classList.add('hidden');
            document.getElementById('gradeModal').classList.remove('hidden');
        } catch (err) {
            Swal.fire('Error', 'Failed to load grade data.', 'error');
        }
    }
</script>
