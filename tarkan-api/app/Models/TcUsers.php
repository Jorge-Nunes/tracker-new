<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class TcUsers extends Model{

    protected $connection = 'mysql_traccar';

    public $timestamps = false;
    protected $fillable = ['name','email','password','ownerId'];

    protected $hidden = ['hashedpassword','salt'];

    public function getPasswordAttribute()
    {
        return null;
    }

    public function setPasswordAttribute($password){


        $iv = random_bytes(24);

        $key = substr(hash_pbkdf2('sha1', $password, $iv, 1000, 24 * 24),0,48);

        $this->attributes['salt'] =bin2hex($iv);
        $this->attributes['hashedpassword'] = $key;
    }

    protected $appends = ['password'];

    protected $casts = [
        'attributes' => Json::class,
    ];


    public function scopeFromCode($query,$codeId){
        return $query->where('ownerId',$codeId==0?'>=':'=',$codeId);
    }




    public function validatePassword($password){
        $iv = hex2bin($this->attributes['salt']);
        $key = substr(hash_pbkdf2('sha1', $password, $iv, 1000, 24 * 24),0,48);

        return $key == $this->attributes['hashedpassword'];
    }
}
