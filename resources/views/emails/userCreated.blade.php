@component('mail::message')

An administration at SDSSU Learning App created an account for you:

Name: {{$user->fullname}}<br>
Email: {{$user->email}}<br>
Password: {{$random_password}}<br>

You'll need to install to start using SDSSU Learning App.

Thanks,<br>
{{ config('app.name') }}
@endcomponent