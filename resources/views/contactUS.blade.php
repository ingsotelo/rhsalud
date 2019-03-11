@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Contacto</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="form-group ">
                        <label for="RFC:">RFC:</label>
                        <input class="form-control" placeholder=" {{ Auth::user()->name }}" name="name" type="text" disabled >
                        <span class="text-danger"></span>
                    </div>
                     
                    <div class="form-group ">
                        <label for="Correo:">Correo:</label>
                        <input class="form-control" placeholder=" {{ Auth::user()->email }}" name="email" type="text" disabled >
                        <span class="text-danger"></span>
                    </div>
                     
                    {!! Form::open(['route'=>'contactus.store']) !!}
                       
                    <div class="form-group {{ $errors->has('message') ? 'has-error' : '' }}">
                    {!! Form::label('Comentarios:') !!}
                    {!! Form::textarea('message', old('message'), ['class'=>'form-control', 'placeholder'=>'Escriba su Comentario']) !!}
                    <span class="text-danger">{{ $errors->first('message') }}</span>
                    </div>
                     
                    <div class="form-group">
                    <button class="btn btn-success">Enviar</button>
                    </div>
                     
                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection