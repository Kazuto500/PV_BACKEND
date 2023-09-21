@component('mail::message')
# Verificación de correo electrónico

Por favor, haga clic en el botón de abajo para verificar su dirección de correo electrónico:

@component('mail::button', ['url' => $verificationLink])
Verificar Email
@endcomponent

Gracias por registrarse en nuestra plataforma.

Saludos,
{{ config('app.name') }}
@endcomponent
