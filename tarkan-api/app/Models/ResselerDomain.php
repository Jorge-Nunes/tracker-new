<?php

namespace App\Models;


use App\Casts\Json;
use Illuminate\Database\Eloquent\Model;

class ResselerDomain extends Model{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ownerId',
        'userId',
        'limitDomains',
        'domainName'
    ];


    protected $casts = [
        'log'=> Json::class
    ];

}
