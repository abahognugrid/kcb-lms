<x-mail::message>
# Password Successfully Reset

Hello {{ $name }},

Your password for {{ $appName }} was successfully reset on {{ $time }}.

If you did not request this password reset, please contact our support team immediately.

<x-mail::button :url="route('login')">
    Login to Your Account
</x-mail::button>

<x-mail::panel>
    Security Tip: Always keep your password secure and never share it with anyone.
</x-mail::panel>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
