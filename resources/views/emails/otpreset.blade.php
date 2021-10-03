@component('mail::message')

    You have requested a password change, use the OTP code below

    {{ $code }}


    Thanks,
    {{ config('app.name') }}
@endcomponent
