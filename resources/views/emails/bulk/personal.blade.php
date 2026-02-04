@component('mail::message')
# Xin chào {{ $name }},

{!! nl2br(e($content)) !!}

Trân trọng,<br>
{{ config('app.name') }}
@endcomponent
