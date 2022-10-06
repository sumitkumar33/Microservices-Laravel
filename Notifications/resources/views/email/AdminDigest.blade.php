@component('mail::message')
# Hello, {{$name}} ({{$email}})

Today's DailyDigest is as follows:<br>
UnApproved members count: {{$count}}<br>

@component('mail::button', ['url' => url('/dashboard')])
Check Here
@endcomponent

Thanks,<br>
SchoolApp Microservice Team.
@endcomponent
