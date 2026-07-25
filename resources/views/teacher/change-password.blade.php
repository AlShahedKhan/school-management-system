@extends('layouts.teacher')

@section('title', 'Change Password')

@section('page-title', 'Change Password')

@push('styles')
<style>
    @if(Illuminate\Support\Facades\Hash::check('00000000', auth()->user()->password))
        aside#sidebar {
            display: none !important;
        }
        header.topbar {
            left: 0 !important;
            width: 100% !important;
            padding: 0 1.5rem !important;
        }
        header.topbar #hamburger {
            display: none !important;
        }
        main.main-scroll {
            margin-left: 0 !important;
            padding-left: 1.5rem !important;
            padding-right: 1.5rem !important;
        }
    @endif
</style>
@endpush

@section('content')
<div class="max-w-md mx-auto my-8">

    <div class="bg-white border border-gray-200 p-6 shadow-sm" style="border-radius: 0;">

        <div class="mb-5">

            <h2 class="text-lg font-medium text-gray-800">Set New Password</h2>

            @if(Illuminate\Support\Facades\Hash::check('00000000', auth()->user()->password))
                <p class="text-xs text-red-500 mt-1">You are currently using the default password. Please choose a new password to continue.</p>
            @else
                <p class="text-xs text-gray-500 mt-1">Please enter your current password and your new password.</p>
            @endif

        </div>

        <form id="changePasswordForm" class="flex flex-col gap-4">

            @csrf

            <div>

                <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5 font-medium">Current Password</label>

                <input type="password" name="current_password" required
                    class="w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px] outline-none focus:border-blue-500"
                    style="border-radius: 0;" />

            </div>

            <div>

                <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5 font-medium">New Password</label>

                <input type="password" name="new_password" required
                    class="w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px] outline-none focus:border-blue-500"
                    style="border-radius: 0;" />

            </div>

            <div>

                <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5 font-medium">Confirm New Password</label>

                <input type="password" name="new_password_confirmation" required
                    class="w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px] outline-none focus:border-blue-500"
                    style="border-radius: 0;" />

            </div>

            <div class="flex flex-col gap-2 mt-2">

                <button type="submit"
                    class="w-full h-[32px] border border-blue-600 bg-blue-600 text-white text-xs hover:bg-blue-700 transition flex items-center justify-center"
                    style="border-radius: 0;">
                    Update Password
                </button>

                @if(!Illuminate\Support\Facades\Hash::check('00000000', auth()->user()->password))
                    <a href="{{ route('teacher.dashboard') }}"
                        class="w-full h-[32px] border border-gray-200 bg-white text-gray-700 text-xs hover:bg-gray-50 transition flex items-center justify-center"
                        style="border-radius: 0;">
                        Cancel
                    </a>
                @else
                    <button type="button" onclick="logoutUser()"
                        class="w-full h-[32px] border border-red-500 bg-white text-red-500 text-xs hover:bg-red-50 transition flex items-center justify-center"
                        style="border-radius: 0;">
                        Logout
                    </button>
                @endif

            </div>

        </form>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('changePasswordForm').addEventListener('submit', function (e) {

        e.preventDefault();

        const formData = new FormData(this);

        axios.post('{{ url('/api/teacher/change-password') }}', formData)

            .then(res => {

                Swal.fire({

                    icon: 'success',

                    title: 'Password Updated',

                    text: 'Your password has been changed successfully. Redirecting...',

                    timer: 2000,

                    showConfirmButton: false

                }).then(() => {

                    window.location.href = '{{ route('teacher.dashboard') }}';

                });

            })

            .catch(err => {

                let msg = 'Failed to update password';

                if (err.response && err.response.data.message) {

                    msg = err.response.data.message;

                }

                Swal.fire({

                    icon: 'error',

                    title: 'Error',

                    text: msg

                });

            });

    });
</script>
@endsection
