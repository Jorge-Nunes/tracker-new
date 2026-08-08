<?php

namespace App\Models;


use App\Casts\Json;
use Illuminate\Database\Eloquent\Model;

class DeviceReportings extends Model{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'all',
        'online',
        'offline',
        'unknown',
        'motion',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id'
    ];



}
