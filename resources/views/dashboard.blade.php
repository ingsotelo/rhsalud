@extends('layouts.app')

@section('styles')
<style type="text/css">
    #menu {
        padding-top: 60px;
        padding-bottom: 35px;
    }

    #menu h3 {
      
        text-align:center;

    }

    #menu h4 {
        font-size: 15px;
        text-align:center;
        font-weight: 700;
        background:rgba(0,0,0,0.5);
        padding-bottom: 20px;

    }

    #menu i {
        padding-top: 30px;
        padding-bottom: 30px;
        color: #ffffff;
        font-size: 90px;
        display: inline-block;
        width: 100%;
        text-align:center;
        background:rgba(0,0,0,0.5);

    }

    #menu p {
        text-align:center;
        display: inline-block;
        width: 100%;
    }

    #menu a {
      text-decoration: none;
      display: inline-block;
      width: 100%;
      color: black;
    }

    #menu a:hover {
      position: relative;
      color: blue;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css">
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Panel de Control</div>
                <div class="card-body">
                    <h4>Bienvenido {{ Auth::user()->full_name }}</h4>
                    <div id="menu">
                        <div class="container">
                            
                                <div class="row justify-content-center">                              

                                    <div class="col-md-3 enterleave">
                                        <a href="{{ route('users.index') }}"> 
                                            <i class="fas fa-users-cog"></i>
                                            <p>{{ __('Administracion de Usuarios') }}</p>
                                        </a>
                                    </div>
                                    

                                    <div class="col-md-3 enterleave">
                                        <a href="{{ route('cfdis.index') }}"> 
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p>{{ __('Cargar Archivos XML') }}</p>
                                        </a>
                                    </div>

                                    <div class="col-md-3 enterleave">
                                        <a href="#"> 
                                            <i class="fas fa-envelope-open-text"></i>
                                            <p>{{ __('Enviar Correo Masivo') }}</p>
                                        </a>
                                    </div>

                                    <div class="col-md-3 enterleave">
                                        <a href="#"> 
                                            <i class="fas fa-file-alt"></i> 
                                            <p>{{ __('Documentos Recibidos') }}</p>
                                        </a> 
                                    </div>

                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    $( "div.enterleave" )
        .mouseenter(function() {
        $( this ).addClass('animated pulse');
    })
        .mouseleave(function() {
        $( this ).removeClass('animated pulse');
    });

</script>
@endsection