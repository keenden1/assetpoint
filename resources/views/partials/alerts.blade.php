{{-- SweetAlert2 feedback driven by session flashes and validation errors.
     Success/status/error messages show as corner TOASTS (non-blocking,
     auto-dismiss). Confirmation dialogs elsewhere still use full popups. --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
<script>
    // Reusable toast (also available to page scripts as window.toast).
    window.toast = function (icon, title, options) {
        return Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            didOpen: function (el) {
                el.addEventListener('mouseenter', Swal.stopTimer);
                el.addEventListener('mouseleave', Swal.resumeTimer);
            },
        }).fire(Object.assign({ icon: icon, title: title }, options || {}));
    };

    document.addEventListener('DOMContentLoaded', function () {
        @php
            // Map Fortify's terse status keys to friendly messages.
            $statusMessages = [
                'verification-link-sent' => 'A new verification link has been sent to your email address.',
                'two-factor-authentication-enabled' => 'Two-factor authentication has been enabled.',
                'two-factor-authentication-disabled' => 'Two-factor authentication has been disabled.',
                'recovery-codes-generated' => 'Recovery codes have been regenerated.',
                'profile-information-updated' => 'Your profile information has been updated.',
                'password-updated' => 'Your password has been updated.',
            ];
        @endphp

        @if ($errors->any() && ! View::hasSection('suppressErrorAlert'))
            toast('error', 'Please check the form', {
                html: @json('<ul style="text-align:left;margin:0;padding-left:1.2rem;">'
                    . collect($errors->all())->map(fn ($e) => '<li>' . e($e) . '</li>')->implode('')
                    . '</ul>'),
                timer: 6000,
            });
        @elseif (session('verification_email_failed'))
            toast('error', "We couldn't send your verification email. Use the Resend button to try again.", { timer: 6000 });
            @php session()->forget('verification_email_failed'); @endphp
        @elseif (session('error'))
            toast('error', @json(session('error')), { timer: 5000 });
        @elseif (session('success'))
            toast('success', @json(session('success')));
        @elseif (session('status'))
            toast('success', @json($statusMessages[session('status')] ?? session('status')));
        @endif
    });
</script>
