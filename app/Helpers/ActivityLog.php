<?php


namespace App\Helpers;
use Request;
use App\Models\UserActivityLog;
use App\Models\User;
use Session;



class ActivityLog
{


    public static function addToLog($operation) {

    	$log = [];
        $user = Session::get('person');

        if(!$user){
            return response('no auth user');
        }

        $userModel = User::where('asu_id', $user['guid'])->with('role')->first();
        $log['asu_id'] = $userModel['asu_id'];
    	$log['user_name'] = $userModel['name'];
    	$log['user_role'] = Request::fullUrl();
    	$log['operation'] = $operation;
    	$log['ip'] = Request::ip();
    	
    	UserActivityLog::create($log);
    }


    public static function logActivityLists()
    {
    	return UserActivityLog::all()->get();
    }


}