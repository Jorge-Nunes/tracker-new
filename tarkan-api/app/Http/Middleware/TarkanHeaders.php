<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TarkanHeaders{

    public function handle(Request $request, Closure $next)
    {


        $tarkan = [];

        $tarkan['code'] = $request->header('tarkan-code',0);
        $tarkan['domain']= $request->header('tarkan-domain');
        $tarkan['host'] = $request->header('traccar-host');


        $request->tarkan = $tarkan;


        return $next($request);
    }
}

?>
