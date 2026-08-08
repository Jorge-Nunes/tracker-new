<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use App\Tarkan\traccarConnector;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class ReportsController extends Controller{

    public function getTripPoints($traccar,$trip){


        $params = "deviceId=".$trip['deviceId']."&from=".date('c',strtotime($trip['startTime']))."&to=".date('c',strtotime($trip['endTime']));


        $points = $traccar->getRoute($params);

        if($points->status()===200){
            $tmp = [];

            foreach($points->json() as $point){
                $tmp[] = $point['latitude'].",".$point['longitude'];
            }

            return implode("|",$tmp);
        }else{
            return [];
        }
    }

    public function getSummary(Request $request){
        $traccar = new traccarConnector($request);

        $resume = $traccar->getSummary($_SERVER['QUERY_STRING'],['h' => ['Cookie' => $request->headers->get('cookie')]]);

        if($request->header('Accept')=='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
            if($resume->status()===200) {

                $query  = explode('&', $_SERVER['QUERY_STRING']);
                $params = array();

                foreach($query as $param){
                    list($name, $value) = explode('=', $param);
                    $params[urldecode($name)][] = urldecode($value);
                }

                $_devices = [];
                $_trips = [];

                $devices = $traccar->getDevice($params['deviceId'],['h' => ['Cookie' => $request->headers->get('cookie')]]);
                foreach($devices->json() as $device){
                    $_devices[$device['id']] = $device;
                    $_trips[$device['id']] = [];
                }


                $trips = $traccar->getTrips($_SERVER['QUERY_STRING'],['h' => ['Cookie' => $request->headers->get('cookie')]]);

                foreach($trips->json() as $trip){
                    if(!IsSet($_trips[$trip['deviceId']])){
                        $_trips[$trip['deviceId']] = [];
                    }

                    $trip['points'] = $this->getTripPoints($traccar,$trip);

                    $trip['points'];

                    $_trips[$trip['deviceId']][] = $trip;
                }



                //return view('resume',['devices'=>$_devices,'resumes'=>$resume->json(),'trips'=>$_trips]);
                return PDF::loadView('resume',['devices'=>$_devices,'resumes'=>$resume->json(),'trips'=>$_trips])->download();
            }else{
                return response('error loading summary',503);
            }
        }else {
            return response($resume->body(), $resume->status());
        }
    }


}

