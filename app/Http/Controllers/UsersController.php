<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Jobs\ProceessUploadData;



class UsersController extends Controller
{


    public function __construct()
    {
        
        $this->middleware('auth');
        $this->middleware('verified');
            
    }


    public function index()
    {
        if (Gate::denies('isAdmin')) {
            abort(403,"Lo siento, Usted no tiene autorizado el acceso a este recurso");
        }

        $users = DB::select("SELECT * FROM rhsalud.users");        
        $data = [
            'users' => $users,
        ];

        return view('users.show-users', $data);
    }


    public function destroy($id)
    {

        $currentUser = \Auth::user();
        $user = User::findOrFail($id);

        if ($currentUser->id != $user->id) {
            $user->delete();
            return redirect('users')->with('success', '¡Se eliminó con éxito el usuario!');
        }

        return back()->with('error', '¡No puedes borrar tu propio usuario!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        $data = [
            'user'   => $user,
            'roles'  => ['Administrador','Pagador','Usuario','Desactivado'],
            'currentRole' => $user->role

         ];

        return view('users.edit-user')->with($data);
    }

    public function update(Request $request, $id)
    {
        
        $user = User::find($id);

        $emailCheck = ($request->input('email') != '') && ($request->input('email') != $user->email);
        $passwordCheck = $request->input('password') != null;

        $rules = [
            'full_name' => 'required|string|max:255',
            'role'=> 'required',
        ];

        if($emailCheck){      
            $rules['email'] = 'required|string|email|max:255|unique:users';
        }

        if ($passwordCheck) {
            $rules['password'] = 'required|string|min:6|confirmed';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }        

        $user->full_name = $request->input('full_name');
        $user->role = $request->input('role');

        if ($emailCheck) {
            $user->email = $request->input('email');
        }elseif($request->input('email') == '') $user->email = null;

        if ($passwordCheck) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return back()->with('success', '¡Se actualizaron con éxito los datos del usuario!');            
    }



    public function uploadData()
    {
        
        return back()->with('success', '¡Por favor actualizar el sistema para habilitar esta funcion.!');

        if (($handle = fopen ( public_path () . '/correos.csv', 'r' )) !== FALSE) {
            
            while ( ($csv_data = fgetcsv ( $handle, 1000, '|' )) !== FALSE ) {

                //VERIFICAR QUE EL RFC SE ENCUENTRE EN LA LISTA DE LOS TIMBRADOS.
                //VERIFICAR EL REGEX DE EMAIL.
                //VERIFICAR QUE NO EXISTE EL USUARIO Y EL EMAIL ANTES DE GUARDARLO.

                $data = [
                            'name' => trim($csv_data [1]),
                            'email' => strtolower(trim($csv_data [2]))
                        ];

                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                        // invalid emailaddress
                        $data['email'] = null;
                    }

                dispatch(new ProceessUploadData($data));   

            }


        fclose ( $handle );
        }
    }



    


















    
    public function create()
    {
        $roles = [];

        

        if ($this->_rolesEnabled) {
            $roles = config('laravelusers.roleModel')::all();
        }

        $data = [
            'rolesEnabled'  => $this->_rolesEnabled,
            'roles'         => $roles,
        ];

        return view('users.create-user')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name'                  => 'required|string|max:13|unique:users',
            'email'                 => 'required|email|max:255|unique:users',
            'password'              => 'required|string|confirmed|min:6',
            'password_confirmation' => 'required|string|same:password',
        ];

        if ($this->_rolesEnabled) {
            $rules['role'] = 'required';
        }

        $messages = [
            'name.unique'         => trans('laravelusers::laravelusers.messages.userNameTaken'),
            'name.required'       => trans('laravelusers::laravelusers.messages.userNameRequired'),
            'email.required'      => trans('laravelusers::laravelusers.messages.emailRequired'),
            'email.email'         => trans('laravelusers::laravelusers.messages.emailInvalid'),
            'password.required'   => trans('laravelusers::laravelusers.messages.passwordRequired'),
            'password.min'        => trans('laravelusers::laravelusers.messages.PasswordMin'),
            'password.max'        => trans('laravelusers::laravelusers.messages.PasswordMax'),
            'role.required'       => trans('laravelusers::laravelusers.messages.roleRequired'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = App\User::create([
            'name'             => $request->input('name'),
            'email'            => $request->input('email'),
            'password'         => bcrypt($request->input('password')),
        ]);

        if ($this->_rolesEnabled) {
            $user->attachRole($request->input('role'));
            $user->save();
        }

        return redirect('users')->with('success', trans('laravelusers::laravelusers.messages.user-creation-success'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = App\User::find($id);

        return view('users.show-user')->withUser($user);
    }


}
