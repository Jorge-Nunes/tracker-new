<?php

namespace App\Http\Controllers\OMC;

use App\Http\Controllers\Controller;
use App\Models\TcUsers;
use Illuminate\Http\Request;

class UserController extends Controller{

    public function get(Request $request){
        return response()->json(TcUsers::fromCode($request->tarkan['code'])->get());
    }

    public function put(Request $request,$userId){

        $user = TcUsers::findOrFail($userId);

        $data = $request->all();

        // payload (camelCase) => [coluna real no banco, tipo]
        // schema validado em traccar.tc_users (MySQL 5.7, strict mode)
        $columns = [
            'name'             => ['name',            'string'],
            'email'            => ['email',           'string'],
            'phone'            => ['phone',           'string'],
            'map'              => ['map',             'string'],
            'coordinateFormat' => ['coordinateformat','string'],
            'poiLayer'         => ['poilayer',        'string'],
            'latitude'         => ['latitude',        'float'],
            'longitude'        => ['longitude',       'float'],
            'zoom'             => ['zoom',            'int'],
            'readonly'         => ['readonly',        'bool'],
            'administrator'    => ['administrator',   'bool'],
            'disabled'         => ['disabled',        'bool'],
            'deviceReadonly'   => ['devicereadonly',  'bool'],
            'limitCommands'    => ['limitcommands',   'bool'],
            'deviceLimit'      => ['devicelimit',     'int'],
            'userLimit'        => ['userlimit',       'int'],
            'expirationTime'   => ['expirationtime',  'datetime'],
        ];

        foreach ($columns as $field => [$column, $type]) {
            if (!array_key_exists($field, $data)) {
                continue;
            }

            $value = $data[$field];

            switch ($type) {
                case 'bool':
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
                    break;
                case 'int':
                    $value = intval($value);
                    break;
                case 'float':
                    $value = floatval($value);
                    break;
                case 'datetime':
                    $ts = is_string($value) ? strtotime($value) : false;
                    $value = ($value === null || $value === '' || $ts === false) ? null : date('Y-m-d H:i:s', $ts);
                    break;
                default:
                    // colunas NOT NULL (name/email) não recebem null
                    if ($value === null) {
                        continue 2;
                    }
                    $value = (string)$value;
                    break;
            }

            $user->{$column} = $value;
        }

        // senha: hash pbkdf2 via setPasswordAttribute; não sobrescreve quando vazia
        if (array_key_exists('password', $data) && !empty($data['password'])) {
            $user->password = $data['password'];
        }

        // atributos (JSON em varchar(4000))
        if (array_key_exists('attributes', $data)) {
            $attributes = $data['attributes'];
            if ($attributes === null || (is_array($attributes) && count($attributes) === 0)) {
                $attributes = (object)null;
            }
            $user->attributes = $attributes;
        }

        // token não é coluna na tabela tc_users (validado) — vive em attributes (padrão dos shares)
        if (array_key_exists('token', $data) && $data['token'] !== null && $data['token'] !== '') {
            $attributes = is_array($user->attributes) ? $user->attributes : [];
            $attributes['token'] = $data['token'];
            $user->attributes = $attributes;
        }

        // ownerId: assumido pelo model (scopeFromCode); aplicado apenas se enviado no payload
        if (array_key_exists('ownerId', $data)) {
            $user->ownerId = intval($data['ownerId']);
        }

        $user->save();

        return response()->json($user);
    }

    public function delete(Request $request,$shareId){

        $user = TcUsers::findOrFail($shareId);

        $user->delete();

        return response()->json([],204);
    }

}
