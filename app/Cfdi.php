<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cfdi extends Model
{
    public $table = 'cfdis';
 
	public $fillable = ['name','xml'];
}
