@extends('layouts.app')

@section('styles')  
    
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>  

    @include('users.partials.styles')
    @include('users.partials.bs-visibility-css')

@endsection

@section('content')
    <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    @include('users.partials.form-status')
                </div>
            </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">

                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Editando Usuario con RFC: ') }} <b>{{ $user->name }}</b>
                            </span>


                            <div class="pull-right">

                                <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm float-right">
                                    <i class="fas fa-fw fa-reply-all" aria-hidden="true"></i>
                                    <span class="hidden-xs">Volver a Panel de Control</span>
                                </a>

                                <a href="{{ route('users.index') }}" class="btn btn-light btn-sm float-right">
                                    <i class="fas fa-fw fa-reply" aria-hidden="true"></i>
                                    <span class="hidden-sm hidden-xs">Volver a Usuarios</span>
                                </a>

                                
                            </div>

                        </div>
                        
                    </div>

                    <div class="card-body">
                        {!! Form::open(array('route' => ['users.update', $user->id], 'method' => 'PUT', 'role' => 'form', 'class' => 'needs-validation')) !!}
                            {!! csrf_field() !!}
                            <div class="form-group has-feedback row {{ $errors->has('name') ? ' has-error ' : '' }}">
                                    {!! Form::label('full_name', 'Nombre:', array('class' => 'col-md-3 control-label')); !!}
                                <div class="col-md-9">
                                    <div class="input-group">
                                        {!! Form::text('full_name', $user->full_name, array('id' => 'full_name', 'class' => 'form-control', 'placeholder' => 'Escriba el nombre completo')) !!}
                                        <div class="input-group-append">
                                            <label class="input-group-text" for="name">
                                                    <i class="fa fa-fw fa-user" aria-hidden="true"></i>
                                            </label>
                                        </div>
                                    </div>
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group has-feedback row {{ $errors->has('email') ? ' has-error ' : '' }}">
                                    {!! Form::label('email', 'Correo electrónico:', array('class' => 'col-md-3 control-label')); !!}
                                <div class="col-md-9">
                                    <div class="input-group">
                                        {!! Form::text('email', $user->email, array('id' => 'email', 'class' => 'form-control', 'placeholder' => 'Escriba su correo electrónico personal')) !!}
                                        <div class="input-group-append">
                                            <label for="email" class="input-group-text">
                                                    <i class="fa fa-fw fa-envelope" aria-hidden="true"></i> 
                                            </label>
                                        </div>
                                    </div>
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group has-feedback row {{ $errors->has('role') ? ' has-error ' : '' }}">                                    
                                    {!! Form::label('role', 'Tipo de Usuario:', array('class' => 'col-md-3 control-label')); !!}
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <select class="custom-select form-control" name="role" id="role">
                                            @if ($roles)
                                                @foreach($roles as $role)
                                                    @if ($currentRole)
                                                        <option value="{{ $role }}" {{ $currentRole == $role ? 'selected="selected"' : '' }}>{{ $role }}</option>
                                                    @else
                                                        <option value="{{ $role }}">{{ $role }}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="input-group-append">
                                            <label class="input-group-text" for="role">
                                                    <i class="fas fa-fw fas fa-shield-alt" aria-hidden="true"></i>
                                            </label>
                                        </div>
                                    </div>
                                    @if ($errors->has('role'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('role') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="pw-change-container">
                                <div class="form-group has-feedback row {{ $errors->has('password') ? ' has-error ' : '' }}">
                                        {!! Form::label('password', 'Contraseña:', array('class' => 'col-md-3 control-label')); !!}
                                    <div class="col-md-9">
                                        <div class="input-group">
                                            {!! Form::password('password', array('id' => 'password', 'class' => 'form-control ', 'placeholder' => 'Nueva Contraseña')) !!}
                                            <div class="input-group-append">
                                                <label class="input-group-text" for="password">
                                                        <i class="fa fa-fw fa-lock" aria-hidden="true"></i>
                                                </label>
                                            </div>
                                        </div>
                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group has-feedback row">
                                        {!! Form::label('password_confirmation', 'Repetir Contraseña', array('class' => 'col-md-3 control-label')); !!}
                                    <div class="col-md-9">
                                        <div class="input-group">
                                            {!! Form::password('password_confirmation', array('id' => 'password_confirmation', 'class' => 'form-control', 'placeholder' => 'Escriba nuevamente su contraseña')) !!}
                                            <div class="input-group-append">
                                                <label class="input-group-text" for="password_confirmation">
                                                        <i class="fa fa-fw fa-lock" aria-hidden="true"></i>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6 mb-2">
                                    <a href="#" class="btn btn-outline-secondary btn-block btn-change-pw mt-3" title="Change Password">
                                        <i class="fa fa-fw fa-lock" aria-hidden="true"></i>
                                        <span></span> Cambiar la contraseña
                                    </a>
                                </div>
                                <div class="col-12 col-sm-6">
                                    {!! Form::button('<i class="fa fa-fw fa-save" aria-hidden="true"></i> Guardar cambios', array('class' => 'btn btn-success btn-block margin-bottom-1 mt-3 mb-2 btn-save','type' => 'button', 'data-toggle' => 'modal', 'data-target' => '#confirmSave', 'data-title' => 'Confirmacion', 'data-message' => '¿Desea guardar los cambios efectuados?')) !!}
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-success modal-save" id="confirmSave" role="dialog" aria-labelledby="confirmSaveLabel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Cerrar</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                </p>
            </div>
            <div class="modal-footer">
                {!! Form::button('<i class="fa fa-fw fa-times" aria-hidden="true"></i> Cancelar', array('class' => 'btn btn-outline pull-left btn-flat', 'type' => 'button', 'data-dismiss' => 'modal' )) !!}
                {!! Form::button('<i class="fa fa-fw fa-save" aria-hidden="true"></i> Guardar cambios', array('class' => 'btn btn-success pull-right btn-flat', 'type' => 'button', 'id' => 'confirm' )) !!}
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts')

    <script type="text/javascript">

      $('#confirmSave').on('show.bs.modal', function (e) {
        var message = $(e.relatedTarget).attr('data-message');
        var title = $(e.relatedTarget).attr('data-title');
        var form = $(e.relatedTarget).closest('form');
        $(this).find('.modal-body p').text(message);
        $(this).find('.modal-title').text(title);
        $(this).find('.modal-footer #confirm').data('form', form);
      });
      $('#confirmSave').find('.modal-footer #confirm').on('click', function(){
          $(this).data('form').submit();
      });

    </script>

    <script type="text/javascript">
      $('.btn-change-pw').click(function(event) {
        event.preventDefault();
        $('.pw-change-container').slideToggle(100);
        $(this).find('.fa').toggleClass('fa-times');
        $(this).find('.fa').toggleClass('fa-lock');
        $(this).find('span').toggleText('', 'Cancelar');
      });
      $("input").keyup(function() {
        checkChanged();
      });
      $("select").change(function() {
        checkChanged();
      });
      function checkChanged() {
        if(!$('input').val()){
          $(".btn-save").hide();
        }
        else {
          $(".btn-save").show();
        }
      }
    </script>

    @include('users.scripts.tooltips')
@endsection

