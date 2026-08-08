<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FixTarkan{

    public function handle(Request $request, Closure $next)
    {
        $isTarkan = $request->segment(1); // one based index!


        if (IsSet($isTarkan) && $isTarkan==='tarkan') { // redirect '/' to default locale

            //dd($request);

            $segments = $request->segments();
            $first = array_shift($segments);


            $request->merge(['requestUri'=>'/version']);


        }


        return $next($request);
    }
}

?>
