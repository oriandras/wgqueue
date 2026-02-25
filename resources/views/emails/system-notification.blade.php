<x-mail::message>
# {{ $title }}

{{ $message }}

@if($buttonUrl)
<x-mail::button :url="$buttonUrl">
{{ $buttonText ?? 'Megtekintés' }}
</x-mail::button>
@endif

Köszönettel,
{{ config('app.name') }}
</x-mail::message>
