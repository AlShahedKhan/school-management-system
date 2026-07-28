<div class="hidden lg:flex items-center gap-2">
    <x-input.search
        id="teacherSearch"
        name="search"
        value="{{ request('search') }}"
        placeholder="Search Faculty..."
        class="hidden w-full lg:block lg:w-72"
    />
    <x-button.secondary id="btnRestoreDesktop" onclick="document.getElementById('teacherSearch').value = ''; document.getElementById('teacherSearch').dispatchEvent(new Event('input'));">
        Restore
    </x-button.secondary>
</div>
