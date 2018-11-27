@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Verifique su dirección de correo electrónico') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('Se ha enviado un nuevo enlace de verificación a su dirección de correo electrónico.') }}
                        </div>
                    @endif
                    <div class="alert alert-warning" role="alert">
                        {{ __('Antes de continuar, compruebe su correo electrónico para ver un enlace de verificación. Según el nivel de protección que seleccione para el filtro de correo electrónico no deseado, es posible que algunos mensajes que desea ver se muevan a la carpeta Correo electrónico no deseado. Es aconsejable revisar periódicamente los mensajes de la carpeta Correo electrónico no deseado para asegurarse de que no está perdiendo mensajes que le interesan.') }}
                        {{ __('Si no ha recibido el correo electrónico') }}, <a href="{{ route('verification.resend') }}">{{ __('haga clic aquí para solicitar otro') }}</a>.
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
