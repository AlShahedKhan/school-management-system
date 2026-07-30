<x-input.search
    id="teacherSearchMobile"
    name="search"
    value="{{ request('search') }}"
    placeholder="Search Faculty..."
    class="col-span-2 min-w-0"
/>
<x-button.secondary id="btnRestoreMobile" onclick="document.getElementById('teacherSearchMobile').value = ''; document.getElementById('teacherSearchMobile').dispatchEvent(new Event('input'));">
    Restore
</x-button.secondary>
