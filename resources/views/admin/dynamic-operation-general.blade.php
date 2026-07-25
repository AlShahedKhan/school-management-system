@extends('layouts.admin')

@section('title', 'General Operation')
@section('page-title', 'General Operation')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="min-h-screen bg-gray-50 p-4 md:p-6">
        <div class="mb-6 flex flex-col justify-between gap-4 border border-gray-100 bg-white p-4 shadow-sm md:flex-row md:items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">General Operation</h2>
                <p class="text-xs text-gray-500">Manage global brand settings, footer content, and dashboard assets.</p>
            </div>
        </div>

        <div class="op-card">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-700">Footer Badge Style</h3>
                    <p class="mt-1 text-xs text-gray-500">Manage footer trust badge order and styles. Badge labels are edited in Translation Operation.</p>
                </div>
                <button onclick="saveFooterContent()" class="inline-flex items-center justify-center bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i> Save Badges
                </button>
            </div>

            <div class="mt-5 space-y-6">
                <div>
                    <div class="flex items-center justify-between gap-3">
                        <label class="text-[10px] font-bold uppercase text-gray-500">Trust Badges</label>
                        <button id="add_footer_badge_button" onclick="addFooterBadge()" class="inline-flex items-center justify-center border border-gray-200 px-3 py-2 text-[11px] font-semibold text-gray-600 transition hover:bg-gray-50">
                            <i class="fas fa-plus mr-2"></i> Add Badge
                        </button>
                    </div>
                    <div id="footer_badges_list" class="mt-3 flex flex-col gap-3"></div>
                    <p class="mt-2 text-[11px] text-gray-400">Badge styles and order stay shared across languages. Maximum 6 badges.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:col-span-3 xl:grid-cols-5">
                @foreach ([
                    'brand_logo_light' => ['title' => 'Light Logo', 'size' => '(512 × 512) px'],
                    'brand_logo_dark' => ['title' => 'White/Dark Logo', 'size' => '(512 × 512) px'],
                    'brand_favicon' => ['title' => 'Favicon', 'size' => '(64 × 64) px'],
                    'school_dashboard_logo' => ['title' => 'Dashboard Logo', 'size' => '(512 × 512) px'],
                    'brand_banner' => ['title' => 'Landing Banner', 'size' => '(1080 × 240) px'],
                ] as $field => $info)
                    <div class="op-card mb-0">
                        <div class="mb-3 flex items-center justify-between">
                            <h3 class="text-xs font-bold uppercase text-gray-700">{{ $info['title'] }}</h3>
                            <span class="text-[10px] font-medium text-gray-400">{{ $info['size'] }}</span>
                        </div>
                        <div id="{{ $field }}_preview_container" class="relative mb-3 group"></div>
                        <div class="flex flex-col gap-2">
                            <input type="file" id="{{ $field }}_file" class="hidden" onchange="uploadSingle('{{ $field }}')">
                            <button onclick="document.getElementById('{{ $field }}_file').click()" class="w-full border border-dashed border-gray-300 py-2 text-xs font-medium text-gray-500 transition hover:bg-gray-50">
                                <i class="fas fa-upload mr-1"></i> Select File
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="op-card">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-bold uppercase text-gray-700">Dashboard Banners</h3>
                        <span class="text-[10px] font-medium text-gray-400">(2354 × 600) px</span>
                    </div>
                    <div>
                        <input type="file" id="school_banners_input" multiple class="hidden" onchange="uploadMultipleBanners()">
                        <button onclick="document.getElementById('school_banners_input').click()" class="flex h-8 w-8 items-center justify-center bg-indigo-600 text-white transition hover:bg-indigo-700">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>
                <div id="school_banner_list" class="custom-scrollbar flex max-h-[300px] flex-col gap-2 overflow-y-auto pr-1"></div>
            </div>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-[70]">
        <div class="w-full max-w-sm bg-white p-6 shadow-2xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-bold uppercase">Update Asset</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <input type="file" id="modal_file" class="mb-4 w-full border border-gray-200 p-2 text-sm">
            <input type="hidden" id="modal_field">
            <div class="flex justify-end gap-2">
                <button onclick="closeModal()" class="bg-gray-100 px-4 py-2 text-[10px] font-bold">CANCEL</button>
                <button onclick="confirmEdit()" class="bg-blue-600 px-4 py-2 text-[10px] font-bold text-white">SAVE CHANGES</button>
            </div>
        </div>
    </div>

    <script>
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let settings = {};

        function showToast(msg, type = 'success') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type === 'success' ? 'success' : 'error',
                title: msg,
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
            });
        }

        async function apiGet(url) {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data?.message || 'Request failed');
            }

            return data;
        }

        async function apiPost(url, payload, isFormData = false) {
            const headers = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token,
            };

            if (!isFormData) {
                headers['Content-Type'] = 'application/json';
            }

            const response = await fetch(url, {
                method: 'POST',
                headers,
                body: isFormData ? payload : JSON.stringify(payload),
                credentials: 'same-origin',
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data?.message || 'Request failed');
            }

            return data;
        }

        async function loadData() {
            try {
                settings = await apiGet('/api/dynamic-operation');

                renderUI();
            } catch (err) {
                console.error('Load failed', err);
                showToast('Could not load general settings', 'error');
            }
        }

        function renderUI() {
            const storageBase = '/storage/';
            const singles = ['brand_logo_light', 'brand_logo_dark', 'brand_favicon', 'school_dashboard_logo', 'brand_banner'];

            singles.forEach((field) => {
                const container = document.getElementById(`${field}_preview_container`);
                if (!container) {
                    return;
                }

                if (settings[field]) {
                    container.innerHTML = `
                        <div class="relative group border border-gray-100 p-1">
                            <img src="${storageBase + settings[field]}" class="preview-img">
                            <div class="asset-overlay">
                                <button onclick="openEdit('${field}')" class="flex h-8 w-8 items-center justify-center bg-white text-blue-600 shadow-sm hover:bg-blue-50">
                                    <i class="fas fa-pen text-xs"></i>
                                </button>
                                <button onclick="deleteSingle('${field}')" class="flex h-8 w-8 items-center justify-center bg-white text-red-600 shadow-sm hover:bg-red-50">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </div>
                    `;
                } else {
                    container.innerHTML = '<div class="preview-img flex items-center justify-center text-[10px] font-bold uppercase italic text-gray-300">No Image</div>';
                }
            });

            const bannerList = document.getElementById('school_banner_list');
            if (!bannerList) {
                return;
            }

            bannerList.innerHTML = '';

            if (settings.school_dashboard_banners && Array.isArray(settings.school_dashboard_banners)) {
                settings.school_dashboard_banners.forEach((path, index) => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center gap-3 border border-gray-100 p-2';
                    div.innerHTML = `
                        <img src="${storageBase + path}" class="h-12 w-20 flex-shrink-0 border border-gray-100 object-cover">
                        <span class="flex-1 truncate text-xs text-gray-400">Banner ${index + 1}</span>
                        <button onclick="confirmDeleteBanner(${index})" class="flex h-8 w-8 flex-shrink-0 items-center justify-center bg-red-50 text-red-500 transition hover:bg-red-100">
                            <i class="fas fa-trash-can text-xs"></i>
                        </button>
                    `;
                    bannerList.appendChild(div);
                });
            }

            renderFooterBadgeEditor();
        }

        function getFooterBadges() {
            if (Array.isArray(settings.footer_trust_badges_i18n)) {
                return settings.footer_trust_badges_i18n;
            }

            if (Array.isArray(settings.footer_trust_badges)) {
                return settings.footer_trust_badges.map((badge) => ({
                    label_en: badge.label || '',
                    label_bn: badge.label || '',
                    style: badge.style || 'success',
                }));
            }

            return [];
        }

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderBadgeStyleOptions(selected) {
            const styles = [
                { value: 'success', label: 'Success' },
                { value: 'neutral', label: 'Neutral' },
                { value: 'info', label: 'Info' },
            ];

            return styles.map((style) =>
                `<option value="${style.value}" ${selected === style.value ? 'selected' : ''}>${style.label}</option>`
            ).join('');
        }

        function renderFooterBadgeEditor() {
            const badgeList = document.getElementById('footer_badges_list');
            const addButton = document.getElementById('add_footer_badge_button');

            if (!badgeList || !addButton) {
                return;
            }

            const badges = getFooterBadges();
            badgeList.innerHTML = '';

            if (badges.length === 0) {
                const emptyState = document.createElement('div');
                emptyState.className = 'border border-dashed border-gray-200 px-4 py-5 text-center text-xs text-gray-400';
                emptyState.textContent = 'No custom badges yet. Add one to override the default footer badges.';
                badgeList.appendChild(emptyState);
            } else {
                badges.forEach((badge, index) => {
                    const row = document.createElement('div');
                    row.className = 'grid grid-cols-1 gap-3 border border-gray-100 p-3 xl:grid-cols-[minmax(0,1fr)_140px_44px]';
                    row.innerHTML = `
                        <div class="flex flex-col justify-center gap-1">
                            <span class="text-[10px] font-bold uppercase text-gray-500">Translation Key</span>
                            <span class="font-mono text-xs text-gray-600">public.footer.badges.${index}.label</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] font-bold uppercase text-gray-500">Style</label>
                            <select
                                onchange="updateFooterBadge(${index}, 'style', this.value)"
                                class="w-full border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-500"
                            >
                                ${renderBadgeStyleOptions(badge.style || 'success')}
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button onclick="removeFooterBadge(${index})" class="flex h-10 w-11 items-center justify-center border border-red-100 bg-red-50 text-red-500 transition hover:bg-red-100">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                    `;
                    badgeList.appendChild(row);
                });
            }

            addButton.disabled = badges.length >= 6;
            addButton.classList.toggle('opacity-50', badges.length >= 6);
            addButton.classList.toggle('cursor-not-allowed', badges.length >= 6);
        }

        function addFooterBadge() {
            const badges = getFooterBadges();
            if (badges.length >= 6) {
                return;
            }

            settings.footer_trust_badges_i18n = [...badges, { label_en: '', label_bn: '', style: 'success' }];
            renderFooterBadgeEditor();
        }

        function updateFooterBadge(index, key, value) {
            const badges = [...getFooterBadges()];
            if (!badges[index]) {
                return;
            }

            badges[index] = {
                ...badges[index],
                [key]: value,
            };

            settings.footer_trust_badges_i18n = badges;
        }

        function removeFooterBadge(index) {
            const badges = [...getFooterBadges()];
            badges.splice(index, 1);
            settings.footer_trust_badges_i18n = badges;
            renderFooterBadgeEditor();
        }

        function saveFooterContent() {
            const badges = getFooterBadges().map((badge) => ({
                label_en: (badge.label_en || '').trim(),
                label_bn: (badge.label_bn || '').trim(),
                style: badge.style || 'success',
            }));

            apiPost('/api/dynamic-operation/update-footer', {
                footer_trust_badges_i18n: badges,
            })
            .then((data) => {
                settings = data;
                renderFooterBadgeEditor();
                showToast('Footer badges saved');
            })
            .catch((err) => {
                showToast(err.message || 'Footer update failed', 'error');
            });
        }

        function uploadSingle(field) {
            const fileInput = document.getElementById(`${field}_file`);
            if (!fileInput.files[0]) {
                return;
            }

            const form = new FormData();
            form.append('image', fileInput.files[0]);
            form.append('field', field);

            apiPost('/api/dynamic-operation/upload-logo', form, true)
            .then((data) => {
                settings = data;
                renderUI();
                showToast('Asset uploaded');
                fileInput.value = '';
            })
            .catch((err) => {
                showToast(err.message || 'Asset upload failed', 'error');
            });
        }

        function uploadMultipleBanners() {
            const fileInput = document.getElementById('school_banners_input');
            const files = fileInput.files;

            if (files.length === 0) {
                return;
            }

            const form = new FormData();
            for (let i = 0; i < files.length; i++) {
                form.append('images[]', files[i]);
            }

            apiPost('/api/dynamic-operation/upload-school-banners', form, true)
            .then((data) => {
                settings = data;
                renderUI();
                showToast('Banners added');
                fileInput.value = '';
            })
            .catch((err) => {
                showToast(err.message || 'Banner upload failed', 'error');
            });
        }

        function deleteSingle(field) {
            Swal.fire({
                title: 'Delete Asset?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                customClass: {
                    popup: 'rounded-0',
                    confirmButton: 'bg-red-600 px-4 py-2 text-white text-xs mx-1',
                    cancelButton: 'bg-gray-200 px-4 py-2 text-gray-700 text-xs mx-1',
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    apiPost('/api/dynamic-operation/delete-image', {
                        field,
                    })
                    .then((data) => {
                        settings = data;
                        renderUI();
                        showToast('Asset removed');
                    })
                    .catch((err) => {
                        showToast(err.message || 'Asset delete failed', 'error');
                    });
                }
            });
        }

        function confirmDeleteBanner(index) {
            Swal.fire({
                title: 'Remove Banner?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Remove',
                customClass: {
                    popup: 'rounded-0',
                    confirmButton: 'bg-red-600 px-4 py-2 text-white text-xs mx-1',
                    cancelButton: 'bg-gray-200 px-4 py-2 text-gray-700 text-xs mx-1',
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    apiPost('/api/dynamic-operation/delete-school-banner', {
                        index,
                    })
                    .then((data) => {
                        settings = data;
                        renderUI();
                        showToast('Banner removed');
                    })
                    .catch((err) => {
                        showToast(err.message || 'Banner delete failed', 'error');
                    });
                }
            });
        }

        function openEdit(field) {
            document.getElementById('modal_field').value = field;
            document.getElementById('editModal').classList.replace('hidden', 'flex');
        }

        function closeModal() {
            document.getElementById('editModal').classList.replace('flex', 'hidden');
            document.getElementById('modal_file').value = '';
        }

        function confirmEdit() {
            const field = document.getElementById('modal_field').value;
            const file = document.getElementById('modal_file').files[0];

            if (!file) {
                showToast('Select a file', 'error');
                return;
            }

            const form = new FormData();
            form.append('image', file);
            form.append('field', field);

            apiPost('/api/dynamic-operation/upload-logo', form, true)
            .then((data) => {
                settings = data;
                renderUI();
                closeModal();
                showToast('Asset updated');
            })
            .catch((err) => {
                showToast(err.message || 'Asset update failed', 'error');
            });
        }

        loadData();
    </script>
@endsection
