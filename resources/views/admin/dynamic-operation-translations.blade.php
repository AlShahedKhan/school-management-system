@extends('layouts.admin')

@section('title', 'Translation Operation')
@section('page-title', 'Translation Operation')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="min-h-screen bg-gray-50 p-4 md:p-6">
        <div class="mb-6 flex flex-col justify-between gap-4 border border-gray-100 bg-white p-4 shadow-sm md:flex-row md:items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Translation Operation</h2>
                <p class="text-xs text-gray-500">Manage public site English and Bangla UI text. Empty values fall back to Laravel language files.</p>
            </div>
            <button
                type="button"
                onclick="openTranslationModal()"
                class="inline-flex items-center justify-center bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700"
            >
                <i class="fas fa-plus mr-2"></i> Add Translation
            </button>
        </div>

        <div class="border border-gray-100 bg-white p-4 shadow-sm">
            <div class="grid grid-cols-1 gap-3 lg:grid-cols-[minmax(0,1fr)_220px_auto]">
                <input
                    id="translationSearch"
                    type="search"
                    class="border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-500"
                    placeholder="Search key, English, Bangla, or description..."
                >
                <select
                    id="translationGroup"
                    class="border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-500"
                >
                    <option value="">All Groups</option>
                </select>
                <button
                    id="translationFilterButton"
                    type="button"
                    class="inline-flex items-center justify-center border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-600 transition hover:border-blue-200 hover:text-blue-600"
                >
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
            </div>
        </div>

        <div class="mt-6 overflow-hidden border border-gray-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-500">Key</th>
                            <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-500">English</th>
                            <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-500">Bangla</th>
                            <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-4 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody id="translationTable" class="divide-y divide-gray-100"></tbody>
                </table>
            </div>

            <div id="translationPagination" class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 px-4 py-3"></div>
        </div>
    </div>

    <div id="translationModal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/50 px-4">
        <div class="w-full max-w-2xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <div>
                    <h3 id="translationModalTitle" class="text-sm font-bold uppercase tracking-wider text-gray-800">Add Translation</h3>
                    <p class="mt-1 text-xs text-gray-500">Use dot notation keys such as public.nav.home.</p>
                </div>
                <button type="button" onclick="closeTranslationModal()" class="text-gray-400 transition hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="translationForm" class="space-y-4 p-5" onsubmit="saveTranslation(event)">
                <input type="hidden" id="translationId">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold uppercase text-gray-500">Key</label>
                        <input id="translationKey" type="text" class="border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-500" placeholder="public.nav.home" required>
                        <p id="translationKeyHelp" class="text-[11px] text-gray-400">Admin can add keys, but developers must use them in code.</p>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold uppercase text-gray-500">Group</label>
                        <input id="translationGroupInput" type="text" class="border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-500" placeholder="nav">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold uppercase text-gray-500">English</label>
                        <textarea id="translationEn" rows="5" maxlength="2000" class="border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-500"></textarea>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold uppercase text-gray-500">Bangla</label>
                        <textarea id="translationBn" rows="5" maxlength="2000" class="border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-500"></textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-[minmax(0,1fr)_160px]">
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold uppercase text-gray-500">Description</label>
                        <input id="translationDescription" type="text" class="border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-500" placeholder="Where this text is used">
                    </div>
                    <label class="flex items-center gap-2 border border-gray-200 px-3 py-2 text-sm font-medium text-gray-600">
                        <input id="translationActive" type="checkbox" class="h-4 w-4" checked>
                        Active
                    </label>
                </div>

                <div id="translationErrors" class="hidden border border-red-100 bg-red-50 p-3 text-sm text-red-700"></div>

                <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                    <button type="button" onclick="closeTranslationModal()" class="border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-600 transition hover:bg-gray-50">
                        Cancel
                    </button>
                    <button id="translationSaveButton" type="submit" class="bg-blue-600 px-5 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                        Save Translation
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const translationState = {
            currentPage: 1,
            items: [],
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        async function apiRequest(url, options = {}) {
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(options.headers || {}),
                },
                ...options,
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw data;
            }

            return data;
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        async function loadTranslations(page = 1) {
            const search = document.getElementById('translationSearch').value;
            const group = document.getElementById('translationGroup').value;
            const params = new URLSearchParams({ page });

            if (search) params.set('search', search);
            if (group) params.set('group', group);

            try {
                const data = await apiRequest(`/api/public-translations?${params.toString()}`);
                translationState.currentPage = data.translations.current_page;
                translationState.items = data.translations.data;

                renderGroupOptions(data.groups || [], group);
                renderTranslations(data.translations);
            } catch (error) {
                renderLoadError(error);
            }
        }

        function renderGroupOptions(groups, selectedGroup) {
            const select = document.getElementById('translationGroup');
            const current = selectedGroup ?? select.value;

            select.innerHTML = '<option value="">All Groups</option>' + groups.map((group) => (
                `<option value="${escapeHtml(group)}" ${group === current ? 'selected' : ''}>${escapeHtml(group)}</option>`
            )).join('');
        }

        function renderTranslations(paginator) {
            const table = document.getElementById('translationTable');

            if (!paginator.data.length) {
                table.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No translations found.</td>
                    </tr>
                `;
            } else {
                table.innerHTML = paginator.data.map((item) => `
                    <tr>
                        <td class="max-w-[260px] px-4 py-4 align-top">
                            <p class="font-mono text-xs font-semibold text-gray-800">${escapeHtml(item.key)}</p>
                            <p class="mt-1 text-[11px] uppercase tracking-wider text-gray-400">${escapeHtml(item.group || 'general')}</p>
                            ${item.description ? `<p class="mt-2 text-[11px] leading-4 text-gray-500">${escapeHtml(item.description)}</p>` : ''}
                        </td>
                        <td class="max-w-[280px] px-4 py-4 align-top text-sm text-gray-600">
                            <p class="line-clamp-3">${escapeHtml(item.en || 'Fallback from lang file')}</p>
                        </td>
                        <td class="max-w-[280px] px-4 py-4 align-top text-sm text-gray-600">
                            <p class="line-clamp-3">${escapeHtml(item.bn || 'Fallback from lang file')}</p>
                        </td>
                        <td class="px-4 py-4 align-top">
                            <span class="inline-flex border px-2 py-1 text-xs font-semibold ${item.is_active ? 'border-emerald-100 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-100 text-slate-600'}">
                                ${item.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right align-top">
                            <button type="button" onclick="openTranslationModal(${item.id})" class="border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 transition hover:border-blue-200 hover:text-blue-600">
                                Edit
                            </button>
                        </td>
                    </tr>
                `).join('');
            }

            renderPagination(paginator);
        }

        function renderLoadError(error) {
            document.getElementById('translationTable').innerHTML = `
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-red-600">
                        ${escapeHtml(error.message || 'Unable to load translations. Please refresh and try again.')}
                    </td>
                </tr>
            `;
            document.getElementById('translationPagination').innerHTML = '';
        }

        function renderPagination(paginator) {
            const wrapper = document.getElementById('translationPagination');
            const from = paginator.from ?? 0;
            const to = paginator.to ?? 0;
            const total = paginator.total ?? 0;

            wrapper.innerHTML = `
                <p class="text-xs text-gray-500">Showing ${from} to ${to} of ${total}</p>
                <div class="flex items-center gap-2">
                    <button type="button" ${paginator.current_page <= 1 ? 'disabled' : ''} data-translation-page="${paginator.current_page - 1}" class="border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600 disabled:cursor-not-allowed disabled:opacity-40">Previous</button>
                    <span class="text-xs font-semibold text-gray-500">Page ${paginator.current_page} of ${paginator.last_page}</span>
                    <button type="button" ${paginator.current_page >= paginator.last_page ? 'disabled' : ''} data-translation-page="${paginator.current_page + 1}" class="border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600 disabled:cursor-not-allowed disabled:opacity-40">Next</button>
                </div>
            `;
        }

        function openTranslationModal(id = null) {
            const item = id ? translationState.items.find((translation) => translation.id === id) : null;

            document.getElementById('translationModalTitle').textContent = item ? 'Edit Translation' : 'Add Translation';
            document.getElementById('translationId').value = item?.id || '';
            document.getElementById('translationKey').value = item?.key || '';
            document.getElementById('translationKey').readOnly = Boolean(item);
            document.getElementById('translationKey').classList.toggle('bg-gray-100', Boolean(item));
            document.getElementById('translationKey').classList.toggle('text-gray-500', Boolean(item));
            document.getElementById('translationKeyHelp').textContent = item
                ? 'Existing keys are read-only because Blade/PHP code uses this exact key.'
                : 'Admin can add keys, but developers must use them in code.';
            document.getElementById('translationGroupInput').value = item?.group || '';
            document.getElementById('translationEn').value = item?.en || '';
            document.getElementById('translationBn').value = item?.bn || '';
            document.getElementById('translationDescription').value = item?.description || '';
            document.getElementById('translationActive').checked = item ? Boolean(item.is_active) : true;
            clearTranslationErrors();

            document.getElementById('translationModal').classList.remove('hidden');
            document.getElementById('translationModal').classList.add('flex');
        }

        function closeTranslationModal() {
            document.getElementById('translationModal').classList.add('hidden');
            document.getElementById('translationModal').classList.remove('flex');
        }

        function clearTranslationErrors() {
            const errorBox = document.getElementById('translationErrors');
            errorBox.classList.add('hidden');
            errorBox.innerHTML = '';
        }

        function showTranslationErrors(error) {
            const errors = error.errors || {};
            const messages = Object.values(errors).flat();
            const errorBox = document.getElementById('translationErrors');

            errorBox.innerHTML = messages.length
                ? messages.map((message) => `<p>${escapeHtml(message)}</p>`).join('')
                : `<p>${escapeHtml(error.message || 'Unable to save translation.')}</p>`;
            errorBox.classList.remove('hidden');
        }

        async function saveTranslation(event) {
            event.preventDefault();
            clearTranslationErrors();

            const id = document.getElementById('translationId').value;
            const button = document.getElementById('translationSaveButton');
            const originalText = button.textContent;
            const payload = {
                group: document.getElementById('translationGroupInput').value,
                en: document.getElementById('translationEn').value,
                bn: document.getElementById('translationBn').value,
                description: document.getElementById('translationDescription').value,
                is_active: document.getElementById('translationActive').checked,
            };

            if (!id) {
                payload.key = document.getElementById('translationKey').value;
            }

            button.disabled = true;
            button.textContent = 'Saving...';

            try {
                await apiRequest(id ? `/api/public-translations/${id}` : '/api/public-translations', {
                    method: id ? 'PUT' : 'POST',
                    body: JSON.stringify(payload),
                });
                closeTranslationModal();
                await loadTranslations(translationState.currentPage);
            } catch (error) {
                showTranslationErrors(error);
            } finally {
                button.disabled = false;
                button.textContent = originalText;
            }
        }

        document.getElementById('translationSearch').addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                loadTranslations(1);
            }
        });

        document.getElementById('translationFilterButton').addEventListener('click', () => loadTranslations(1));
        document.getElementById('translationGroup').addEventListener('change', () => loadTranslations(1));
        document.getElementById('translationPagination').addEventListener('click', (event) => {
            const button = event.target.closest('[data-translation-page]');

            if (!button || button.disabled) {
                return;
            }

            loadTranslations(Number(button.dataset.translationPage));
        });
        document.addEventListener('DOMContentLoaded', () => loadTranslations(1));
    </script>
@endpush
