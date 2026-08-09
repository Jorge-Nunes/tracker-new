<?php

namespace App\Models;


use App\Casts\Json;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UserLog extends Model{

    /**
     * Registra um log de usuário padronizado a partir do request.
     *
     * $extra pode conter: sesId, username, userIp, userAgent, object, data, old, command, native
     */
    public static function record(Request $request, string $username, int $code, $status, array $extra = [])
    {
        $log = [
            'code' => $code,
            'object' => $extra['object'] ?? [],
            'status' => $status,
        ];

        foreach (['data', 'old', 'command', 'native'] as $key) {
            if (array_key_exists($key, $extra)) {
                $log[$key] = $extra[$key];
            }
        }

        return static::create([
            'sesId' => $extra['sesId'] ?? $request->cookie('JSESSIONID'),
            'serverIp' => $request->ip(),
            'serverHost' => $request->header('tarkan-domain'),
            'username' => $extra['username'] ?? $username,
            'userIp' => $extra['userIp'] ?? $request->header('x-real-ip'),
            'userAgent' => $extra['userAgent'] ?? $request->userAgent(),
            'log' => $log,
        ]);
    }

    /**
     * Escopo para filtrar logs pelo domínio do cliente (com fallback localhost).
     */
    public function scopeForDomain($query, $domain)
    {
        return $query->where(function ($q) use ($domain) {
            $q->where('serverHost', $domain)->orWhere('serverHost', 'localhost');
        });
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sesId',
        'serverIp',
        'serverHost',
        'username',
        'userIp',
        'userAgent',
        'log'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id'
    ];

    protected $casts = [
        'log'=> Json::class
    ];

}
