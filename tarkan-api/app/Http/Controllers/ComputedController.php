<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use App\Tarkan\traccarConnector;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class ComputedController extends Controller{

    public static function checkComputed(Request $request){

        $traccar = new traccarConnector($request);
        $list = $traccar->getComputed();

        $names = [];
        $response = [];

        foreach($list->json() as $a){
            $names[] = $a['description'];
            if(substr($a['description'],0,7)=='tarkan.'){
                $response[$a['description']] = $a['id'];
            }
        }

        if(!in_array("tarkan.QRDriverID",$names)){
            $c = $traccar->createComputed(["id"=>0,"description"=>"tarkan.QRDriverID","attribute"=>"driverUniqueId","expression"=>"qrDriverId?qrDriverId:null","type"=>"string"]);

            if($c->status()==200){
                $d = $c->json();
                $response[$d['description']] = $d['id'];
            }
        }



        if(!in_array("tarkan.QRLockInfo",$names)){
            $c = $traccar->createComputed(["id"=>0,"description"=>"tarkan.QRLockInfo","attribute"=>"isQrLocked","expression"=>"blocked && isQrLocked","type"=>"boolean"]);

            if($c->status()==200){
                $d = $c->json();
                $response[$d['description']] = $d['id'];
            }
        }



        if(!in_array("tarkan.QRCheckAlarm",$names)){
            $c = $traccar->createComputed(["id"=>0,"description"=>"tarkan.QRCheckAlarm","attribute"=>"alarm","expression"=>"(qrCheckoutTime)?((serverTime.getTime()>qrCheckoutTime)?'checkoutDriver':null):null","type"=>"string"]);

            if($c->status()==200){
                $d = $c->json();
                $response[$d['description']] = $d['id'];
            }
        }



        if(!in_array("tarkan.QRLockAlarm",$names)){
            $c = $traccar->createComputed(["id"=>0,"description"=>"tarkan.QRLockAlarm","attribute"=>"alarm","expression"=>"(qrLockTime)?((qrDriverId)?null:((serverTime.getTime()>qrLockTime)?'unknownDriver':null)):null","type"=>"string"]);

            if($c->status()==200){
                $d = $c->json();
                $response[$d['description']] = $d['id'];
            }
        }

        return $response;
    }


}

