@extends('layouts.school')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .form-input-fixed { width: 100%; border: 1px solid #cbd5e1 !important; padding: .5rem .7rem; border-radius: 0; font-size: .85rem; background: #fff; outline: none; transition: border-color 0.2s; }
        .form-input-fixed:focus { border-color: #2563eb !important; }
        input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .action-icon-btn { font-size: 1.25rem; padding: 0px !important; background: none; border: none; cursor: pointer; transition: transform 0.1s; }
        .action-icon-btn:hover { transform: scale(1.1); }
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            @include('school.fees.collection.partials.header')
            @include('school.fees.collection.partials.table')
        </div>
    </div>

    @include('school.fees.collection.partials.filter-modal')
    @include('school.fees.collection.partials.export-modal')
    @include('school.fees.collection.partials.slip-modal')
    @include('school.fees.collection.partials.collection-modal')
    @include('school.academic.class.partials.class-modal')
    @include('school.academic.group.partials.group-modal')
    @include('school.academic.section.partials.section-modal')
    @include('school.academic.session.partials.session-modal')

    <script>
        // --- Configuration & Global State ---
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        let allStudents = [];
        let allClasses = [];
        let currentPage = 1;
        let editId = null;

        function populateDropdown(menuId, data, valueField, labelField) {
            const menu = document.querySelector(`#${menuId}`);
            if (!menu) return;
            menu.innerHTML = '';
            data.forEach(item => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight transition-colors hover:bg-slate-100 text-slate-800';
                btn.dataset.value = String(item[valueField]);
                btn.textContent = item[labelField];
                btn.setAttribute('role', 'option');
                btn.setAttribute('aria-selected', 'false');
                btn.setAttribute('data-dropdown-select-option', '');
                btn.addEventListener('click', function() {
                    const root = menu.closest('[data-dropdown-select]');
                    const input = root.querySelector('[data-dropdown-select-input]');
                    const label = root.querySelector('[data-dropdown-select-label]');
                    input.value = this.dataset.value || '';
                    label.textContent = this.textContent.trim();
                    menu.querySelectorAll('[data-dropdown-select-option]').forEach(item => {
                        const sel = item === this;
                        item.classList.toggle('bg-slate-100', sel);
                        item.classList.toggle('text-slate-900', sel);
                        item.classList.toggle('text-slate-800', !sel);
                        item.setAttribute('aria-selected', String(sel));
                    });
                    menu.classList.add('hidden');
                    root.querySelector('[data-dropdown-select-button]')?.setAttribute('aria-expanded', 'false');
                    const icon = root.querySelector('[data-dropdown-select-button] i');
                    if (icon) icon.classList.remove('rotate-180');
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });
                menu.appendChild(btn);
            });
        }

        function setDropdownValue(dropdownId, value, label) {
            const input = document.querySelector(`#${dropdownId}`);
            if (input) input.value = value;
            const labelEl = document.querySelector(`#${dropdownId}Button [data-dropdown-select-label]`);
            if (labelEl) labelEl.textContent = label || (labelEl.dataset.placeholder || 'Select...');
        }

        // Filter State
        let activeFilters = {
            class: null,
            group: null,
            section: null,
            session: null,
            student: null
        };

        // --- Initialization ---
        document.addEventListener('DOMContentLoaded', async function() {
            await loadStudents();
            await loadClasses();
            setupFilterListeners();

            fetchPayments();

            populateStaticMenu('pay_methodMenu', [
                { value: 'Cash', label: 'Cash' },
                { value: 'Bank', label: 'Bank' },
            ]);

            document.getElementById('pay_method').addEventListener('change', function() {
                if (this.value === 'Bank') {
                    Swal.fire({
                        title: 'Gateway Unavailable',
                        text: 'Bank payment is not available right now. Please pay with cash.',
                        icon: 'info',
                        confirmButtonColor: '#2563eb',
                        confirmButtonText: 'Understood'
                    });
                    setDropdownValue('pay_method', 'Cash', 'Cash');
                }
            });

            // Export Modal
            const exportModal = document.getElementById('exportModal');

            function exportPopulate(selectId, options, placeholder) {
                const sel = document.getElementById(selectId);
                sel.innerHTML = `<option value="">${placeholder}</option>`;
                options.forEach(o => { if (o) sel.innerHTML += `<option value="${o}">${o}</option>`; });
            }

            // Class → Group cascade for export
            document.getElementById('exportClassFilter').addEventListener('change', function() {
                const cls = this.value;
                exportPopulate('exportGroupFilter',   [], 'All Groups');
                exportPopulate('exportSectionFilter', [], 'All Sections');
                exportPopulate('exportSessionFilter', [], 'All Sessions');
                exportPopulate('exportStudentFilter', [], '— Select Student —');
                if (!cls) return;
                const groups = [...new Set(
                    allStudents.filter(s => s.class_name === cls).map(s => s.group_name)
                )].filter(Boolean);
                exportPopulate('exportGroupFilter', groups, 'All Groups');
            });

            document.getElementById('exportGroupFilter').addEventListener('change', function() {
                const cls   = document.getElementById('exportClassFilter').value;
                const grp   = this.value;
                exportPopulate('exportSectionFilter', [], 'All Sections');
                exportPopulate('exportSessionFilter', [], 'All Sessions');
                exportPopulate('exportStudentFilter', [], '— Select Student —');
                if (!cls || !grp) return;
                const sections = [...new Set(
                    allStudents.filter(s => s.class_name === cls && s.group_name === grp).map(s => s.section_name)
                )].filter(Boolean);
                exportPopulate('exportSectionFilter', sections, 'All Sections');
            });

            document.getElementById('exportSectionFilter').addEventListener('change', function() {
                const cls = document.getElementById('exportClassFilter').value;
                const grp = document.getElementById('exportGroupFilter').value;
                const sec = this.value;
                exportPopulate('exportSessionFilter', [], 'All Sessions');
                exportPopulate('exportStudentFilter', [], '— Select Student —');
                if (!cls || !grp || !sec) return;
                const sessions = [...new Set(
                    allStudents.filter(s => s.class_name === cls && s.group_name === grp && s.section_name === sec).map(s => s.session_year)
                )].filter(Boolean);
                exportPopulate('exportSessionFilter', sessions, 'All Sessions');
            });

            document.getElementById('exportSessionFilter').addEventListener('change', function() {
                const cls = document.getElementById('exportClassFilter').value;
                const grp = document.getElementById('exportGroupFilter').value;
                const sec = document.getElementById('exportSectionFilter').value;
                const ses = this.value;
                exportPopulate('exportStudentFilter', [], '— Select Student —');
                if (!ses) return;
                const filtered = allStudents.filter(s =>
                    (!cls || s.class_name   === cls) &&
                    (!grp || s.group_name   === grp) &&
                    (!sec || s.section_name === sec) &&
                    s.session_year === ses
                ).sort((a, b) => a.student_name.localeCompare(b.student_name));
                const sel = document.getElementById('exportStudentFilter');
                sel.innerHTML = '<option value="">— Select Student —</option>';
                filtered.forEach(s => {
                    sel.innerHTML += `<option value="${s.id}">${s.student_id_number} — ${s.student_name}</option>`;
                });
            });

            document.getElementById('exportStudentFilter').addEventListener('change', function() {
                if (this.value) {
                    document.getElementById('exportStudentError').classList.add('hidden');
                    this.classList.remove('border-red-400');
                    this.classList.add('border-blue-300');
                }
            });

            document.getElementById('exportStudentIdSearch').addEventListener('input', function() {
                const sid = this.value.trim();
                const errEl = document.getElementById('exportIdNotFound');
                if (!sid) { errEl.classList.add('hidden'); return; }
                const student = allStudents.find(s => String(s.student_id_number) === sid);
                if (!student) {
                    errEl.classList.remove('hidden');
                    exportPopulate('exportClassFilter',   allClasses.map(c => c.class_name), 'All Classes');
                    exportPopulate('exportGroupFilter',   [], 'All Groups');
                    exportPopulate('exportSectionFilter', [], 'All Sections');
                    exportPopulate('exportSessionFilter', [], 'All Sessions');
                    exportPopulate('exportStudentFilter', [], '— Select Student —');
                    const studentSel = document.getElementById('exportStudentFilter');
                    studentSel.classList.remove('border-red-400');
                    studentSel.classList.add('border-blue-300');
                    document.getElementById('exportStudentError').classList.add('hidden');
                    return;
                }
                errEl.classList.add('hidden');
                exportPopulate('exportClassFilter', allClasses.map(c => c.class_name), 'All Classes');
                document.getElementById('exportClassFilter').value = student.class_name;
                const groups = [...new Set(allStudents.filter(s => s.class_name === student.class_name).map(s => s.group_name))].filter(Boolean);
                exportPopulate('exportGroupFilter', groups, 'All Groups');
                document.getElementById('exportGroupFilter').value = student.group_name;
                const sections = [...new Set(allStudents.filter(s => s.class_name === student.class_name && s.group_name === student.group_name).map(s => s.section_name))].filter(Boolean);
                exportPopulate('exportSectionFilter', sections, 'All Sections');
                document.getElementById('exportSectionFilter').value = student.section_name;
                const sessions = [...new Set(allStudents.filter(s => s.class_name === student.class_name && s.group_name === student.group_name && s.section_name === student.section_name).map(s => s.session_year))].filter(Boolean);
                exportPopulate('exportSessionFilter', sessions, 'All Sessions');
                document.getElementById('exportSessionFilter').value = student.session_year;
                const peers = allStudents.filter(s =>
                    s.class_name   === student.class_name &&
                    s.group_name   === student.group_name &&
                    s.section_name === student.section_name &&
                    s.session_year === student.session_year
                ).sort((a, b) => a.student_name.localeCompare(b.student_name));
                const studentSel = document.getElementById('exportStudentFilter');
                studentSel.innerHTML = '<option value="">— Select Student —</option>';
                peers.forEach(s => {
                    studentSel.innerHTML += `<option value="${s.id}">${s.student_id_number} — ${s.student_name}</option>`;
                });
                studentSel.value = student.id;
                studentSel.classList.remove('border-red-400');
                studentSel.classList.add('border-blue-300');
                document.getElementById('exportStudentError').classList.add('hidden');
            });

            function getExportParams() {
                const params = new URLSearchParams();
                const cls  = document.getElementById('exportClassFilter').value;
                const grp  = document.getElementById('exportGroupFilter').value;
                const sec  = document.getElementById('exportSectionFilter').value;
                const ses  = document.getElementById('exportSessionFilter').value;
                const stu  = document.getElementById('exportStudentFilter').value;
                if (cls) params.append('class',   cls);
                if (grp) params.append('group',   grp);
                if (sec) params.append('section', sec);
                if (ses) params.append('session', ses);
                if (stu) params.append('student', stu);
                const search = document.getElementById('paySearch')?.value?.trim();
                if (search) params.append('search', search);
                return params;
            }

            document.getElementById('exportPdf').addEventListener('click', async function() {
                const studentSel = document.getElementById('exportStudentFilter');
                if (!studentSel.value) {
                    studentSel.classList.remove('border-blue-300');
                    studentSel.classList.add('border-red-400');
                    document.getElementById('exportStudentError').classList.remove('hidden');
                    studentSel.focus();
                    return;
                }

                document.getElementById('exportModal').classList.add('hidden');

                Swal.fire({
                    title: 'Generating Report...',
                    text: 'Please wait.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    const studentId  = studentSel.value;
                    const studentObj = allStudents.find(s => String(s.id) === String(studentId));
                    const cls = document.getElementById('exportClassFilter').value   || '-';
                    const grp = document.getElementById('exportGroupFilter').value   || '-';
                    const sec = document.getElementById('exportSectionFilter').value || '-';
                    const ses = document.getElementById('exportSessionFilter').value || '-';

                    const [payRes, schoolRes] = await Promise.all([
                        axios.get('/api/school/payments', { params: { per_page: 1000 } }),
                        axios.get('/api/get-school-info')
                    ]);

                    const payments = (payRes.data.data || [])
                        .filter(p => String(p.admission_student_id) === String(studentId))
                        .sort((a, b) => new Date(a.pay_date) - new Date(b.pay_date));

                    const school     = schoolRes.data.data || schoolRes.data || {};
                    const schoolName = school.school_name || 'School';
                    const addressLine1 = school.village || '';
                    const addressLine2 = [school.upazila, school.district, school.division].filter(Boolean).join(', ');

                    const seenFees = new Set();
                    let grandTotal = 0, grandPaid = 0;
                    payments.forEach(p => {
                        const k = p.fees_type + '|' + p.fee_name;
                        if (!seenFees.has(k)) { seenFees.add(k); grandTotal += parseFloat(p.total_payable) || 0; }
                        grandPaid += parseFloat(p.type_amount) || 0;
                    });
                    const grandDue = Math.max(grandTotal - grandPaid, 0);

                    const fmt     = n => Number(n).toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    const fmtDate = d => { const dt = new Date(d); const months = ['January','February','March','April','May','June','July','August','September','October','November','December']; return dt.getDate() + '-' + months[dt.getMonth()] + '-' + dt.getFullYear(); };
                    const now     = new Date();
                    const genDate = fmtDate(now);
                    const today   = new Date(); today.setHours(0,0,0,0);

                    const W = 794;
                    const H = 1123;

                    const shownFees = new Set();
                    const border    = '1px solid #d1d5db';
                    const tdS       = 'padding:5px 7px;border:' + border + ';vertical-align:middle;font-size:9.5px;color:#111827;';
                    const rows = payments.length === 0
                        ? '<tr><td colspan="10" style="' + tdS + 'text-align:center;color:#6b7280;font-style:italic;padding:20px;">No payment records found.</td></tr>'
                        : payments.map(function(p, i) {
                            const total   = parseFloat(p.total_payable) || 0;
                            const paid    = parseFloat(p.type_amount)   || 0;
                            const due     = parseFloat(p.payable_due)   || 0;
                            const fk      = p.fees_type + '|' + p.fee_name;
                            const showTot = !shownFees.has(fk); if (showTot) shownFees.add(fk);
                            const pd      = new Date(p.pay_date);
                            const overdue = due > 0 && pd < today;
                            const sl      = (p.status || 'unpaid').charAt(0).toUpperCase() + (p.status || 'unpaid').slice(1);
                            const rowBg   = i % 2 === 0 ? '#ffffff' : '#f9fafb';
                            return '<tr style="background:' + rowBg + ';">'
                                + '<td style="' + tdS + 'text-align:center;padding-bottom: 50px;">' + (i + 1) + '</td>'
                                + '<td style="' + tdS + 'padding-bottom: 50px;">' + fmtDate(p.pay_date) + '</td>'
                                + '<td style="' + tdS + 'padding-bottom: 50px;">' + (p.pay_method || '-') + '</td>'
                                + '<td style="' + tdS + 'text-align:center;padding-bottom: 50px;">' + sl + '</td>'
                                + '<td style="' + tdS + 'padding-bottom: 50px;">' + (p.fees_type || '-') + '</td>'
                                + '<td style="' + tdS + 'padding-bottom: 50px;">' + (p.fee_name  || '-') + '</td>'
                                + '<td style="' + tdS + 'text-align:right;padding-bottom: 50px;">' + (showTot ? fmt(total) : '-') + '</td>'
                                + '<td style="' + tdS + 'text-align:right;padding-bottom: 50px;">' + fmt(paid) + '</td>'
                                + '<td style="' + tdS + 'text-align:right;padding-bottom: 50px;">' + fmt(due) + '</td>'
                                + '<td style="' + tdS + 'text-align:center;padding-bottom: 50px;">' + (overdue ? 'YES' : '-') + '</td>'
                                + '</tr>';
                        }).join('');

                    const tfoot = payments.length > 0
                        ? '<tfoot><tr style="background:#f3f4f6;">'
                            + '<td colspan="6" style="' + tdS + 'text-align:right;font-weight:700;border-top:2px solid #374151;">Grand Total</td>'
                            + '<td style="' + tdS + 'text-align:right;font-weight:700;border-top:2px solid #374151;">' + fmt(grandTotal) + '</td>'
                            + '<td style="' + tdS + 'text-align:right;font-weight:700;border-top:2px solid #374151;">' + fmt(grandPaid) + '</td>'
                            + '<td style="' + tdS + 'text-align:right;font-weight:700;border-top:2px solid #374151;">' + fmt(grandDue) + '</td>'
                            + '<td style="' + tdS + 'border-top:2px solid #374151;"></td>'
                            + '</tr></tfoot>'
                        : '';

                    const wrap = document.createElement('div');
                    wrap.id    = '__rpt__';
                    wrap.style.cssText = [
                        'position:fixed', 'left:-9999px', 'top:0',
                        'width:' + W + 'px',
                        'background:#fff',
                        'font-family:Arial,sans-serif',
                        'font-size:10px',
                        'color:#111827',
                        'box-sizing:border-box',
                        'padding:36px 40px'
                    ].join(';');

                    const headerHtml =
                        '<table style="width:100%;border-collapse:collapse;margin-bottom:0;border:1px solid #e5e7eb;">'
                        + '<tr>'
                        + '<td style="width:50%;vertical-align:top;padding:14px 18px;border-right:1px solid #e5e7eb;background:#f9fafb;">'
                            + '<div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#6b7280;margin-bottom:5px;">School Information</div>'
                            + '<div style="font-size:15px;font-weight:700;color:#111827;margin-bottom:5px;line-height:1.3;">' + schoolName + '</div>'
                            + (addressLine1 ? '<div style="font-size:10px;color:#374151;margin-bottom:2px;"><strong>Address:</strong> ' + addressLine1 + '</div>' : '')
                            + (addressLine2 ? '<div style="font-size:10px;color:#374151;margin-bottom:3px;">' + addressLine2 + '</div>' : '')
                            + (school.mobile ? '<div style="font-size:10px;color:#374151;margin-bottom:3px;"><strong>Phone:</strong> ' + school.mobile + '</div>' : '')
                            + (school.email ? '<div style="font-size:10px;color:#374151;margin-bottom:3px;"><strong>Email:</strong> ' + school.email + '</div>' : '')
                            + (school.eiin_number ? '<div style="font-size:10px;color:#374151;margin-bottom:14px;"><strong>EIIN:</strong> ' + school.eiin_number + '</div>' : '<div style="margin-bottom:14px;"></div>')
                        + '</td>'
                        + '<td style="width:50%;vertical-align:top;padding:14px 18px;background:#f9fafb;">'
                            + '<div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#6b7280;margin-bottom:5px;">Student Information</div>'
                            + '<div style="font-size:15px;font-weight:700;color:#111827;margin-bottom:5px;line-height:1.3;">' + (studentObj ? studentObj.student_name : '-') + '</div>'
                            + '<div style="font-size:10px;color:#374151;margin-bottom:3px;"><strong>Student ID:</strong> ' + (studentObj ? studentObj.student_id_number : '-') + '</div>'
                            + '<div style="font-size:10px;color:#374151;margin-bottom:3px;"><strong>Class:</strong> ' + cls + '</div>'
                            + '<div style="font-size:10px;color:#374151;margin-bottom:3px;"><strong>Group:</strong> ' + grp + '</div>'
                            + '<div style="font-size:10px;color:#374151;margin-bottom:3px;"><strong>Section:</strong> ' + sec + '</div>'
                            + '<div style="font-size:10px;color:#374151;margin-bottom:14px;"><strong>Session:</strong> ' + ses + '</div>'
                        + '</td>'
                        + '</tr>'
                        + '</table>';

                    const titleHtml =
                        '<div style="text-align:center;margin:14px 0 4px;padding-bottom:14px;">'
                        + '<div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#111827;">Payment Report</div>'
                        + '<div style="font-size:8.5px;color:#6b7280;margin-top:2px;">Generated: ' + genDate + '</div>'
                        + '</div>'
                        + '<div style="border-top:2px solid #111827;margin-bottom:14px;"></div>';

                    const summaryHtml =
                        '<table style="width:100%;border-collapse:collapse;margin-bottom:24px;border:1px solid #d1d5db;">'
                        + '<tr style="background:#f3f4f6;">'
                        + '<td style="padding:14px 16px 30px;border-right:1px solid #d1d5db;text-align:center;">'
                            + '<div style="font-size:8.5px;text-transform:uppercase;letter-spacing:0.06em;color:#6b7280;margin-bottom:6px;">Total Fee</div>'
                            + '<div style="font-size:16px;font-weight:700;color:#111827;padding-bottom: 30px;">&#2547; ' + fmt(grandTotal) + '</div>'
                        + '</td>'
                        + '<td style="padding:14px 16px 30px;border-right:1px solid #d1d5db;text-align:center;">'
                            + '<div style="font-size:8.5px;text-transform:uppercase;letter-spacing:0.06em;color:#6b7280;margin-bottom:6px;">Total Paid</div>'
                            + '<div style="font-size:16px;font-weight:700;color:#111827;padding-bottom: 30px;">&#2547; ' + fmt(grandPaid) + '</div>'
                        + '</td>'
                        + '<td style="padding:14px 16px 30px;text-align:center;">'
                            + '<div style="font-size:8.5px;text-transform:uppercase;letter-spacing:0.06em;color:#6b7280;margin-bottom:6px;">Remaining Due</div>'
                            + '<div style="font-size:16px;font-weight:700;color:#111827;padding-bottom: 30px;">&#2547; ' + fmt(grandDue) + '</div>'
                        + '</td>'
                        + '</tr>'
                        + '</table>';

                    const thS = 'padding:6px 7px;border:' + border + ';font-size:8.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.04em;color:#111827;background:#f3f4f6;white-space:nowrap;';
                    const tableHtml =
                        '<table style="width:100%;border-collapse:collapse;">'
                        + '<thead><tr>'
                        + '<th style="' + thS + 'text-align:center;width:28px;">SL</th>'
                        + '<th style="' + thS + 'width:70px;">Pay Date</th>'
                        + '<th style="' + thS + 'width:68px;">Method</th>'
                        + '<th style="' + thS + 'text-align:center;width:60px;">Status</th>'
                        + '<th style="' + thS + '">Fee Type</th>'
                        + '<th style="' + thS + '">Fee Name</th>'
                        + '<th style="' + thS + 'text-align:right;width:72px;">Total Fee</th>'
                        + '<th style="' + thS + 'text-align:right;width:64px;">Paid</th>'
                        + '<th style="' + thS + 'text-align:right;width:64px;">Due</th>'
                        + '<th style="' + thS + 'text-align:center;width:52px;">Overdue</th>'
                        + '</tr></thead>'
                        + '<tbody>' + rows + '</tbody>'
                        + tfoot
                        + '</table>';

                    const footerHtml =
                        '<div style="margin-top:20px;padding-top:8px;border-top:1px solid #d1d5db;display:flex;justify-content:space-between;font-size:8px;color:#6b7280;">'
                        + '<span>' + schoolName + ' — Confidential Payment Record</span>'
                        + '<span>Page 1 | ' + genDate + '</span>'
                        + '</div>';

                    wrap.innerHTML = headerHtml + titleHtml + summaryHtml + tableHtml + footerHtml;
                    document.body.appendChild(wrap);

                    const canvas = await html2canvas(wrap, {
                        scale: 2,
                        useCORS: true,
                        backgroundColor: '#ffffff',
                        width: W,
                        height: wrap.scrollHeight,
                        windowWidth: W,
                        logging: false,
                        onclone: function(doc) {
                            const el = doc.getElementById('__rpt__');
                            if (el) { el.style.left = '0'; el.style.position = 'relative'; }
                        }
                    });

                    document.body.removeChild(wrap);

                    const { jsPDF } = window.jspdf;
                    const pdf      = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
                    const pageW    = pdf.internal.pageSize.getWidth();
                    const pageH    = pdf.internal.pageSize.getHeight();
                    const imgData  = canvas.toDataURL('image/png');
                    const imgW     = canvas.width;
                    const imgH     = canvas.height;
                    const ratio    = pageW / imgW;
                    const scaledH  = imgH * ratio;

                    if (scaledH <= pageH) {
                        pdf.addImage(imgData, 'PNG', 0, 0, pageW, scaledH);
                    } else {
                        const pxPerPage = Math.floor(pageH / ratio);
                        let   yOffset   = 0;
                        while (yOffset < imgH) {
                            const sliceH = Math.min(pxPerPage, imgH - yOffset);
                            const slice = document.createElement('canvas');
                            slice.width  = imgW;
                            slice.height = sliceH;
                            slice.getContext('2d').drawImage(canvas, 0, yOffset, imgW, sliceH, 0, 0, imgW, sliceH);
                            const sliceData = slice.toDataURL('image/png');
                            const sliceMmH  = sliceH * ratio;
                            if (yOffset > 0) pdf.addPage();
                            pdf.addImage(sliceData, 'PNG', 0, 0, pageW, sliceMmH);
                            yOffset += pxPerPage;
                        }
                    }

                    const pad2    = n => String(n).padStart(2, '0');
                    const fname   = 'payment_report_' + (studentObj ? studentObj.student_id_number : studentId)
                                  + '_' + now.getFullYear() + pad2(now.getMonth() + 1) + pad2(now.getDate()) + '.pdf';
                    pdf.save(fname);

                    Swal.close();
                    Toastify({ text: 'Report downloaded', style: { background: '#10b981' } }).showToast();
                } catch (err) {
                    console.error('Canvas export error:', err);
                    Swal.fire('Error', 'Could not generate the report. Please try again.', 'error');
                }
            });

            // Header buttons
            document.getElementById('btnFilter')?.addEventListener('click', function() {
                document.getElementById('filterModal')?.classList.remove('hidden');
            });

            document.getElementById('openCollectionModalBtn')?.addEventListener('click', function() {
                openPaymentModal();
            });
        });

        // --- Filter Logic ---
        function setupFilterListeners() {
            populateDropdown('classFilterMenu', allClasses, 'id', 'class_name');

            document.getElementById('classFilter').addEventListener('change', function() {
                const selectedClass = this.value;
                setDropdownValue('groupFilter', '', 'Select Group');
                setDropdownValue('sectionFilter', '', 'Select Section');
                setDropdownValue('sessionFilter', '', 'Select Session');
                setDropdownValue('studentFilter', '', 'Select Student');
                populateDropdown('groupFilterMenu', [], 'id', 'group_name');
                populateDropdown('sectionFilterMenu', [], 'id', 'section_name');
                populateDropdown('sessionFilterMenu', [], 'id', 'session_year');
                populateDropdown('studentFilterMenu', [], 'id', 'student_name');
                if (selectedClass) {
                    const filteredStudents = allStudents.filter(s => String(s.class_id) === String(selectedClass));
                    const groupMap = new Map();
                    filteredStudents.forEach(s => {
                        if (s.group_id != null && s.group_name) groupMap.set(String(s.group_id), { id: s.group_id, group_name: s.group_name });
                    });
                    populateDropdown('groupFilterMenu', [...groupMap.values()], 'id', 'group_name');
                }
            });

            document.getElementById('groupFilter').addEventListener('change', function() {
                const selectedClass = document.getElementById('classFilter').value;
                const selectedGroup = this.value;
                setDropdownValue('sectionFilter', '', 'Select Section');
                setDropdownValue('sessionFilter', '', 'Select Session');
                setDropdownValue('studentFilter', '', 'Select Student');
                populateDropdown('sectionFilterMenu', [], 'id', 'section_name');
                populateDropdown('sessionFilterMenu', [], 'id', 'session_year');
                populateDropdown('studentFilterMenu', [], 'id', 'student_name');
                if (selectedClass && selectedGroup) {
                    const filteredStudents = allStudents.filter(s =>
                        String(s.class_id) === String(selectedClass) && String(s.group_id) === String(selectedGroup)
                    );
                    const sectionMap = new Map();
                    filteredStudents.forEach(s => {
                        if (s.section_id != null && s.section_name) sectionMap.set(String(s.section_id), { id: s.section_id, section_name: s.section_name });
                    });
                    populateDropdown('sectionFilterMenu', [...sectionMap.values()], 'id', 'section_name');
                }
            });

            document.getElementById('sectionFilter').addEventListener('change', function() {
                const selectedClass = document.getElementById('classFilter').value;
                const selectedGroup = document.getElementById('groupFilter').value;
                const selectedSection = this.value;
                setDropdownValue('sessionFilter', '', 'Select Session');
                setDropdownValue('studentFilter', '', 'Select Student');
                populateDropdown('sessionFilterMenu', [], 'id', 'session_year');
                populateDropdown('studentFilterMenu', [], 'id', 'student_name');
                if (selectedClass && selectedGroup && selectedSection) {
                    const filteredStudents = allStudents.filter(s =>
                        String(s.class_id) === String(selectedClass) &&
                        String(s.group_id) === String(selectedGroup) &&
                        String(s.section_id) === String(selectedSection)
                    );
                    const sessionMap = new Map();
                    filteredStudents.forEach(s => {
                        if (s.session_id != null && s.session_year) sessionMap.set(String(s.session_id), { id: s.session_id, session_year: s.session_year });
                    });
                    populateDropdown('sessionFilterMenu', [...sessionMap.values()], 'id', 'session_year');
                }
            });

            document.getElementById('sessionFilter').addEventListener('change', function() {
                const selectedClass = document.getElementById('classFilter').value;
                const selectedGroup = document.getElementById('groupFilter').value;
                const selectedSection = document.getElementById('sectionFilter').value;
                const selectedSession = this.value;
                setDropdownValue('studentFilter', '', 'Select Student');
                populateDropdown('studentFilterMenu', [], 'id', 'student_name');
                if (selectedClass && selectedGroup && selectedSection && selectedSession) {
                    const sortedStudents = allStudents.filter(s =>
                        String(s.class_id) === String(selectedClass) &&
                        String(s.group_id) === String(selectedGroup) &&
                        String(s.section_id) === String(selectedSection) &&
                        String(s.session_id) === String(selectedSession)
                    ).sort((a, b) => a.student_name.localeCompare(b.student_name));
                    const items = sortedStudents.map(s => ({ id: s.id, student_name: s.student_id_number + ' - ' + s.student_name }));
                    populateDropdown('studentFilterMenu', items, 'id', 'student_name');
                }
            });

            document.getElementById('applyFilter').onclick = () => {
                updateActiveFilters();
                fetchPayments(1);
                document.getElementById('filterModal').classList.add('hidden');
            };

            document.getElementById('resetFilter').onclick = () => {
                setDropdownValue('classFilter', '', 'Select Class');
                setDropdownValue('groupFilter', '', 'Select Group');
                setDropdownValue('sectionFilter', '', 'Select Section');
                setDropdownValue('sessionFilter', '', 'Select Session');
                setDropdownValue('studentFilter', '', 'Select Student');
                populateDropdown('groupFilterMenu', [], 'id', 'group_name');
                populateDropdown('sectionFilterMenu', [], 'id', 'section_name');
                populateDropdown('sessionFilterMenu', [], 'id', 'session_year');
                populateDropdown('studentFilterMenu', [], 'id', 'student_name');
                updateActiveFilters();
                fetchPayments(1);
                document.getElementById('filterModal').classList.add('hidden');
            };
        }

        function updateActiveFilters() {
            activeFilters.class = document.getElementById('classFilter').value;
            activeFilters.group = document.getElementById('groupFilter').value;
            activeFilters.section = document.getElementById('sectionFilter').value;
            activeFilters.session = document.getElementById('sessionFilter').value;
            activeFilters.student = document.getElementById('studentFilter').value;
        }

        // --- Modal Logic ---
        async function openPaymentModal() {
            document.getElementById('paymentForm').reset();
            document.getElementById('paymentModal').classList.remove('hidden');
            document.getElementById('modalTitle').innerText = "Collect Fee";
            populateDropdown('fees_typeMenu', [], 'id', 'fees_type');
            populateDropdown('fee_nameMenu', [], 'id', 'fee_name');
            document.getElementById('feeMonthGridWrap').classList.add('hidden');
            document.getElementById('feePaymentSection').classList.add('hidden');
            document.getElementById('feeStudentInfo').classList.add('hidden');
            document.getElementById('for_month').value = '';
            document.getElementById('is_advance').value = '0';
            editId = null;
            await loadStudents();
            feePopulateCascades();
        }

        function closeModal() {
            document.getElementById('paymentModal').classList.add('hidden');
            editId = null;
            document.getElementById('paymentForm').reset();
        }

        // --- Data Loading ---
        async function loadStudents() {
            try {
                const res = await axios.get('/api/school/students?all=true');
                allStudents = Array.isArray(res.data) ? res.data : res.data.data;
                const classList = [...new Set(allStudents.map(s => s.class_name))].filter(Boolean);
            } catch (e) {
                console.error("Students retrieval failed", e);
            }
        }

        async function loadClasses() {
            try {
                const res = await axios.get('/api/classes');
                const classesData = Array.isArray(res.data) ? res.data : res.data.data;
                allClasses = classesData
                    .map(c => ({ id: c.id, class_name: c.class_name }))
                    .filter(c => c.class_name);
                allClasses.sort((a, b) => {
                    const numA = parseInt(String(a.class_name).replace(/\D/g, ''), 10) || 0;
                    const numB = parseInt(String(b.class_name).replace(/\D/g, ''), 10) || 0;
                    return numA - numB;
                });
            } catch (e) {
                console.error("Classes retrieval failed", e);
            }
        }

        async function findStudentByCascade(student) {
            document.getElementById('feeStudentInfo').classList.remove('hidden');
            document.getElementById('admission_student_id').value = student.id;
            document.getElementById('feeDispName').textContent = student.student_name;
            document.getElementById('feeDispClass').textContent = student.class_name || 'N/A';
            document.getElementById('feeDispGroup').textContent = student.group_name || 'N/A';
            document.getElementById('feeDispSection').textContent = student.section_name || 'N/A';

            const classId = student.school_class?.id || student.class;
            const sessionId = student.school_session?.id || student.session;

            if (classId && sessionId) {
                await loadFilteredFees(classId, sessionId);
                await loadAndRenderMonthGrid(student.id);
            }
        }

        async function loadAndRenderMonthGrid(studentId) {
            const wrap = document.getElementById('feeMonthGridWrap');
            const grid = document.getElementById('feeMonthGrid');
            const statusEl = document.getElementById('feeClearanceStatus');

            try {
                const res = await axios.get(`/api/students/${studentId}/payment-months`);
                const data = res.data;
                wrap.classList.remove('hidden');
                grid.innerHTML = '';

                data.months.forEach(m => {
                    const cell = document.createElement('div');
                    cell.className = 'relative border text-center px-1 py-2 cursor-pointer transition-all text-[10px] leading-tight select-none';

                    switch (m.status) {
                        case 'paid':    cell.className += ' bg-green-100 border-green-300 text-green-800'; break;
                        case 'partial': cell.className += ' bg-blue-50 border-blue-300 text-blue-700'; break;
                        case 'overdue': cell.className += ' bg-red-100 border-red-300 text-red-800 font-bold'; break;
                        case 'current': cell.className += ' bg-yellow-50 border-yellow-400 text-yellow-800 font-bold ring-2 ring-yellow-400'; break;
                        case 'advance': cell.className += ' bg-teal-100 border-teal-300 text-teal-800'; break;
                        case 'locked':  cell.className += ' bg-gray-100 border-gray-200 text-gray-400 cursor-not-allowed'; break;
                        case 'no_fees': cell.className += ' bg-white border-gray-200 text-gray-400'; break;
                        default:        cell.className += ' bg-white border-gray-200 text-gray-600';
                    }

                    const shortName = m.month_name.substring(0, 3);
                    const amountText = m.status === 'locked' || m.status === 'no_fees' ? '' : `${m.due > 0 ? '৳' + m.due : '✓'}`;

                    cell.innerHTML = `<div class="font-semibold">${shortName}</div><div class="text-[9px]">${amountText}</div>`;

                    cell.addEventListener('click', function() {
                        if (m.status === 'locked') {
                            Swal.fire('Locked', 'Advance payment not available until dues are cleared.', 'info');
                            return;
                        }
                        if (m.status === 'no_fees') {
                            Swal.fire('No Fees', 'No fee configuration found for this month.', 'info');
                            return;
                        }
                        if (m.status === 'paid') {
                            Swal.fire('Already Paid', 'This month has been fully paid.', 'info');
                            return;
                        }

                        document.getElementById('for_month').value = m.year_month;
                        document.getElementById('is_advance').value = m.is_future ? '1' : '0';
                        document.getElementById('feePaymentSection').classList.remove('hidden');

                        if (m.is_future) {
                            const feeTypeVal = document.getElementById('fees_type').value;
                            const filteredTypes = currentFeesList
                                .filter(f => ['Food', 'Tuition'].includes(f.fee_type_name))
                                .map(f => f.fee_type_name);
                            const uniqueTypes = [...new Set(filteredTypes)];
                            populateDropdown('fees_typeMenu', uniqueTypes.map(t => ({ id: t, fees_type: t })), 'id', 'fees_type');
                            if (!['Food', 'Tuition'].includes(feeTypeVal)) {
                                setDropdownValue('fees_type', '', 'Select Fees Type');
                                setDropdownValue('fee_name', '', 'Select Fee Name');
                                populateDropdown('fee_nameMenu', [], 'id', 'fee_name');
                                document.getElementById('total_payable').value = '';
                                document.getElementById('payable_due').value = '';
                                document.getElementById('payable_due').removeAttribute('data-original-due');
                            }
                        } else {
                            const allTypes = [...new Set(currentFeesList.map(f => f.fee_type_name))];
                            populateDropdown('fees_typeMenu', allTypes.map(t => ({ id: t, fees_type: t })), 'id', 'fees_type');
                        }

                        grid.querySelectorAll('.ring-2').forEach(el => el.classList.remove('ring-2'));
                        this.classList.add('ring-2', 'ring-blue-500');
                    });

                    grid.appendChild(cell);
                });

                if (data.has_due) {
                    statusEl.className = 'text-[9px] font-medium text-red-500';
                    statusEl.textContent = '⚠ Due pending';
                } else {
                    statusEl.className = 'text-[9px] font-medium text-green-600';
                    statusEl.textContent = '✓ All cleared';
                }
                statusEl.classList.remove('hidden');

                const currentMonthData = data.months.find(m => m.is_current);
                if (currentMonthData) {
                    const cells = grid.children;
                    for (let i = 0; i < data.months.length; i++) {
                        if (data.months[i].year_month === currentMonthData.year_month) {
                            cells[i]?.click();
                            break;
                        }
                    }
                }
            } catch (e) {
                console.error('Month grid load error:', e);
                wrap.classList.add('hidden');
            }
        }

        function feePopulateCascades() {
            populateDropdown('feeClassFilterMenu', allClasses, 'class_name', 'class_name');
            populateDropdown('feeGroupFilterMenu', [], 'id', 'group_name');
            populateDropdown('feeSectionFilterMenu', [], 'id', 'section_name');
            populateDropdown('feeSessionFilterMenu', [], 'id', 'session_year');
            populateDropdown('feeStudentFilterMenu', [], 'id', 'student_name');
            setDropdownValue('feeGroupFilter', '', 'Select Group');
            setDropdownValue('feeSectionFilter', '', 'Select Section');
            setDropdownValue('feeSessionFilter', '', 'Select Session');
            setDropdownValue('feeStudentFilter', '', 'Select Student');
        }

        function populateStaticMenu(menuId, options) {
            const menu = document.querySelector(`#${menuId}`);
            if (!menu) return;
            menu.innerHTML = '';
            options.forEach(({ value, label }) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight transition-colors hover:bg-slate-100 text-slate-800';
                btn.dataset.value = String(value);
                btn.textContent = label;
                btn.setAttribute('role', 'option');
                btn.setAttribute('aria-selected', 'false');
                btn.setAttribute('data-dropdown-select-option', '');
                btn.addEventListener('click', function() {
                    const root = menu.closest('[data-dropdown-select]');
                    const input = root.querySelector('[data-dropdown-select-input]');
                    const labelEl = root.querySelector('[data-dropdown-select-label]');
                    input.value = this.dataset.value || '';
                    labelEl.textContent = this.textContent.trim();
                    menu.querySelectorAll('[data-dropdown-select-option]').forEach(item => {
                        const sel = item === this;
                        item.classList.toggle('bg-slate-100', sel);
                        item.classList.toggle('text-slate-900', sel);
                        item.classList.toggle('text-slate-800', !sel);
                        item.setAttribute('aria-selected', String(sel));
                    });
                    menu.classList.add('hidden');
                    root.querySelector('[data-dropdown-select-button]')?.setAttribute('aria-expanded', 'false');
                    const icon = root.querySelector('[data-dropdown-select-button] i');
                    if (icon) icon.classList.remove('rotate-180');
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });
                menu.appendChild(btn);
            });
        }

        let currentFeesList = [];
        async function loadFilteredFees(classId, sessionId) {
            try {
                const res = await axios.get('/api/fee-templates', {
                    params: { class_id: classId, session_id: sessionId, all: true }
                });
                const fees = Array.isArray(res.data) ? res.data : res.data.data;
                currentFeesList = fees;

                const filteredTypes = [...new Set(fees.map(f => f.fee_type_name))];
                const items = filteredTypes.map(t => ({ id: t, fees_type: t }));
                populateDropdown('fees_typeMenu', items, 'id', 'fees_type');
            } catch (e) {
                console.error("Fee loading failed", e);
            }
        }

        document.getElementById('fees_type').addEventListener('change', function() {
            const selectedType = this.value;
            setDropdownValue('fee_name', '', 'Select Fee Name');
            populateDropdown('fee_nameMenu', [], 'id', 'fee_name');
            document.getElementById('total_payable').value = '';
            document.getElementById('payable_due').value = '';
            document.getElementById('payable_due').removeAttribute('data-original-due');

            const seenNames = new Set();
            const items = [];
            currentFeesList
                .filter(f => f.fee_type_name === selectedType)
                .forEach(fee => {
                    if (!seenNames.has(fee.fee_name)) {
                        items.push({ id: fee.fee_name, fee_name: fee.fee_name, amount: fee.amount });
                        seenNames.add(fee.fee_name);
                    }
                });
            populateDropdown('fee_nameMenu', items, 'id', 'fee_name');
            items.forEach(f => {
                const opt = document.querySelector('#fee_nameMenu [data-value="' + f.id + '"]');
                if (opt) opt.dataset.amount = f.amount;
            });
        });

        async function fetchTotalPayable() {
            const feeName = document.getElementById('fee_name').value;
            const feesType = document.getElementById('fees_type').value;
            const admissionId = document.getElementById('admission_student_id').value;

            if (!feeName || !feesType || !admissionId) return;

            try {
                const response = await axios.get('/api/school/payments/get-total-fee', {
                    params: { fees_type: feesType, fee_name: feeName, admission_id: admissionId }
                });

                const data = response.data;
                const totalField = document.getElementById('total_payable');
                totalField.value = data.total_payable;
                totalField.style.color = data.has_discount ? '#10b981' : 'inherit';

                const payableDueField = document.getElementById('payable_due');
                payableDueField.value = data.remaining_due;
                payableDueField.setAttribute('data-original-due', data.remaining_due);

                calculateDue();
            } catch (error) {
                console.error("Fee Fetch Error:", error);
                Swal.fire({ title: 'Error', text: 'Could not retrieve the fee amount. Please try again.', icon: 'error', confirmButtonColor: '#2563eb' });
            }
        }

        document.getElementById('fee_name').addEventListener('change', function() {
            fetchTotalPayable();
        });

        document.getElementById('type_amount').addEventListener('input', function() {
            const inputField = this;
            const originalDue = parseFloat(document.getElementById('payable_due').getAttribute('data-original-due'));
            if (isNaN(originalDue)) return;
            let typedAmount = parseFloat(inputField.value) || 0;
            if (typedAmount > originalDue) {
                typedAmount = originalDue;
                inputField.value = originalDue;
                inputField.style.border = '2px solid #ef4444';
            } else {
                inputField.style.border = '2px solid #10b981';
            }
            calculateDue();
        });

        function calculateDue() {
            const originalDue = parseFloat(document.getElementById('payable_due').getAttribute('data-original-due')) || 0;
            const typedAmount = parseFloat(document.getElementById('type_amount').value) || 0;
            let due = originalDue - typedAmount;
            if (due < 0) due = 0;
            document.getElementById('payable_due').value = due.toFixed(2);
            let status = 'unpaid';
            if (typedAmount > 0 && due > 0) status = 'partial';
            else if (due <= 0 && originalDue > 0) status = 'paid';
            return status;
        }

        // --- CRUD Operations ---
        async function fetchPayments(page = 1) {
            currentPage = page;
            const search = document.getElementById('paySearch')?.value || document.getElementById('paySearchMobile')?.value || '';

            try {
                const params = { page, search };
                if (activeFilters.class) params.class_id = activeFilters.class;
                if (activeFilters.group) params.group_id = activeFilters.group;
                if (activeFilters.section) params.section_id = activeFilters.section;
                if (activeFilters.session) params.session_id = activeFilters.session;
                if (activeFilters.student) params.student = activeFilters.student;

                const res = await axios.get('/api/school/payments', { params });
                const tbody = document.getElementById('paymentTableBody');
                tbody.innerHTML = '';

                // Server already applied the class/group/section/session/student filters.
                const rawData = res.data.data;
                const filteredData = rawData;

                if (filteredData.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="15" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No collections found.</td></tr>';
                    document.getElementById('paginationInfo').innerText = '0 of 0';
                    document.getElementById('paginationControls').innerHTML = '';
                    return;
                }

                filteredData.forEach((p, i) => {
                    const payDate = new Date(p.pay_date);
                    const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                    const formattedDate = payDate.getDate() + '-' + months[payDate.getMonth()] + '-' + payDate.getFullYear();

                    const className = p.student?.school_class?.class_name || 'N/A';
                    const groupName = p.student?.school_group?.group_name || 'N/A';
                    const sectionName = p.student?.school_section?.section_name || 'N/A';
                    const sessionYear = p.student?.school_session?.session_year || 'N/A';

                    const due = parseFloat(p.payable_due) || 0;
                    const dueDisplay = due > 0
                        ? `<span style="color:#ef4444;font-weight:700;">${due.toFixed(2)}</span>`
                        : `<span style="color:#10b981;font-weight:700;">0.00</span>`;

                    tbody.innerHTML += `
                    <tr>
                        <td class="h-8 border border-gray-300 px-3 text-center">${res.data.from + i}</td>
                        <td class="h-8 border border-gray-300 px-3">${p.student?.student_id_number || '---'}</td>
                        <td class="h-8 border border-gray-300 px-3">${p.student?.student_name || 'Unknown'}</td>
                        <td class="h-8 border border-gray-300 px-3">${className}</td>
                        <td class="h-8 border border-gray-300 px-3">${groupName}</td>
                        <td class="h-8 border border-gray-300 px-3">${sectionName}</td>
                        <td class="h-8 border border-gray-300 px-3">${sessionYear}</td>
                        <td class="h-8 border border-gray-300 px-3">${p.fees_type}</td>
                        <td class="h-8 border border-gray-300 px-3">${p.fee_name || '---'}</td>
                        <td class="h-8 border border-gray-300 px-3">${p.total_payable}</td>
                        <td class="h-8 border border-gray-300 px-3">${p.type_amount}</td>
                        <td class="h-8 border border-gray-300 px-3">${dueDisplay}</td>
                        <td class="h-8 border border-gray-300 px-3">${p.pay_method}</td>
                        <td class="h-8 border border-gray-300 px-3">${formattedDate}</td>
                        <td class="h-8 border border-gray-300 px-3 text-center">
                            <div class="flex h-6 w-full items-center justify-center -space-x-[3px]">
                                <button type="button" title="Edit" onclick="editPayment(${p.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-blue-600"><i class="far fa-edit text-xs"></i></button>
                                <button type="button" title="Delete" onclick="deletePayment(${p.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-red-600"><i class="far fa-trash-alt text-xs"></i></button>
                            </div>
                        </td>
                    </tr>`;
                });

                document.getElementById('paginationInfo').innerText = `${res.data.to || 0} of ${res.data.total || 0}`;
                renderPagination(res.data);
            } catch (e) {
                console.error("Fetch error", e);
            }
        }

        function renderPagination(data) {
            const wrap = document.getElementById('paginationControls');
            wrap.innerHTML = '';
            if (!data.links) return;
            data.links.forEach(link => {
                const activeClass = link.active ? 'active' : '';
                const disabled = !link.url ? 'disabled' : '';
                let label = link.label;
                if (label.includes('Previous')) label = '<i class="mdi mdi-chevron-left"></i>';
                else if (label.includes('Next')) label = '<i class="mdi mdi-chevron-right"></i>';

                const pageNum = link.url ? new URL(link.url).searchParams.get('page') : null;
                const btn = document.createElement('button');
                btn.className = `pagination-btn ${activeClass}`;
                btn.innerHTML = label;
                if (disabled) btn.disabled = true;
                if (pageNum) btn.onclick = () => fetchPayments(pageNum);
                wrap.appendChild(btn);
            });
        }

        async function editPayment(id) {
            try {
                const res = await axios.get(`/api/school/payments/${id}`);
                const p = res.data;
                editId = id;
                document.getElementById('paymentModal').classList.remove('hidden');
                document.getElementById('modalTitle').innerText = "Update Payment Record";
                document.getElementById('is_advance').value = '0';
                await loadStudents();

                document.getElementById('feeMonthGridWrap').classList.add('hidden');
                document.getElementById('feePaymentSection').classList.remove('hidden');

                document.getElementById('feeStudentInfo').classList.remove('hidden');
                document.getElementById('admission_student_id').value = p.admission_student_id;
                document.getElementById('feeDispName').textContent = p.student?.student_name || '';
                document.getElementById('feeDispClass').textContent = p.student?.school_class?.class_name || 'N/A';
                document.getElementById('feeDispGroup').textContent = p.student?.school_group?.group_name || 'N/A';
                document.getElementById('feeDispSection').textContent = p.student?.school_section?.section_name || 'N/A';

                const cId = p.student?.school_class?.id || p.student?.class;
                const sId = p.student?.school_session?.id || p.student?.session;
                if (cId && sId) await loadFilteredFees(cId, sId);
                setDropdownValue('fees_type', p.fees_type, p.fees_type);
                document.getElementById('fees_type').dispatchEvent(new Event('change'));
                setDropdownValue('fee_name', p.fee_name || '', p.fee_name || '');
                document.getElementById('total_payable').value = p.total_payable;
                document.getElementById('type_amount').value = p.type_amount;
                document.getElementById('payable_due').value = p.payable_due;
                document.getElementById('payable_due').setAttribute('data-original-due', parseFloat(p.payable_due) + parseFloat(p.type_amount));
                setDropdownValue('pay_method', p.pay_method, p.pay_method);
                document.getElementById('pay_date').value = p.pay_date;
            } catch (error) {
                Swal.fire('Error', 'Failed to fetch details.', 'error');
            }
        }

        async function deletePayment(id) {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: "Delete this payment?",
                icon: 'warning',
                showCancelButton: true
            });
            if (result.isConfirmed) {
                try {
                    await axios.delete(`/api/school/payments/${id}`);
                    fetchPayments(currentPage);
                    Toastify({ text: "Deleted", style: { background: "#ef4444" } }).showToast();
                } catch (err) {
                    Swal.fire('Error', 'Could not delete', 'error');
                }
            }
        }

        document.getElementById('paymentForm').onsubmit = async function(e) {
            e.preventDefault();
            const btn = document.getElementById('saveBtn');
            const spinner = document.getElementById('saveBtnSpinner');
            const btnText = document.getElementById('saveBtnText');

            btn.disabled = true;
            spinner.classList.remove('hidden');
            btnText.textContent = 'Saving...';

            const studentId = document.getElementById('admission_student_id').value;
            const feesType = document.getElementById('fees_type').value;
            const feeName = document.getElementById('fee_name').value;
            const amount = document.getElementById('type_amount').value;
            const payDate = document.getElementById('pay_date').value;
            const payMethod = document.getElementById('pay_method').value;
            const isAdvance = document.getElementById('is_advance').value === '1';
            const forMonth = document.getElementById('for_month').value;

            try {
                if (editId) {
                    const data = {
                        admission_student_id: studentId,
                        fees_type: feesType,
                        fee_name: feeName,
                        total_payable: document.getElementById('total_payable').value,
                        type_amount: amount,
                        payable_due: document.getElementById('payable_due').value,
                        pay_date: payDate,
                        pay_method: payMethod,
                        status: calculateDue()
                    };
                    await axios.put(`/api/school/payments/${editId}`, data);
                } else if (isAdvance) {
                    await axios.post('/api/school/payments/store-advance', {
                        admission_student_id: studentId,
                        fees_type: feesType,
                        fee_name: feeName,
                        amount: amount,
                        for_month: forMonth,
                        pay_date: payDate,
                        pay_method: payMethod,
                    });
                } else {
                    const data = {
                        admission_student_id: studentId,
                        fees_type: feesType,
                        fee_name: feeName,
                        total_payable: document.getElementById('total_payable').value,
                        type_amount: amount,
                        payable_due: document.getElementById('payable_due').value,
                        pay_date: payDate,
                        pay_method: payMethod,
                        for_month: forMonth,
                        status: calculateDue()
                    };
                    await axios.post('/api/school/payments', data);
                }

                const student = allStudents.find(s => s.id == studentId);
                if (student) {
                    document.getElementById('classFilter').value = student.class_name;
                    document.getElementById('groupFilter').value = student.group_name;
                    document.getElementById('sectionFilter').value = student.section_name;
                    document.getElementById('sessionFilter').value = student.session_year;
                    updateActiveFilters();
                }

                closeModal();
                fetchPayments(currentPage);
                Toastify({
                    text: editId ? "Record Updated" : isAdvance ? "Advance Payment Recorded" : "Payment Recorded",
                    style: { background: "#10b981" }
                }).showToast();
            } catch (err) {
                const msg = err.response?.data?.message || 'Check student/fee selection';
                Swal.fire('Error', msg, 'error');
            } finally {
                btn.disabled = false;
                spinner.classList.add('hidden');
                btnText.textContent = 'Save';
            }
        };

        // --- FEE MODAL CASCADE ---
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('feeClassFilter').addEventListener('change', function() {
                const cls = this.value;
                setDropdownValue('feeGroupFilter', '', 'All');
                setDropdownValue('feeSectionFilter', '', 'All');
                setDropdownValue('feeSessionFilter', '', 'All');
                setDropdownValue('feeStudentFilter', '', 'Select Student');
                populateDropdown('feeGroupFilterMenu', [], 'id', 'group_name');
                populateDropdown('feeSectionFilterMenu', [], 'id', 'section_name');
                populateDropdown('feeSessionFilterMenu', [], 'id', 'session_year');
                populateDropdown('feeStudentFilterMenu', [], 'id', 'student_name');
                document.getElementById('feeStudentInfo').classList.add('hidden');
                document.getElementById('feeMonthGridWrap').classList.add('hidden');
                document.getElementById('feePaymentSection').classList.add('hidden');
                if (!cls) return;
                const groups = [...new Set(allStudents.filter(s => s.class_name === cls).map(s => s.group_name))].filter(Boolean);
                populateDropdown('feeGroupFilterMenu', groups.map(g => ({ id: g, group_name: g })), 'id', 'group_name');
            });

            document.getElementById('feeGroupFilter').addEventListener('change', function() {
                const cls = document.getElementById('feeClassFilter').value;
                const grp = this.value;
                setDropdownValue('feeSectionFilter', '', 'All');
                setDropdownValue('feeSessionFilter', '', 'All');
                setDropdownValue('feeStudentFilter', '', 'Select Student');
                populateDropdown('feeSectionFilterMenu', [], 'id', 'section_name');
                populateDropdown('feeSessionFilterMenu', [], 'id', 'session_year');
                populateDropdown('feeStudentFilterMenu', [], 'id', 'student_name');
                document.getElementById('feeStudentInfo').classList.add('hidden');
                document.getElementById('feeMonthGridWrap').classList.add('hidden');
                document.getElementById('feePaymentSection').classList.add('hidden');
                if (!cls || !grp) return;
                const sections = [...new Set(allStudents.filter(s => s.class_name === cls && s.group_name === grp).map(s => s.section_name))].filter(Boolean);
                populateDropdown('feeSectionFilterMenu', sections.map(s => ({ id: s, section_name: s })), 'id', 'section_name');
            });

            document.getElementById('feeSectionFilter').addEventListener('change', function() {
                const cls = document.getElementById('feeClassFilter').value;
                const grp = document.getElementById('feeGroupFilter').value;
                const sec = this.value;
                setDropdownValue('feeSessionFilter', '', 'All');
                setDropdownValue('feeStudentFilter', '', 'Select Student');
                populateDropdown('feeSessionFilterMenu', [], 'id', 'session_year');
                populateDropdown('feeStudentFilterMenu', [], 'id', 'student_name');
                document.getElementById('feeStudentInfo').classList.add('hidden');
                document.getElementById('feeMonthGridWrap').classList.add('hidden');
                document.getElementById('feePaymentSection').classList.add('hidden');
                if (!cls || !grp || !sec) return;
                const sessions = [...new Set(allStudents.filter(s => s.class_name === cls && s.group_name === grp && s.section_name === sec).map(s => s.session_year))].filter(Boolean);
                populateDropdown('feeSessionFilterMenu', sessions.map(s => ({ id: s, session_year: s })), 'id', 'session_year');
            });

            document.getElementById('feeSessionFilter').addEventListener('change', function() {
                const cls = document.getElementById('feeClassFilter').value;
                const grp = document.getElementById('feeGroupFilter').value;
                const sec = document.getElementById('feeSectionFilter').value;
                const ses = this.value;
                setDropdownValue('feeStudentFilter', '', 'Select Student');
                populateDropdown('feeStudentFilterMenu', [], 'id', 'student_name');
                document.getElementById('feeStudentInfo').classList.add('hidden');
                document.getElementById('feeMonthGridWrap').classList.add('hidden');
                document.getElementById('feePaymentSection').classList.add('hidden');
                if (!ses) return;
                const filtered = allStudents.filter(s =>
                    (!cls || s.class_name === cls) &&
                    (!grp || s.group_name === grp) &&
                    (!sec || s.section_name === sec) &&
                    s.session_year === ses
                ).sort((a, b) => a.student_name.localeCompare(b.student_name));
                const items = filtered.map(s => ({ id: s.id, student_name: s.student_id_number + ' — ' + s.student_name }));
                populateDropdown('feeStudentFilterMenu', items, 'id', 'student_name');
            });

            document.getElementById('feeStudentFilter').addEventListener('change', function() {
                const id = this.value;
                document.getElementById('feeStudentInfo').classList.add('hidden');
                document.getElementById('feeMonthGridWrap').classList.add('hidden');
                document.getElementById('feePaymentSection').classList.add('hidden');
                if (!id) return;
                const student = allStudents.find(s => s.id == id);
                if (student) findStudentByCascade(student);
            });
        });

        // ==================== PAYMENT SLIP MODAL ====================
        (function() {
            const slipModal = document.getElementById('slipModal');
            const closeSlipModal = () => slipModal.classList.add('hidden');
            document.getElementById('closeSlipFooter')?.addEventListener('click', closeSlipModal);

            function resetSlipCascades() {
                setDropdownValue('slipClassFilter', '', 'All Classes');
                setDropdownValue('slipGroupFilter', '', 'All Groups');
                setDropdownValue('slipSectionFilter', '', 'All Sections');
                setDropdownValue('slipSessionFilter', '', 'All Sessions');
                setDropdownValue('slipStudentFilter', '', '— Select Student —');
                populateDropdown('slipGroupFilterMenu', [], 'id', 'group_name');
                populateDropdown('slipSectionFilterMenu', [], 'id', 'section_name');
                populateDropdown('slipSessionFilterMenu', [], 'id', 'session_year');
                populateDropdown('slipStudentFilterMenu', [], 'id', 'student_name');
            }

            document.getElementById('btnPaymentSlip').addEventListener('click', function() {
                populateDropdown('slipClassFilterMenu', allClasses, 'class_name', 'class_name');
                resetSlipCascades();
                document.getElementById('slipFromDate').value = '';
                document.getElementById('slipToDate').value = '';
                document.getElementById('slipStudentError').classList.add('hidden');
                slipModal.classList.remove('hidden');
            });

            document.getElementById('slipClassFilter').addEventListener('change', function() {
                const cls = this.value;
                setDropdownValue('slipGroupFilter', '', 'All Groups');
                setDropdownValue('slipSectionFilter', '', 'All Sections');
                setDropdownValue('slipSessionFilter', '', 'All Sessions');
                setDropdownValue('slipStudentFilter', '', '— Select Student —');
                populateDropdown('slipGroupFilterMenu', [], 'id', 'group_name');
                populateDropdown('slipSectionFilterMenu', [], 'id', 'section_name');
                populateDropdown('slipSessionFilterMenu', [], 'id', 'session_year');
                populateDropdown('slipStudentFilterMenu', [], 'id', 'student_name');
                document.getElementById('slipFromDate').value = '';
                document.getElementById('slipToDate').value = '';
                if (!cls) return;
                const groups = [...new Set(allStudents.filter(s => s.class_name === cls).map(s => s.group_name))].filter(Boolean);
                populateDropdown('slipGroupFilterMenu', groups.map(g => ({ id: g, group_name: g })), 'id', 'group_name');
            });

            document.getElementById('slipGroupFilter').addEventListener('change', function() {
                const cls = document.getElementById('slipClassFilter').value;
                const grp = this.value;
                setDropdownValue('slipSectionFilter', '', 'All Sections');
                setDropdownValue('slipSessionFilter', '', 'All Sessions');
                setDropdownValue('slipStudentFilter', '', '— Select Student —');
                populateDropdown('slipSectionFilterMenu', [], 'id', 'section_name');
                populateDropdown('slipSessionFilterMenu', [], 'id', 'session_year');
                populateDropdown('slipStudentFilterMenu', [], 'id', 'student_name');
                document.getElementById('slipFromDate').value = '';
                document.getElementById('slipToDate').value = '';
                if (!cls || !grp) return;
                const sections = [...new Set(allStudents.filter(s => s.class_name === cls && s.group_name === grp).map(s => s.section_name))].filter(Boolean);
                populateDropdown('slipSectionFilterMenu', sections.map(s => ({ id: s, section_name: s })), 'id', 'section_name');
            });

            document.getElementById('slipSectionFilter').addEventListener('change', function() {
                const cls = document.getElementById('slipClassFilter').value;
                const grp = document.getElementById('slipGroupFilter').value;
                const sec = this.value;
                setDropdownValue('slipSessionFilter', '', 'All Sessions');
                setDropdownValue('slipStudentFilter', '', '— Select Student —');
                populateDropdown('slipSessionFilterMenu', [], 'id', 'session_year');
                populateDropdown('slipStudentFilterMenu', [], 'id', 'student_name');
                document.getElementById('slipFromDate').value = '';
                document.getElementById('slipToDate').value = '';
                if (!cls || !grp || !sec) return;
                const sessions = [...new Set(allStudents.filter(s => s.class_name === cls && s.group_name === grp && s.section_name === sec).map(s => s.session_year))].filter(Boolean);
                populateDropdown('slipSessionFilterMenu', sessions.map(s => ({ id: s, session_year: s })), 'id', 'session_year');
            });

            document.getElementById('slipSessionFilter').addEventListener('change', async function() {
                const cls = document.getElementById('slipClassFilter').value;
                const grp = document.getElementById('slipGroupFilter').value;
                const sec = document.getElementById('slipSectionFilter').value;
                const ses = this.value;
                setDropdownValue('slipStudentFilter', '', '— Select Student —');
                populateDropdown('slipStudentFilterMenu', [], 'id', 'student_name');
                document.getElementById('slipFromDate').value = '';
                document.getElementById('slipToDate').value = '';
                if (!ses) return;

                try {
                    const matchedStudent = allStudents.find(s => s.session_year === ses &&
                        (!cls || s.class_name === cls) &&
                        (!grp || s.group_name === grp) &&
                        (!sec || s.section_name === sec));
                    const sessionId = matchedStudent?.session
                        || matchedStudent?.school_session?.id
                        || matchedStudent?.session_id;
                    if (sessionId) {
                        const res = await axios.get(`/api/school-sessions/${sessionId}/dates`);
                        if (res.data.start_date) document.getElementById('slipFromDate').value = res.data.start_date;
                        if (res.data.end_date) document.getElementById('slipToDate').value = res.data.end_date;
                    }
                } catch (e) {
                    console.warn('Could not fetch session dates', e);
                }

                const filtered = allStudents.filter(s =>
                    (!cls || s.class_name === cls) &&
                    (!grp || s.group_name === grp) &&
                    (!sec || s.section_name === sec) &&
                    s.session_year === ses
                ).sort((a, b) => a.student_name.localeCompare(b.student_name));
                const items = filtered.map(s => ({ id: s.id, student_name: s.student_id_number + ' — ' + s.student_name }));
                populateDropdown('slipStudentFilterMenu', items, 'id', 'student_name');
            });

            document.getElementById('slipStudentFilter').addEventListener('change', function() {
                if (this.value) {
                    document.getElementById('slipStudentError').classList.add('hidden');
                }
            });

            document.getElementById('slipShowPage').addEventListener('click', function() {
                const studentSel = document.getElementById('slipStudentFilter');
                if (!studentSel.value) {
                    document.getElementById('slipStudentError').classList.remove('hidden');
                    studentSel.focus();
                    return;
                }
                slipModal.classList.add('hidden');
                const params = new URLSearchParams();
                params.append('student_id', studentSel.value);
                const fromDate = document.getElementById('slipFromDate').value;
                const toDate = document.getElementById('slipToDate').value;
                if (fromDate) params.append('from_date', fromDate);
                if (toDate) params.append('to_date', toDate);
                window.location.href = `/school/payment/slip?${params.toString()}`;
            });
        })();

        // Restore handlers
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btnRestoreDesktop')?.addEventListener('click', function() {
                document.getElementById('paySearch').value = '';
                currentPage = 1;
                fetchPayments(1);
            });

            document.getElementById('btnRestoreMobile')?.addEventListener('click', function() {
                document.getElementById('paySearchMobile').value = '';
                document.getElementById('paySearch').value = '';
                currentPage = 1;
                fetchPayments(1);
            });

            document.getElementById('paySearch')?.addEventListener('input', function() {
                currentPage = 1;
                fetchPayments(1);
            });

            document.getElementById('paySearchMobile')?.addEventListener('input', function() {
                currentPage = 1;
                fetchPayments(1);
            });

            document.querySelectorAll('[role="dialog"]').forEach(dialog => {
                dialog.addEventListener('click', function(e) {
                    if (e.target === this) this.classList.add('hidden');
                });
            });
        });
    </script>

    @include('school.academic.class.partials.js.modal-open')
    @include('school.academic.group.partials.js.modal-open')
    @include('school.academic.section.partials.js.modal-open')
    @include('school.academic.session.partials.js.modal-open')
    @include('school.academic.class.partials.js.error-validation')
    @include('school.academic.group.partials.js.error-validation')
    @include('school.academic.section.partials.js.error-validation')
    @include('school.academic.session.partials.js.error-validation')
    @include('school.academic.class.partials.js.modal-submit')
    @include('school.academic.group.partials.js.modal-submit')
    @include('school.academic.section.partials.js.modal-submit')
    @include('school.academic.session.partials.js.modal-submit')
@endsection
