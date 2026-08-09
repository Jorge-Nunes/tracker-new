<?php

namespace App\Http\Controllers;

use App\Tarkan\traccarConnector;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Cria o connector do Traccar a partir do request (headers de runtime).
     */
    protected static function traccar(Request $request): traccarConnector
    {
        return new traccarConnector($request);
    }

    /**
     * Auth por cookie de sessão do cliente logado.
     */
    protected static function cookieAuth(Request $request): array
    {
        return ['h' => ['Cookie' => $request->headers->get('cookie')]];
    }

    /**
     * Retorna [$traccar, $me] com a sessão autenticada ou false se não autenticado.
     */
    protected static function authedTraccar(Request $request)
    {
        $traccar = new traccarConnector($request);
        $me = $traccar->getSession(self::cookieAuth($request));

        if ($me->status() !== 200) {
            return false;
        }

        return [$traccar, $me];
    }
}
