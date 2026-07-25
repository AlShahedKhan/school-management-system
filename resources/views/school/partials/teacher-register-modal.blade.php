<x-modal.form
    id="teacherModal"
    form-id="teacherForm"
    title="Teacher registration"
    close-button-id="closeTeacherModal"
    title-class="teacher-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
>
    <input type="hidden" name="teacher_id" id="teacher_id">

    <div class="relative">
        <x-input.control id="teacherName" class="peer placeholder:text-transparent" name="name" placeholder=" " required />
        <x-input.floating-label for="teacherName">
            Full name
        </x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.control id="teacherDesignation" class="peer placeholder:text-transparent" name="designation" placeholder=" " />
        <x-input.floating-label for="teacherDesignation">
            Designation
        </x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.control
            id="teacherMobile"
            class="peer placeholder:text-transparent"
            name="mobile"
            required
            placeholder=" "
            inputmode="numeric"
            pattern="[0-9]+"
            title="Must provide numbers only." />
        <x-input.floating-label for="teacherMobile">
            Mobile
        </x-input.floating-label>
        <p id="teacherMobileError" class="mt-1 hidden text-[10px] text-red-500">
            Must provide numbers only.
        </p>
    </div>

    <div class="relative">
        <x-input.control id="teacherEmail" class="peer placeholder:text-transparent" type="email" name="email" placeholder=" " required />
        <x-input.floating-label for="teacherEmail">
            Email address
        </x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.photo />
        <x-input.floating-label for="photoInput" :floating="false" class="pointer-events-auto peer-focus:text-slate-500">
            Profile photo
        </x-input.floating-label>
    </div>
</x-modal.form>
