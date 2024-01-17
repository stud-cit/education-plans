<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Http\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\ExternalServices\Asu\Department;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EnsureCabinetTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $cabinetUrl = config('app.cabinet_app_url') . 'api/getPerson';
        $params = [
            'key' => $request->header('Authorization'),
            'token' => config('app.cabinet_app_token'),
        ];

        $response = Http::retry(3, 100)->get($cabinetUrl, $params)->json();

        if ($response['status'] === 'OK') {
            $user = $response['result'];

            $model = User::select(
                'id',
                'asu_id', // Do not delete, it is used for logging
                'name',
                'faculty_id',
                // 'faculty_name',
                'department_id',
                // 'department_name',
                'email', // Чи треба нам емейл
                'role_id'
            )->where("asu_id", $user['guid'])->first();
            clock($model);
            if ($model) {
                $new = [
                    'name' => "{$user['surname']} {$user['name']} {$user['patronymic']}",
                    'email' => $user['email'],
                ];

                if ($this->isArrayDiffByKey($model->toArray(), $new, array_keys($new))) {
                    $model->update($new);
                    $model->refresh();
                }

                Auth::check() ?: Auth::login($model);

                return $next($request);
            } else {
                $user = $response['result'];

                $newUser = User::create([
                    'name' => "{$user['surname']} {$user['name']} {$user['patronymic']}",
                    'asu_id' => $user['guid'],
                    'email' => $user['email']
                ]);

                Auth::login($newUser);

                return $next($request);
                //return response(['message' => __('auth.not_allowed_user')], 403);
            }
        } else if (in_array($response['status'], Constant::ASU_ERRORS)) {

            return response(['message' => "Cabinet: {$response['result']}"], 401);
        } else {
            throw new HttpException(500, $response);
        }
    }

    public static function isArrayDiffByKey($array1, $array2, $byKeys): bool
    {
        $status = true;
        foreach ($byKeys as $key) {
            if (array_key_exists($key, $array1) && array_key_exists($key, $array2) && $array1[$key] !== $array2[$key]) {
                $status = false;
            }
        }

        return !$status;
    }
}
