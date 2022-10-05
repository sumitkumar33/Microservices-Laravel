@component('mail::message')
# Hello! {{$name}},

Your account has been approved by {{$admin_name}},

@component('mail::button', ['url' => url('/dashboard')])
Check Account
@endcomponent

Thanks,<br>
SchoolApp Team.
@endcomponent
