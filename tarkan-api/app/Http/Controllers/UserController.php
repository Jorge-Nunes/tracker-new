<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use Illuminate\Http\Request;

class UserController extends Controller{

    public function post(Request $request){
        $auth = self::authedTraccar($request);
        if($auth === false){
            return response('User not authed', 503);
        }

        [$traccar, $me] = $auth;

        $data = $request->all();
        if (isset($data['attributes']) && is_array($data['attributes']) && count($data['attributes'])==0) {
            $data['attributes'] = (object)null;
        }


        $notifications = $traccar->getAllNotifications();

        $user = $traccar->createUser($data, self::cookieAuth($request));
        $userData = $user->json();


        foreach($notifications->json() as $n){
            if(isset($n['attributes']['tarkan.autoadd']) && $n['attributes']['tarkan.autoadd']==true){
                $traccar->linkObjects(['userId'=>$userData['id'],'notificationId'=>$n['id']]);
            }
        }


        UserLog::record($request, $me['email'], 101, $user->status(), [
            'object' => ['userId' => (isset($request->userId)) ? intval($request->userId) : 0],
            'data' => $request->all()
        ]);

        return response($user->body(), $user->status());

    }

    public function put(Request $request){
        $auth = self::authedTraccar($request);
        if($auth === false){
            return response('User not authed', 503);
        }

        [$traccar, $me] = $auth;

        $old = $traccar->getUsers($request->userId);

        $data = $request->all();
        if (isset($data['attributes']) && is_array($data['attributes']) && count($data['attributes'])==0) {
            $data['attributes'] = (object)null;
        }

        $user = $traccar->updateUser($request->userId, $data, self::cookieAuth($request));

        UserLog::record($request, $me['email'], 105, $user->status(), [
            'object' => ['userId' => (isset($request->userId)) ? intval($request->userId) : 0],
            'old' => $old->json(),
            'data' => $request->all()
        ]);

        return response($user->body(), $user->status());

    }


    public function delete(Request $request){
        $auth = self::authedTraccar($request);
        if($auth === false){
            return response('User not authed', 503);
        }

        [$traccar, $me] = $auth;

        $getDevices = $traccar->getUsers($request->input('shareId'), self::cookieAuth($request));
        if($getDevices->status()!==200){
            return response()->json([],404);
        }

        $deleteUser = $traccar->deleteUser($request->shareId);

        UserLog::record($request, $me['email'], 102, $deleteUser->status(), [
            'object' => ['userId' => (isset($request->userId)) ? intval($request->userId) : 0]
        ]);

        if($deleteUser->status()===204){
            return response()->json(['success'=>true]);
        }

        return response()->json($deleteUser->body(),401);
    }


}
