<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ThemeController extends Controller{

    public function put(Request $request){

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
    }

    public function upload(Request $request){

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

            $icons = [
                "android-chrome-512x512.png" => [512, 512],
                "android-chrome-192x192.png" => [192, 192],
                "android-chrome-maskable-192x192.png" => [192, 192],
                "android-chrome-maskable-512x512.png" => [512, 512],
                "apple-touch-icon.png" => [180, 180],
                "apple-touch-icon-60x60.png" => [60, 60],
                "apple-touch-icon-76x76.png" => [76, 76],
                "apple-touch-icon-120x120.png" => [120, 120],
                "apple-touch-icon-152x152.png" => [152, 152],
                "apple-touch-icon-180x180.png" => [180, 180],
                "favicon-16x16.png" => [16, 16],
                "favicon-32x32.png" => [32, 32],
                "msapplication-icon-144x144.png" => [144, 144],
                "mstile-150x150.png" => [150, 150]
            ];

            foreach ($icons as $name => [$width, $height]) {
                Image::make($request->file('file'))->fit($width, $height)->save($path.$name)->encode('png', 100);
            }

        }else if($request->type==='bg-login') {
            $path = $baseDir.'/bg.jpg';
            Image::make($request->file('file'))->fit('1600', '900')->save($path)->encode('jpg', 80);
        }else if($request->type==='logo-login'){
            Storage::putFileAs($assetsDir,$request->file('file'),'logoWhite.png');
        }else if($request->type==='logo-interno'){
            Storage::putFileAs($assetsDir,$request->file('file'),'logo.png');
        }

        return response()->json();
    }

}
