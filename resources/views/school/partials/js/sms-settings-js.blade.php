<script>
document.addEventListener('DOMContentLoaded', function () {
    let cachedSmsData = [];

    window.openSmsSettingsModal = function () {
        const modal = document.getElementById('smsSettingsModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
        loadSmsSettingsData();
    };

    const closeModal = function () {
        const modal = document.getElementById('smsSettingsModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    };

    document.getElementById('closeSmsSettingsModal')?.addEventListener('click', closeModal);
    document.getElementById('closeSmsSettingsModalBtn')?.addEventListener('click', closeModal);

    const typeSelect = document.getElementById('sms_template_type');
    const bodyTextarea = document.getElementById('sms_template_body');
    const statusSelect = document.getElementById('sms_template_status');
    const placeholdersContainer = document.getElementById('sms_placeholders_container');
    const charCountEl = document.getElementById('sms_char_count');
    const partsCountEl = document.getElementById('sms_parts_count');
    const saveBtn = document.getElementById('saveSmsSettingsBtn');

    function updateCounters() {
        if (!bodyTextarea) return;
        const text = bodyTextarea.value || '';
        const length = text.length;
        if (charCountEl) charCountEl.textContent = length;
        const parts = length <= 160 ? (length > 0 ? 1 : 0) : Math.ceil(length / 153);
        if (partsCountEl) partsCountEl.textContent = parts;
    }

    bodyTextarea?.addEventListener('input', updateCounters);

    function loadSmsSettingsData() {
        if (!typeSelect) return;
        typeSelect.innerHTML = '<option value="" disabled selected>Loading templates...</option>';

        axios.get('{{ url("/api/school/sms-settings") }}')
            .then(res => {
                cachedSmsData = res.data.types || [];
                typeSelect.innerHTML = '';

                cachedSmsData.forEach((item, index) => {
                    const opt = document.createElement('option');
                    opt.value = item.type;
                    opt.textContent = item.label + (item.is_customized ? ' (Customized)' : '');
                    typeSelect.appendChild(opt);
                });

                if (cachedSmsData.length > 0) {
                    typeSelect.selectedIndex = 0;
                    renderSelectedTemplate(cachedSmsData[0]);
                }
            })
            .catch(err => {
                console.error('Error fetching SMS settings:', err);
                if (typeof Toastify === 'function') {
                    Toastify({ text: "Failed to load SMS settings", style: { background: "#ef4444" } }).showToast();
                }
            });
    }

    function renderSelectedTemplate(item) {
        if (!item) return;
        if (bodyTextarea) bodyTextarea.value = item.current_body || item.default_body || '';
        if (statusSelect) statusSelect.value = item.status || 'Active';
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

    typeSelect?.addEventListener('change', function () {
        const selectedType = this.value;
        const item = cachedSmsData.find(d => d.type === selectedType);
        if (item) {
            renderSelectedTemplate(item);
        }
    });

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

    saveBtn?.addEventListener('click', function () {
        const smsType = typeSelect?.value;
        const templateBody = bodyTextarea?.value;
        const status = statusSelect?.value;

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

});
</script>
