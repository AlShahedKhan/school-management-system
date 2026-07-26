<div class="mt-3 grid grid-cols-3 gap-2 lg:hidden">
    <x-input.search
        id="studentSearchMobile"
        placeholder="Search students..."
        class="col-span-2 min-w-0"
    />
    <x-button.secondary id="btnRestoreMobile" onclick="document.getElementById('studentSearchMobile').value = ''; document.getElementById('studentSearchMobile').dispatchEvent(new Event('input'));">
        Restore
    </x-button.secondary>
</div>
