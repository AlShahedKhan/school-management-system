<x-input.search
    id="teacherSearch"
    name="search"
    value="{{ request('search') }}"
    placeholder="Search Faculty..."
    class="w-full lg:w-72"
/>
<x-button.secondary id="btnRestoreDesktop" onclick="document.getElementById('teacherSearch').value = ''; document.getElementById('teacherSearch').dispatchEvent(new Event('input'));">
    Restore
</x-button.secondary>
