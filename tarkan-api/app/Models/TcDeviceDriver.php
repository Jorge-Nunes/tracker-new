<?php

namespace App\Models;


use App\Casts\Json;
use Illuminate\Database\Eloquent\Model;

class TcDeviceDriver extends Model{

    protected $table = "tc_device_driver";
    protected $connection = 'mysql_traccar';

}
