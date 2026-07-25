@extends('layouts.admin')

@section('title', 'Home Operation')
@section('page-title', 'Home Operation')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="min-h-screen bg-gray-50 p-4 md:p-6">
        <div class="mb-6 flex flex-col justify-between gap-4 border border-gray-100 bg-white p-4 shadow-sm md:flex-row md:items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Home Operation</h2>
                <p class="text-xs text-gray-500">Manage the public home page hero, stats, and intro content.</p>
            </div>
            <button onclick="saveHomePageSettings()" class="inline-flex items-center justify-center bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i> Save Home Page
            </button>
        </div>

        <div class="op-card">
            <div class="space-y-6">
                <section class="border border-gray-100 bg-white p-4 md:p-5">
                    <div class="flex flex-col gap-1">
                        <h4 class="text-xs font-bold uppercase tracking-[0.18em] text-gray-600">Hero</h4>
                        <p class="text-[11px] text-gray-400">Control the public hero copy, dashboard preview text, colors, and live activity content.</p>
                    </div>

                    <div class="mt-5 grid grid-cols-1 gap-5">
                        <div class="grid grid-cols-1 gap-5 2xl:grid-cols-[minmax(0,1fr)_360px]">
                            <div class="border border-gray-100 bg-gray-50 p-4">
                                <div class="mb-4 border-b border-gray-100 pb-3">
                                    <h5 class="text-[11px] font-bold uppercase tracking-[0.16em] text-gray-500">Hero Actions</h5>
                                    <p class="mt-1 text-[10px] text-gray-400">Text labels are edited in Translation Operation. URLs and badge style stay here.</p>
                                </div>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    @foreach ([
                                        'hero_primary_cta_url' => 'Primary CTA URL',
                                        'hero_secondary_cta_url' => 'Secondary CTA URL',
                                    ] as $field => $label)
                                        <div class="flex flex-col gap-1">
                                            <label class="text-[10px] font-bold uppercase text-gray-500">{{ $label }}</label>
                                            <input type="text" id="home_{{ $field }}" class="border border-gray-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500">
                                        </div>
                                    @endforeach
                                    <div class="flex flex-col gap-1">
                                        <label class="text-[10px] font-bold uppercase text-gray-500">Status Badge Style</label>
                                        <select id="home_hero_status_badge_style" class="border border-gray-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500">
                                            <option value="success">Success</option>
                                            <option value="neutral">Neutral</option>
                                            <option value="info">Info</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border border-gray-100 bg-gray-50 p-4">
                            <div class="mb-4 border-b border-gray-100 pb-3">
                                <h5 class="text-[11px] font-bold uppercase tracking-[0.16em] text-gray-500">Hero Colors</h5>
                                <p class="mt-1 text-[11px] text-gray-400">Control the title highlight and Book a Demo button accent.</p>
                            </div>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                @foreach ([
                                    'hero_accent_color' => 'Hero Accent Color',
                                    'hero_accent_soft_color' => 'Hero Soft Accent Color',
                                    'hero_primary_button_color' => 'Primary Button Color',
                                    'hero_primary_button_hover_color' => 'Primary Button Hover Color',
                                ] as $field => $label)
                                    <div class="flex flex-col gap-1">
                                        <label class="text-[10px] font-bold uppercase text-gray-500">{{ $label }}</label>
                                        <div class="grid grid-cols-[48px_minmax(0,1fr)] border border-gray-200 bg-white transition focus-within:border-blue-500">
                                            <label
                                                for="home_{{ $field }}_picker"
                                                class="flex h-11 cursor-pointer items-center justify-center border-r border-gray-200 bg-gray-50"
                                                aria-label="{{ $label }} picker"
                                            >
                                                <input
                                                    type="color"
                                                    id="home_{{ $field }}_picker"
                                                    class="h-7 w-7 cursor-pointer border border-gray-300 bg-white p-0.5"
                                                    oninput="syncColorField('{{ $field }}', this.value, 'picker')"
                                                >
                                            </label>
                                            <input
                                                type="text"
                                                id="home_{{ $field }}"
                                                class="h-11 border-0 bg-white px-3 text-sm font-medium uppercase outline-none"
                                                placeholder="#2563EB"
                                                oninput="syncColorField('{{ $field }}', this.value, 'text')"
                                            >
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                            <div class="border border-gray-100 bg-gray-50 p-4">
                                <div class="flex items-center justify-between gap-3 border-b border-gray-100 pb-3">
                                    <div>
                                        <h5 class="text-[11px] font-bold uppercase tracking-[0.16em] text-gray-500">Hero Metric Cards</h5>
                                        <p class="mt-1 text-[10px] text-gray-400">Exactly 3 items on save.</p>
                                    </div>
                                    <button onclick="addHomeListItem('hero_metric_cards')" class="inline-flex items-center border border-gray-200 bg-white px-3 py-2 text-[11px] font-semibold text-gray-600 transition hover:bg-gray-50">
                                        <i class="fas fa-plus mr-2"></i> Add Metric
                                    </button>
                                </div>
                                <div id="home_metric_cards_list" class="mt-4 flex flex-col gap-3"></div>
                            </div>

                            <div class="border border-gray-100 bg-gray-50 p-4">
                                <div class="flex items-center justify-between gap-3 border-b border-gray-100 pb-3">
                                    <h5 class="text-[11px] font-bold uppercase tracking-[0.16em] text-gray-500">Hero Activity Items</h5>
                                    <button onclick="addHomeListItem('hero_activity_items')" class="inline-flex items-center border border-gray-200 bg-white px-3 py-2 text-[11px] font-semibold text-gray-600 transition hover:bg-gray-50">
                                        <i class="fas fa-plus mr-2"></i> Add Activity
                                    </button>
                                </div>
                                <div id="home_activity_items_list" class="mt-4 flex flex-col gap-3"></div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>

    <script>
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let homePageSettings = {};
        const HOME_TONE_OPTIONS = [
            { value: 'primary', label: 'Primary' },
            { value: 'success', label: 'Success' },
            { value: 'warning', label: 'Warning' },
            { value: 'info', label: 'Info' },
        ];
        const HOME_FIELD_IDS = [
            'hero_accent_color',
            'hero_accent_soft_color',
            'hero_primary_button_color',
            'hero_primary_button_hover_color',
            'hero_primary_cta_url',
            'hero_secondary_cta_url',
            'hero_status_badge_style',
        ];

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

        async function apiPost(url, payload) {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify(payload),
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
                homePageSettings = await apiGet('/api/home-page-settings');
                hydrateHomePageInputs();
                renderHomePageEditor();
            } catch (err) {
                console.error('Load failed', err);
                showToast('Could not load home settings', 'error');
            }
        }

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function hydrateHomePageInputs() {
            HOME_FIELD_IDS.forEach((field) => {
                const element = document.getElementById(`home_${field}`);
                if (element) {
                    element.value = homePageSettings[field] || '';
                }

                const picker = document.getElementById(`home_${field}_picker`);
                if (picker && homePageSettings[field]) {
                    picker.value = homePageSettings[field];
                }
            });
        }

        function syncColorField(field, value, source) {
            const textInput = document.getElementById(`home_${field}`);
            const pickerInput = document.getElementById(`home_${field}_picker`);

            if (source !== 'text' && textInput) {
                textInput.value = value;
            }

            if (source !== 'picker' && pickerInput && /^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(value)) {
                pickerInput.value = value;
            }
        }

        function renderHomePageEditor() {
            renderHomeList('hero_metric_cards');
            renderHomeList('hero_activity_items');
        }

        function getHomeListDefaults(type) {
            if (type === 'hero_metric_cards') {
                return { label: '', value: '', tone: 'primary' };
            }

            if (type === 'hero_activity_items') {
                return { text: '', meta: '', tone: 'primary' };
            }

            return { value: '', label: '' };
        }

        function getHomeList(type) {
            return Array.isArray(homePageSettings[type]) ? homePageSettings[type] : [];
        }

        function addHomeListItem(type) {
            const current = [...getHomeList(type)];
            const limits = {
                hero_metric_cards: 3,
                hero_activity_items: 4,
            };

            if (limits[type] && current.length >= limits[type]) {
                showToast(`Maximum ${limits[type]} items allowed`, 'error');
                return;
            }

            current.push(getHomeListDefaults(type));
            homePageSettings[type] = current;
            renderHomeList(type);
        }

        function updateHomeListItem(type, index, field, value) {
            const current = [...getHomeList(type)];
            if (!current[index]) {
                return;
            }

            current[index] = {
                ...current[index],
                [field]: value,
            };

            homePageSettings[type] = current;
        }

        function removeHomeListItem(type, index) {
            const current = [...getHomeList(type)];
            current.splice(index, 1);
            homePageSettings[type] = current;
            renderHomeList(type);
        }

        function moveHomeListItem(type, index, direction) {
            const current = [...getHomeList(type)];
            const targetIndex = index + direction;

            if (!current[index] || !current[targetIndex]) {
                return;
            }

            [current[index], current[targetIndex]] = [current[targetIndex], current[index]];
            homePageSettings[type] = current;
            renderHomeList(type);
        }

        function renderSelectOptions(options, selected) {
            return options.map((option) =>
                `<option value="${option.value}" ${option.value === selected ? 'selected' : ''}>${option.label}</option>`
            ).join('');
        }

        function renderMoveControls(type, index, total, includeDelete) {
            return `
                <div class="flex items-start gap-2">
                    <button type="button" onclick="moveHomeListItem('${type}', ${index}, -1)" class="h-10 w-10 border border-gray-200 text-gray-500 transition hover:bg-gray-50 ${index === 0 ? 'pointer-events-none opacity-40' : ''}">
                        <i class="fas fa-arrow-up text-xs"></i>
                    </button>
                    <button type="button" onclick="moveHomeListItem('${type}', ${index}, 1)" class="h-10 w-10 border border-gray-200 text-gray-500 transition hover:bg-gray-50 ${index === total - 1 ? 'pointer-events-none opacity-40' : ''}">
                        <i class="fas fa-arrow-down text-xs"></i>
                    </button>
                    ${includeDelete ? `
                        <button type="button" onclick="removeHomeListItem('${type}', ${index})" class="h-10 w-10 border border-red-100 bg-red-50 text-red-500 transition hover:bg-red-100">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    ` : ''}
                </div>
            `;
        }

        function renderHomeList(type) {
            const containerMap = {
                hero_metric_cards: 'home_metric_cards_list',
                hero_activity_items: 'home_activity_items_list',
            };

            const container = document.getElementById(containerMap[type]);
            if (!container) {
                return;
            }

            const items = getHomeList(type);
            container.innerHTML = '';

            items.forEach((item, index) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'border border-gray-100 p-3';

                let fieldsHtml = '';

                if (type === 'hero_metric_cards') {
                    fieldsHtml = `
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-[minmax(0,1fr)_140px_auto]">
                            <div class="flex flex-col justify-center gap-1 border border-gray-100 bg-white px-3 py-2">
                                <span class="font-mono text-[11px] text-gray-500">public.home.hero.metrics.${index}.label</span>
                                <span class="truncate text-xs text-gray-700">${escapeHtml(item.label || '')}</span>
                            </div>
                            <select onchange="updateHomeListItem('${type}', ${index}, 'tone', this.value)" class="border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-500">
                                ${renderSelectOptions(HOME_TONE_OPTIONS, item.tone || 'primary')}
                            </select>
                            ${renderMoveControls(type, index, items.length, true)}
                        </div>
                    `;
                } else if (type === 'hero_activity_items') {
                    fieldsHtml = `
                        <div class="grid grid-cols-1 gap-3 lg:grid-cols-[minmax(0,1fr)_140px_auto]">
                            <div class="flex flex-col justify-center gap-1 border border-gray-100 bg-white px-3 py-2">
                                <span class="font-mono text-[11px] text-gray-500">public.home.hero.activities.${index}.text</span>
                                <span class="truncate text-xs text-gray-700">${escapeHtml(item.text || '')}</span>
                            </div>
                            <select onchange="updateHomeListItem('${type}', ${index}, 'tone', this.value)" class="border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-500">
                                ${renderSelectOptions(HOME_TONE_OPTIONS, item.tone || 'primary')}
                            </select>
                            ${renderMoveControls(type, index, items.length, true)}
                        </div>
                    `;
                }

                wrapper.innerHTML = fieldsHtml;
                container.appendChild(wrapper);
            });
        }

        function collectHomePagePayload() {
            const payload = {};

            HOME_FIELD_IDS.forEach((field) => {
                const element = document.getElementById(`home_${field}`);
                payload[field] = element ? element.value : '';
            });

            payload.hero_metric_cards = getHomeList('hero_metric_cards').map((item) => ({
                label: (item.label || '').trim(),
                value: (item.value || '').trim(),
                tone: item.tone || 'primary',
            }));

            payload.hero_activity_items = getHomeList('hero_activity_items').map((item) => ({
                text: (item.text || '').trim(),
                meta: (item.meta || '').trim(),
                tone: item.tone || 'primary',
            }));

            return payload;
        }

        function saveHomePageSettings() {
            apiPost('/api/home-page-settings', collectHomePagePayload())
            .then((data) => {
                homePageSettings = data;
                hydrateHomePageInputs();
                renderHomePageEditor();
                showToast('Home page content saved');
            })
            .catch((err) => {
                showToast(err.message || 'Home page save failed', 'error');
            });
        }

        loadData();
    </script>
@endsection
