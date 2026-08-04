<script>
(function () {
    let cachedSmsData = [];

    function getDropdownParts(id) {
        const input = document.getElementById(id);
        const root = input?.closest('[data-dropdown-select]');
        return {
            input,
            root,
            button: root?.querySelector('[data-dropdown-select-button]'),
            label: root?.querySelector('[data-dropdown-select-label]'),
            menu: root?.querySelector('[data-dropdown-select-menu]'),
        };
    }

    function setDropdownSelectValue(id, value = '', label = null, shouldNotify = true) {
        const parts = getDropdownParts(id);
        if (!parts.input) return;

        const selected = Array.from(parts.menu?.querySelectorAll('[data-dropdown-select-option]') || [])
            .find(option => String(option.dataset.value || '') === String(value || ''));
        const placeholder = parts.label?.dataset.placeholder || 'Select...';

        parts.input.value = value || '';
        if (parts.label) {
            parts.label.textContent = label ?? selected?.textContent.trim() ?? placeholder;
        }

        parts.menu?.querySelectorAll('[data-dropdown-select-option]').forEach(option => {
            const isSelected = option === selected;
            option.classList.toggle('bg-slate-100', isSelected);
            option.classList.toggle('text-slate-900', isSelected);
            option.classList.toggle('text-slate-800', !isSelected);
            option.setAttribute('aria-selected', String(isSelected));
        });

        if (shouldNotify) {
            parts.input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    function fillDropdownSelectOptions(id, items, valueKey = 'id', labelKey = 'label') {
        const parts = getDropdownParts(id);
        if (!parts.menu) return;

        parts.menu.innerHTML = '';

        (items || []).forEach(item => {
            const val = item[valueKey] ?? item.type ?? '';
            const lbl = item[labelKey] ? (item[labelKey] + (item.is_customized ? ' (Customized)' : '')) : val;

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight text-slate-800 transition-colors hover:bg-slate-100';
            btn.dataset.value = String(val);
            btn.dataset.type = String(item.type || '');
            btn.setAttribute('data-dropdown-select-option', '');
            btn.setAttribute('role', 'option');
            btn.setAttribute('aria-selected', 'false');
            btn.textContent = lbl;

            btn.addEventListener('click', () => {
                setDropdownSelectValue(id, btn.dataset.value, btn.textContent.trim(), true);
                parts.menu.classList.add('hidden');
                parts.button?.setAttribute('aria-expanded', 'false');
            });

            parts.menu.appendChild(btn);
        });
    }

    function updateCounters() {
        const bodyTextarea = document.getElementById('sms_template_body');
        const charCountEl = document.getElementById('sms_char_count');
        const partsCountEl = document.getElementById('sms_parts_count');
        if (!bodyTextarea) return;
        const text = bodyTextarea.value || '';
        const length = text.length;
        if (charCountEl) charCountEl.textContent = length;
        const parts = length <= 160 ? (length > 0 ? 1 : 0) : Math.ceil(length / 153);
        if (partsCountEl) partsCountEl.textContent = parts;
    }

    function insertAtCursor(myField, myValue) {
        if (!myField) return;
        if (document.selection) {
            myField.focus();
            const sel = document.selection.createRange();
            sel.text = myValue;
        } else if (myField.selectionStart || myField.selectionStart === 0) {
            const startPos = myField.selectionStart;
            const endPos = myField.selectionEnd;
            myField.value = myField.value.substring(0, startPos) + myValue + myField.value.substring(endPos, myField.value.length);
            myField.selectionStart = startPos + myValue.length;
            myField.selectionEnd = startPos + myValue.length;
        } else {
            myField.value += myValue;
        }
        myField.focus();
    }

    function renderSelectedTemplate(item) {
        if (!item) return;
        const bodyTextarea = document.getElementById('sms_template_body');
        const placeholdersContainer = document.getElementById('sms_placeholders_container');

        if (bodyTextarea) bodyTextarea.value = item.current_body || item.default_body || '';
        const currentStatus = item.status || 'Active';
        setDropdownSelectValue('sms_template_status', currentStatus, null, false);
        updateCounters();

        if (placeholdersContainer) {
            placeholdersContainer.innerHTML = '';
            const placeholders = item.placeholders || [];
            placeholders.forEach(tag => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'px-2 py-1 text-[11px] font-mono font-bold bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white border border-blue-200 transition-all rounded-none cursor-pointer';
                btn.textContent = tag;
                btn.title = 'Click to insert ' + tag;
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    insertAtCursor(bodyTextarea, tag);
                    updateCounters();
                });
                placeholdersContainer.appendChild(btn);
            });
        }
    }

    window.openSmsSettingsModal = function () {
        const modal = document.getElementById('smsSettingsModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
        window.loadSmsSettingsData();
    };

    window.loadSmsSettingsData = function () {
        const typeInput = document.getElementById('sms_template_type');
        if (!typeInput) return;

        fillDropdownSelectOptions('sms_template_type', [{ id: '', type: '', label: 'Loading templates...' }], 'id', 'label');
        setDropdownSelectValue('sms_template_type', '', 'Loading templates...', false);

        axios.get('{{ url("/api/school/sms-settings") }}')
            .then(res => {
                cachedSmsData = res.data.types || [];
                fillDropdownSelectOptions('sms_template_type', cachedSmsData, 'id', 'label');

                if (cachedSmsData.length > 0) {
                    const first = cachedSmsData[0];
                    const firstLabel = first.label + (first.is_customized ? ' (Customized)' : '');
                    setDropdownSelectValue('sms_template_type', first.id, firstLabel, false);
                    renderSelectedTemplate(first);
                } else {
                    setDropdownSelectValue('sms_template_type', '', 'No templates available', false);
                }
            })
            .catch(err => {
                console.error('Error fetching SMS settings:', err);
                fillDropdownSelectOptions('sms_template_type', [], 'id', 'label');
                setDropdownSelectValue('sms_template_type', '', 'Failed to load templates', false);
                if (typeof Toastify === 'function') {
                    Toastify({ text: "Failed to load SMS settings", style: { background: "#ef4444" } }).showToast();
                }
            });
    };

    document.addEventListener('DOMContentLoaded', function () {
        const closeModal = function () {
            const modal = document.getElementById('smsSettingsModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        };

        document.getElementById('closeSmsSettingsModal')?.addEventListener('click', closeModal);
        document.getElementById('closeSmsSettingsModalBtn')?.addEventListener('click', closeModal);

        const typeInput = document.getElementById('sms_template_type');
        const bodyTextarea = document.getElementById('sms_template_body');
        const statusInput = document.getElementById('sms_template_status');
        const saveBtn = document.getElementById('saveSmsSettingsBtn');

        bodyTextarea?.addEventListener('input', updateCounters);

        typeInput?.addEventListener('change', function () {
            const selectedVal = this.value;
            const item = cachedSmsData.find(d => String(d.id) === String(selectedVal) || String(d.type) === String(selectedVal));
            if (item) {
                renderSelectedTemplate(item);
            }
        });

        saveBtn?.addEventListener('click', function () {
            const selectedVal = typeInput?.value;
            const templateBody = bodyTextarea?.value;
            const status = statusInput?.value || 'Active';

            const item = cachedSmsData.find(d => String(d.id) === String(selectedVal) || String(d.type) === String(selectedVal));
            const smsType = item ? item.type : selectedVal;

            if (!smsType || !templateBody) {
                alert('Please select a template and enter message text.');
                return;
            }

            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';

            axios.post('{{ url("/api/school/sms-settings") }}', {
                sms_type: smsType,
                template_body: templateBody,
                status: status
            })
            .then(res => {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Settings';
                if (item) {
                    item.current_body = templateBody;
                    item.status = status;
                    item.is_customized = true;
                    fillDropdownSelectOptions('sms_template_type', cachedSmsData, 'id', 'label');
                }
                if (typeof Toastify === 'function') {
                    Toastify({ text: "SMS Settings saved successfully", duration: 3000, style: { background: "#10b981" } }).showToast();
                } else {
                    alert('SMS Settings saved successfully!');
                }
                closeModal();
            })
            .catch(err => {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Settings';
                console.error('Error saving SMS settings:', err);
                const msg = err.response?.data?.message || 'Failed to save SMS settings';
                alert(msg);
            });
        });

        const modal = document.getElementById('smsSettingsModal');
        if (modal && !modal.classList.contains('hidden')) {
            window.loadSmsSettingsData();
        }
    });
})();
</script>
