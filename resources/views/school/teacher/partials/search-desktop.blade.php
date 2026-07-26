<form method="GET" action="{{ route('school.teacher-registration') }}" class="hidden lg:flex items-center gap-2">
    <x-input.search
        id="teacherSearch"
        name="search"
        value="{{ request('search') }}"
        placeholder="Search Faculty..."
        class="hidden w-full lg:block lg:w-72"
    />
    <x-button.secondary id="btnRestoreDesktop" onclick="window.location.href='{{ route('school.teacher-registration') }}'">
        Restore
    </x-button.secondary>
</form>
