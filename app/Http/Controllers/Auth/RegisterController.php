<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        Validator::extend('chkemail', function ($attribute, $value, $parameters, $validator) {
            $name = array_get($validator->getData(), $parameters[0], null);
            $user = User::where('name', $name)->first();
            return  $value == $user->email;
        });

        return Validator::make($data, [
            'name' => ['required', 'string', 'max:13', 'exists:users',
                function ($attribute, $value, $fail) {
                    $user = User::where('name', $value)->first();
                    if ($user->email_verified_at != null) {
                        $fail('Este RFC ya se encuentra registrado.');
                    }
                },         
            ],
            'email' => ['required','string', 'email', 'max:255', 'exists:users', 'chkemail:name'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'captcha' => 'required|captcha'],
        [
            'captcha' => 'El captcha ingresado es incorrecto.',
            'chkemail' => 'El correo ingresado no corresponde al RFC'
        ]);

    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {

        $user =  User::where('name', $data['name'])->first();
        $user->password = Hash::make($data['password']);
        $user->save();
        return $user;
    }

    
}
