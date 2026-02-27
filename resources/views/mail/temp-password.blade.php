<x-mail::message>
# Welcome to {{ config('app.name') }}!

Your account has been created. Here are your login details:

**Email:** {{ $userEmail }} <br>
**Temporary Password:** {{ $tempPassword }}

Please login and change your password immediately.

<x-mail::button :url="route('login')">
    Login to Your Account
</x-mail::button>

<x-mail::panel>
    Security Tip: Never share your password with anyone. Our team will never ask for your password.
</x-mail::panel>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
