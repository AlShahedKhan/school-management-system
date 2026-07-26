<div class="hidden lg:flex items-center gap-2">
    <x-input.search
        id="studentSearch"
        placeholder="Search students..."
        class="hidden w-full lg:block lg:w-72"
    />
    <x-button.secondary id="btnRestoreDesktop" onclick="document.getElementById('studentSearch').value = ''; document.getElementById('studentSearch').dispatchEvent(new Event('input'));">
        Restore
    </x-button.secondary>
</div>
