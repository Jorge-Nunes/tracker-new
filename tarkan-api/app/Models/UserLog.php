<?php

namespace App\Models;


use App\Casts\Json;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sesId',
        'serverIp',
        'serverHost',
        'username',
        'userIp',
        'userAgent',
        'log'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id'
    ];

    protected $casts = [
        'log'=> Json::class
    ];

}
