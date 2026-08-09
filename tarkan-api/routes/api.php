<?php

use App\Http\Controllers\CommandsController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GeofenceController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\Master\RegisterReportingsController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\ThemeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    Route::get('/{deviceId}/logs', [LogsController::class,'deviceLogs']);
});

Route::group(['prefix'=>'qr-driver'],function(){
    Route::post("/",[DriverController::class,'checkDriver']);
});


Route::post("/autolink",[DeviceController::class,'autoLink']);

Route::group(['prefix'=>'server'],function(){
    Route::get('/logs', [LogsController::class,'serverLogs']);
});

Route::group(['prefix'=>'users'],function() {
    Route::get('{userId}/logs', [LogsController::class,'userLogs']);
});

Route::group(['prefix'=>'shares'],function() {
    Route::get('/',[ShareController::class,'getShares']);
    Route::post('/',[ShareController::class,'createShare']);
    Route::put('/{shareId}',[ShareController::class,'updateShare']);
    Route::delete('/{shareId}',[ShareController::class,'deleteShare']);
});

Route::put("/theme",[ThemeController::class,'put']);

Route::post("/theme/upload",[ThemeController::class,'upload']);


Route::group(['prefix'=>'api'],function(){


    Route::get("/server",[ServerController::class,'get']);
    Route::put("/server",[ServerController::class,'put']);
    Route::post("/server/restart",[ServerController::class,'restartServer']);
    Route::post('/session',[SessionController::class,'post']);

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
