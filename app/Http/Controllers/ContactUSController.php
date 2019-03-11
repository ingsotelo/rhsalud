<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Mail\SendEmail;
use Mail;


class ContactUSController extends Controller
{

	public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    public function contactUSPost(Request $request)
    {

      $request->validate(['message' => 'required']);
/*
      $users = \DB::table('users')
                ->where('send', null)
                ->where('email','<>',null)->get();

      foreach ($users as $user) {
        $file = public_path() . '/' .$user->paysheet.'/'.$user->cr.'/'.$user->file;

        $file = str_replace(".xml", "", $file);  ////////////


        $details = ['email' => $user->email,
              'route_view' => 'emails.contact',
              'subject' => 'Recibo de Nómina',
              'files' => [$file.'.xml',$file.'.pdf']
             ];
        try{
        $email = new SendEmail($details);
        Mail::to($details['email'])->send($email);

        \DB::table('users')
            ->where('id', $user->id)
            ->update([
                        'send' => 1
                    ]);
        }
        catch(\Exception $e){
          echo $e;
        }

      }

*/
      return back()->with('success', '¡Gracias por sus comentarios!');

    }

    public function contactUS()
    {
    	return view('contactUS'); 
    }
}