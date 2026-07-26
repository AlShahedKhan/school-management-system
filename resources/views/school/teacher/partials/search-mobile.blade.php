<form method="GET" action="{{ route('school.teacher-registration') }}" class="mt-3 grid grid-cols-3 gap-2 lg:hidden">
    <x-input.search
        id="teacherSearchMobile"
        name="search"
        value="{{ request('search') }}"
        placeholder="Search Faculty..."
        class="col-span-2 min-w-0"
    />
    <x-button.secondary id="btnRestoreMobile" onclick="window.location.href='{{ route('school.teacher-registration') }}'">
        Restore
    </x-button.secondary>
</form>
