<?php

namespace App\Http\Controllers;

use App\Http\Constant;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\IndexLogRequest;
use Illuminate\Support\Facades\Request;
use App\Http\Resources\UserActivityResource;

class UserActivityController extends Controller
{
    public function index(IndexLogRequest $request)
    {
        $validated = $request->validated();
        $perPage = array_key_exists('items_per_page', $validated) ? $validated['items_per_page'] : Constant::PAGINATE;

        $logs = UserActivityLog::select('id', 'name', 'role', 'ip', 'operation', 'model', 'data', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        return UserActivityResource::collection($logs);
    }

    public static function addToLog($operation, $model, $data = null)
    {
        $user = Auth::user();

        $log = [];
        $log['asu_id'] = $user['asu_id'];
        $log['name'] = $user['name'];
        $log['role'] = $user['role']['label'];
        $log['operation'] = $operation;
        $log['model'] = $model;
        $log['ip'] = Request::ip();
        $log['data'] = $data;

        UserActivityLog::create($log);
    }
}
