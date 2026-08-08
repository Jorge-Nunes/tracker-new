<?php


namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\DeviceReportings;
use App\Models\UserLog;
use App\Tarkan\traccarConnector;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class RegisterReportingsController extends Controller{


    public function get(Request $request){

        $traccar = new traccarConnector($request);
        $devices = $traccar->getDevices(["all"=>true])->json();

        $R = ['all'=>count($devices),'online'=>0,'offline'=>0,'unknown'=>0,'motion'=>0];

        foreach($devices as $device){

            if($device['status']==='online'){
                $R['online']++;
            }else if($device['status']==='offline'){
                $R['offline']++;
            }else if($device['status']==='unknown'){
                $R['unknown']++;
            }

        }

        DeviceReportings::create($R);

        //dd(Carbon::now()->subHours(24));

        DeviceReportings::where( 'created_at', '<=', Carbon::now()->subHours(24))->delete();


        return response()->json($R);
    }


    public function getAverage(Request $request){

        //SELECT AVG(`all`) as avgAll,AVG(`online`) as avgOnline,AVG(`offline`) as avgOffline,AVG(`unknown`) as avgUnknown,DATE_FORMAT(`created_at`,"%H-%d") as avgHOUR FROM `device_reportings` GROUP BY DATE_FORMAT(`created_at`,"%H-%d")


        $q = DeviceReportings::selectRaw("AVG(`all`) as avgAll,AVG(`online`) as avgOnline,AVG(`offline`) as avgOffline,AVG(`unknown`) as avgUnknown,DATE_FORMAT(`created_at`,'%m-%d-%H') as avgHOUR")->groupByRaw("DATE_FORMAT(`created_at`,'%m-%d-%H')")->orderBy('avgHOUR','DESC')->get();


        $l = UserLog::orderBy('id','desc')->limit(10)->get();

            return response()->json(["data"=>$q,"logs"=>$l]);
    }


}
