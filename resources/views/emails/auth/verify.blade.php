@component('mail::message')

Hi {{ $username }},

Welcome to senhung.net. You have received this email because you recently registered in **senhung.net**.
If you did not register, please disregard this email.

To verify your account, please click the button below.

@component('mail::button', ['url' => $activationUrl])
    Verify Email
@endcomponent

Or copy and past the URL into your browser:

{{ $activationUrl }}

Thanks,

{{ config('app.name') }}

@endcomponent
