<?php

use App\Http\Controllers\CommandsController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GeofenceController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\Master\RegisterReportingsController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\DriverController;
use App\Models\TcDevices;
use App\Models\TcDeviceDriver;
use App\Models\UserLog;
use App\Tarkan\traccarConnector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/




Route::get("/version",function(Request $request){

    return response()->json(['version'=>'1.2.0']);
});

Route::group(['prefix'=>'clear'],function(){
    Route::get('/shares',[ShareController::class,'clearShare']);
    Route::get('/logs',[LogsController::class,'clearLogs']);

});

Route::group(['prefix'=>'master'],function(){
    Route::get('registerReportings',[RegisterReportingsController::class,'get']);
    Route::get('loadReportings',[RegisterReportingsController::class,'getAverage']);
});

Route::group(['prefix'=>'webhooks'],function() {
    Route::post("/event", [EventController::class,'handleEvent']);
});

Route::get('/pdf',function(){
    return PDF::loadView('resume')
        // Se quiser que fique no formato a4 retrato: ->setPaper('a4', 'landscape')
        ->download('nome-arquivo-pdf-gerado.pdf');

    //return view('resume');
});

Route::group(['prefix'=>'devices'],function(){
    Route::get("/",function(){
        return response()->json(['id'=>1]);
    });


    Route::post("/{deviceId}/photo",[DeviceController::class,'uploadImage']);
    Route::get('/{deviceId}/logs', function (Request $request) {
        return response()->json(UserLog::where(function($query) use ($request){ $query->where(['serverHost'=>$request->header('tarkan-domain')])->orWhere(['serverHost'=>'localhost']); })->whereRaw('JSON_CONTAINS(log->"$.object", \'{"deviceId":'.intval($request->deviceId).'}\')')->orderBy('created_at')->get());
    });
});

Route::group(['prefix'=>'qr-driver'],function(){
    Route::post("/",[DriverController::class,'checkDriver']);
});


Route::post("/autolink",[DeviceController::class,'autoLink']);

Route::group(['prefix'=>'server'],function(){

    Route::get('/logs', function (Request $request) {
        return response()->json(UserLog::where(function($query) use ($request){
            $query->where(['serverHost'=>$request->header('tarkan-domain')])->orWhere(['serverHost'=>'localhost']);
        })->orderBy('created_at')->get());
    });
});




Route::group(['prefix'=>'users'],function() {
    Route::get('{userId}/logs', function (Request $request) {

        $traccar = new traccarConnector($request);

        $user = $traccar->getUsers($request->userId);
        if($user->status()===200){
            return response()->json(UserLog::where(['serverHost'=>$request->header('tarkan-domain'),'username' => $user['email']])->orderBy('created_at')->get());
        }else{
            return response($user->body(),$user->status());
        }


    });
});

Route::group(['prefix'=>'shares'],function() {
    Route::get('/',[ShareController::class,'getShares']);
    Route::post('/',[ShareController::class,'createShare']);
    Route::put('/{shareId}',[ShareController::class,'updateShare']);
    Route::delete('/{shareId}',[ShareController::class,'deleteShare']);
});

Route::put("/theme",function(Request $request){

    $baseStorage = storage_path('app/');


    $conf = json_encode($request->input('config'));
    $colors = json_encode($request->input('colors'));

    $domain = $request->header('tarkan-domain');


    $manifest = json_decode('{"name":"Tarkan","short_name":"Tarkan","theme_color":"#05a7e3","icons":[{"src":"./icons/android-chrome-192x192.png","sizes":"192x192","type":"image/png"},{"src":"./icons/android-chrome-512x512.png","sizes":"512x512","type":"image/png"},{"src":"./icons/android-chrome-maskable-192x192.png","sizes":"192x192","type":"image/png","purpose":"maskable"},{"src":"./icons/android-chrome-maskable-512x512.png","sizes":"512x512","type":"image/png","purpose":"maskable"}],"start_url":"../../../","display":"standalone","background_color":"#000000"}',true);

    $manifest['name'] = $request->json('config')['title'];
    $manifest['short_name'] = $request->json('config')['title'];
    $manifest['theme_color'] = $request->json('colors')['--el-color-primary'];

    $conffile = "const CONFIG=".$conf.";";

    $colorfile = "const defaultThemeData =".$colors.";";
    $colorfile.= "const initTheme = ()=>{  let tmp = []; for(var v of Object.keys(defaultThemeData)){ tmp.push(v+':'+defaultThemeData[v]+';'); } document.querySelector(\":root\").style=tmp.join(\"\");}; window.addEventListener(\"load\",initTheme());";


    if(!Storage::exists('assets/'.$request->ip().'/'.$domain.'/assets/custom/')){

        mkdir($baseStorage.'assets/'.$request->ip().'/'.$domain.'/assets/custom/',0777,true);
    }




    Storage::put('assets/'.$request->ip().'/'.$domain.'/assets/custom/colors.js',$colorfile);
    Storage::put('assets/'.$request->ip().'/'.$domain.'/assets/custom/config.js',$conffile);
    Storage::put('assets/'.$request->ip().'/'.$domain.'/assets/custom/manifest.json',json_encode($manifest));

    return response()->json([]);
});

Route::post("/theme/upload",function(Request $request){


    $domain = $request->header('tarkan-domain');

    $assetsDir = 'assets/'.$request->ip().'/'.$domain.'/assets/custom';
    $baseDir = storage_path(). '/app/'.$assetsDir;

    if(!file_exists($baseDir)){
        mkdir($baseDir,0777,true);
        mkdir($baseDir.'/icons',0777,true);
    }

    if($request->type==='fav-icon') {

        $path = $baseDir.'/icons/';

        if(!file_exists($path)) {
            mkdir($path,0777,true);
        }

        Image::make($request->file('file'))->fit('512', '512')->save($path."android-chrome-512x512.png")->encode('png', 100);
        Image::make($request->file('file'))->fit('192', '192')->save($path."android-chrome-192x192.png")->encode('png', 100);


        Image::make($request->file('file'))->fit('192', '192')->save($path."android-chrome-maskable-192x192.png")->encode('png', 100);
        Image::make($request->file('file'))->fit('512', '512')->save($path."android-chrome-maskable-512x512.png")->encode('png', 100);

        Image::make($request->file('file'))->fit('180', '180')->save($path."apple-touch-icon.png")->encode('png', 100);
        Image::make($request->file('file'))->fit('60', '60')->save($path."apple-touch-icon-60x60.png")->encode('png', 100);
        Image::make($request->file('file'))->fit('76', '76')->save($path."apple-touch-icon-76x76.png")->encode('png', 100);
        Image::make($request->file('file'))->fit('120', '120')->save($path."apple-touch-icon-120x120.png")->encode('png', 100);
        Image::make($request->file('file'))->fit('152', '152')->save($path."apple-touch-icon-152x152.png")->encode('png', 100);
        Image::make($request->file('file'))->fit('180', '180')->save($path."apple-touch-icon-180x180.png")->encode('png', 100);

        Image::make($request->file('file'))->fit('16', '16')->save($path."favicon-16x16.png")->encode('png', 100);
        Image::make($request->file('file'))->fit('32', '32')->save($path."favicon-32x32.png")->encode('png', 100);
        Image::make($request->file('file'))->fit('144', '144')->save($path."msapplication-icon-144x144.png")->encode('png', 100);
        Image::make($request->file('file'))->fit('150', '150')->save($path."mstile-150x150.png")->encode('png', 100);

    }else if($request->type==='bg-login') {
        $path = $baseDir.'/bg.jpg';
        Image::make($request->file('file'))->fit('1600', '900')->save($path)->encode('jpg', 80);
    }else if($request->type==='logo-login'){
        $path = $assetsDir;
        Storage::putFileAs($path,$request->file('file'),'logoWhite.png');
    }else if($request->type==='logo-interno'){
        $path = $assetsDir;
        Storage::putFileAs($path,$request->file('file'),'logo.png');
    }


    return response()->json();
});


Route::group(['prefix'=>'api'],function(){


    Route::get("/server",[ServerController::class,'get']);
    Route::put("/server",[ServerController::class,'put']);
    Route::post("/server/restart",[ServerController::class,'restartServer']);
    Route::post('/session',[SessionController::class,'post']);

    /*
    Route::group(['prefix'=>'attributes'],function(){
        Route::group(['prefix'=>'computed'],function() {
            Route::post('/', [GeofenceController::class, 'post']);
            Route::delete('/{geofenceId}', [GeofenceController::class, 'delete']);
        });
    });*/

    Route::group(['prefix'=>'geofences'],function(){
        Route::post('/',[GeofenceController::class,'post']);
        Route::delete('/{geofenceId}',[GeofenceController::class,'delete']);
    });

    Route::group(['prefix'=>'devices'],function(){
        Route::post("/",[DeviceController::class,'post']);
        Route::put("/{deviceId}",[DeviceController::class,'put']);
        Route::delete("/{deviceId}",[DeviceController::class,'delete']);
    });

    Route::group(['prefix'=>'drivers'],function(){
        Route::post("/",[DriverController::class,'post']);
        Route::put("/{driverId}",[DriverController::class,'put']);
    });

    Route::group(['prefix'=>'users'],function(){
        if(env('ENABLE_DB_INTRUSIVE',false)){
            Route::get('/', [App\Http\Controllers\OMC\UserController::class, 'get']);
            Route::put('/{userId}', [App\Http\Controllers\OMC\UserController::class, 'put']);
            Route::delete("/{shareId}", [App\Http\Controllers\OMC\UserController::class, 'delete']);
        }else {
            Route::post('/', [App\Http\Controllers\UserController::class, 'post']);
            Route::put('/{userId}', [App\Http\Controllers\UserController::class, 'put']);
            Route::delete("/{shareId}", [App\Http\Controllers\UserController::class, 'delete']);
        }
    });


    Route::group(['prefix'=>'permissions'],function() {

        Route::get('/',[PermissionsController::class,'get']);
        Route::delete('/',[PermissionsController::class,'delete']);
        Route::post('/',[PermissionsController::class,'post']);
        Route::post('/bulk',[PermissionsController::class,'postBulk']);
        Route::delete('/bulk',[PermissionsController::class,'deleteBulk']);
    });

    Route::group(['prefix'=>'commands'],function() {
        Route::post("/send",[CommandsController::class,'send']);
    });


    Route::group(['prefix'=>'reports'],function(){
            //Route::get("summary",[ReportsController::class,'getSummary']);
    });


    Route::fallback(function(Request $request){
        return response()->json(['code'=>404,'error'=>'What are you looking for?','uri'=>$request->url()],405);
    });
});

Route::fallback(function(Request $request){
    return response()->json(['code'=>404,'error'=>'What are you looking for?','uri'=>$request->url()],404);
});
