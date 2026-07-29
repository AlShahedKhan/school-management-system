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
        setGradeFullMarkValue('100', '100 Mark Grade');
        document.getElementById('custom_full_mark').value = '';
        document.getElementById('customFullMarkInput').classList.add('hidden');
        document.querySelectorAll('#gradeForm .text-red-500').forEach(e => e.classList.add('hidden'));
    }

    function toggleCustomFullMarkInput() {
        const container = document.getElementById('customFullMarkInput');
        const input = document.getElementById('custom_full_mark');
        const isHidden = container.classList.toggle('hidden');

        if (!isHidden) {
            container.classList.add('flex');
            input.focus();
        } else {
            container.classList.remove('flex');
        }
    }

    function setGradeFullMarkValue(value, label) {
        const input = document.getElementById('full_mark');
        const selectedLabel = document.querySelector('#full_markButton [data-dropdown-select-label]');
        const menu = document.getElementById('full_markMenu');

        if (input) input.value = value;
        if (selectedLabel) selectedLabel.textContent = label;

        menu?.querySelectorAll('[data-dropdown-select-option]').forEach(option => {
            const selected = option.dataset.value === String(value);
            option.setAttribute('aria-selected', String(selected));
            option.classList.toggle('bg-slate-100', selected);
            option.classList.toggle('text-slate-900', selected);
            option.classList.toggle('text-slate-800', !selected);
        });
    }

    function ensureGradeFullMarkOption(value) {
        const menu = document.getElementById('full_markMenu');
        const optionValue = String(value);
        let option = Array.from(menu?.querySelectorAll('[data-dropdown-select-option]') || [])
            .find(item => item.dataset.value === optionValue);

        if (option || !menu) {
            return option;
        }

        option = document.createElement('button');
        option.type = 'button';
        option.className = 'dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight text-slate-800 transition-colors hover:bg-slate-100';
        option.dataset.value = optionValue;
        option.setAttribute('role', 'option');
        option.setAttribute('aria-selected', 'false');
        option.setAttribute('data-dropdown-select-option', '');
        option.textContent = `${optionValue} Mark Grade`;
        option.addEventListener('click', () => {
            setGradeFullMarkValue(optionValue, option.textContent.trim());
            menu.classList.add('hidden');
            document.getElementById('full_markButton')?.setAttribute('aria-expanded', 'false');
            document.getElementById('full_mark')?.dispatchEvent(new Event('change', { bubbles: true }));
        });
        menu.appendChild(option);

        return option;
    }

    function addCustomFullMark() {
        const input = document.getElementById('custom_full_mark');
        const error = document.getElementById('full_mark_error');
        const value = Number(input.value);

        if (!Number.isFinite(value) || value <= 0) {
            error.textContent = 'Please enter a valid full mark.';
            error.classList.remove('hidden');
            input.focus();
            return;
        }

        const optionValue = String(value);
        ensureGradeFullMarkOption(optionValue);
        setGradeFullMarkValue(optionValue, `${optionValue} Mark Grade`);
        input.value = '';
        error.classList.add('hidden');
        document.getElementById('customFullMarkInput').classList.add('hidden');
        document.getElementById('customFullMarkInput').classList.remove('flex');
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
        <div class="grade-row grid grid-cols-1 items-center gap-3 border border-slate-200 bg-white p-3 shadow-sm sm:grid-cols-[2fr_2fr_2fr_2fr_1fr] sm:gap-2" id="row_${rowId}">
            <div class="relative min-w-0">
                <label class="mb-1 block text-[9px] text-slate-400 sm:hidden">Minimum mark</label>
                <input type="number" step="0.01" name="mark_from" value="${data?.mark_from || ''}" class="form-input-fixed h-8 w-full border border-gray-200 px-3 text-xs" style="border-radius: 0;" placeholder="80">
                <div class="mt-1 hidden text-[10px] text-red-500 grade-field-error"></div>
            </div>
            <div class="relative min-w-0">
                <label class="mb-1 block text-[9px] text-slate-400 sm:hidden">Maximum mark</label>
                <input type="number" step="0.01" name="mark_to" value="${data?.mark_to || ''}" class="form-input-fixed h-8 w-full border border-gray-200 px-3 text-xs" style="border-radius: 0;" placeholder="100">
                <div class="mt-1 hidden text-[10px] text-red-500 grade-field-error"></div>
            </div>
            <div class="relative min-w-0">
                <label class="mb-1 block text-[9px] text-slate-400 sm:hidden">Letter name</label>
                <input type="text" name="grade_name" value="${data?.grade_name || ''}" class="form-input-fixed h-8 w-full border border-gray-200 px-3 text-xs" style="border-radius: 0;" placeholder="e.g. A+">
                <div class="mt-1 hidden text-[10px] text-red-500 grade-field-error"></div>
            </div>
            <div class="relative min-w-0">
                <label class="mb-1 block text-[9px] text-slate-400 sm:hidden">Point no</label>
                <input type="number" step="0.01" name="grade_point" value="${data?.grade_point || ''}" class="form-input-fixed h-8 w-full border border-gray-200 px-3 text-xs" style="border-radius: 0;" placeholder="5.00">
                <div class="mt-1 hidden text-[10px] text-red-500 grade-field-error"></div>
            </div>
            <div class="flex h-8 self-end items-center justify-center">
                <button type="button" title="Delete row" aria-label="Delete row" onclick="document.getElementById('row_${rowId}').remove()" class="flex h-8 w-7 items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-red-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-1">
                    <i class="far fa-trash-alt text-sm" aria-hidden="true"></i>
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
            ensureGradeFullMarkOption(fullMark);
            setGradeFullMarkValue(fullMark, `${fullMark} Mark Grade`);

            toggleStep(2);
            document.getElementById('backBtn').classList.add('hidden');
            document.getElementById('gradeModal').classList.remove('hidden');
        } catch (err) {
            Swal.fire('Error', 'Failed to load grade data.', 'error');
        }
    }
</script>
