@component('mail::message')
# Form received

The following form has been submitted via the website:

@component('mail::panel')
{{ $message }}
@endcomponent

@endcomponent
