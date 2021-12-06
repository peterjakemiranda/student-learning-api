@component('mail::message')

You've been invited by {{$teacher->fullname}} to participate in a class at SDSSU Learning App.

Course: {{$course->title}}<br>
Description: {{$course->description}}<br>
Period: {{$course->period}}<br>
Your Email: {{$user->email}}<br>

You'll need to login to SDSSU Learning App before you can participate in the class.

Thanks,<br>
{{ config('app.name') }}
@endcomponent