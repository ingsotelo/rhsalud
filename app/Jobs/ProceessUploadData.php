<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Hash;


class ProceessUploadData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        //
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        /*User::create([
            'name' => $this->data['name'],
            'full_name' => $this->data['full_name'],
            'paysheet' => $this->data['paysheet'],
            'password' => Hash::make(str_random(10)),
        ]);*/


        \DB::table('users')
            ->where('name', $this->data['name'])
            ->update([
                        'email' => $this->data['email']
                    ]);
    }
}
