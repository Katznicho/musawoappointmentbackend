@component('mail::message')
 Hello  {{ $doctor_name }},

You have a new patient  from request {{ $patient_name }}. Please check your app for more details.
If you dont have the app, please download it from the link below.

@component(
'mail::button', [
    'url' => 'https://play.google.com/store/apps/details?id=com.adfamedicare.app',
    'color' => 'success',


    ]

)
Download App
@endcomponent




Thanks,<br>
{{ config('app.name') }}
@endcomponent
