<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel Users Blades Language Lines
    |--------------------------------------------------------------------------
    */

    'showing-all-users'     => 'Usuarios Registrados',
    'users-menu-alt'        => 'Mostrar menú de gestión de usuarios',
    'create-new-user'       => 'Crear nuevo usuario',
    'show-deleted-users'    => 'Mostrar usuario eliminado',
    'editing-user'          => 'Editando Usuario :name',
    'showing-user'          => 'Mostrando Usuario :name',
    'showing-user-title'    => 'Información de :name',

    'users-table' => [
        'caption'   => '{1} :userscount Usuario|[2,*] :userscount Usuarios',
        'id'        => 'ID',
        'name'      => 'RFC',
        'email'     => 'Correo',
        'role'      => 'Tipo',
        'created'   => 'Creado',
        'updated'   => 'Actualizado',
        'actions'   => 'Opciones',
    ],

    'buttons' => [
        'create-new'    => '<span class="hidden-xs hidden-sm">Nuevo Usuario</span>',
        'delete'        => '<i class="far fa-trash-alt fa-fw" aria-hidden="true"></i>  <span class="hidden-xs hidden-sm">Eliminar</span>',
        'show'          => '<i class="fas fa-eye fa-fw" aria-hidden="true"></i> <span class="hidden-xs hidden-sm">Mostrar</span>',
        'edit'          => '<i class="fas fa-pencil-alt fa-fw" aria-hidden="true"></i> <span class="hidden-xs hidden-sm">Editar</span>',
        'back-to-users' => '<span class="hidden-sm hidden-xs">Volver a </span><span class="hidden-xs">Usuarios</span>',
        'back-to-user'  => '<span class="hidden-xs">Volver</span>',
        'delete-user'   => '<i class="far fa-trash-alt fa-fw" aria-hidden="true"></i>  <span class="hidden-xs">Eliminar</span><span class="hidden-xs"> Usuario</span>',
        'edit-user'     => '<i class="fas fa-pencil-alt fa-fw" aria-hidden="true"></i> <span class="hidden-xs">Editar</span><span class="hidden-xs"> Usuario</span>',
        'upload-csv'  => '<span class="hidden-xs hidden-sm">Cargar CSV de Usuarios</span>',
    ],

    'tooltips' => [
        'delete'        => 'Eliminar',
        'show'          => 'Mostrar',
        'edit'          => 'Editar',
        'create-new'    => 'Crear nuevo usuario',
        'back-users'    => 'Volver',
        'email-user'    => 'Correo Electronico :user',
        'submit-search' => 'Buscar Usuarios',
        'clear-search'  => 'Limpiar Resultados',
        'upload-csv'  => 'Carga Masiva de Usuarios',
    ],

    'messages' => [
        'userNameTaken'          => 'RFC ya existe.',
        'userNameRequired'       => 'Username is required',
        'fNameRequired'          => 'First Name is required',
        'lNameRequired'          => 'Last Name is required',
        'emailRequired'          => 'Email is required',
        'emailInvalid'           => 'Email is invalid',
        'passwordRequired'       => 'Password is required',
        'PasswordMin'            => 'Password needs to have at least 6 characters',
        'PasswordMax'            => 'Password maximum length is 20 characters',
        'captchaRequire'         => 'Captcha is required',
        'CaptchaWrong'           => 'Wrong captcha, please try again.',
        'roleRequired'           => 'User role is required.',
        'user-creation-success'  => 'Usuario creado con éxito!',
        'update-user-success'    => 'Usuario actualizado con éxito!',
        'delete-success'         => 'Se eliminó con éxito el usuario!',
        'cannot-delete-yourself' => '¡No puedes eliminarte a ti mismo!',
    ],

    'show-user' => [
        'id'                => 'ID',
        'name'              => 'RFC',
        'email'             => 'Correo<span class="hidden-xs"> Electronico</span>',
        'role'              => 'Tipo',
        'created'           => 'Creado',
        'updated'           => 'Actualizado',
        'labelRole'         => 'User Role',
        'labelAccessLevel'  => '<span class="hidden-xs">User</span> Access Level|<span class="hidden-xs">User</span> Access Levels',
    ],

    'search'  => [
        'title'         => 'Showing Search Results',
        'found-footer'  => ' Record(s) found',
        'no-results'    => 'No Results',
    ],
];
