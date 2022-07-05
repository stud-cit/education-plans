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
        $params = [
            'key' => $request->header('Authorization'),
            'token' => config('app.cabinet_app_token'),
        ];

        clock("Authorization: {$params['key']}");
        clock("Token: {$params['token']}");

        $response = Http::retry(3, 100)->get('https://cabinet.sumdu.edu.ua/api/getPersonInfo', $params)->json();
        clock($response);

        if($response['status'] === 'OK') {
            clock("response status {$response['status']}");
            $user = $response['result'];

            $model = User::where("asu_id", $user['guid'])->first();
            clock("model", $model->toArray());

            if($model) {
                $asu = new Department();
                $divisions = $asu->getDepartmentInfoByUser($user);

                $new = [
                    'name' => "{$user['surname']} {$user['name']} {$user['patronymic']}",
                    'faculty_id' => $divisions['faculty_id'],
                    'faculty_name' => $divisions['faculty_name'],
                    'department_id' => $divisions['department_id'],
                    'department_name' => $divisions['department_name'],
                    'email' => $user['email'],
                ];

                if ($this->isArrayDiffByKey($model->toArray(), $new, array_keys($new)))  {
                    $model->update($new);
                    $model->refresh();
                }

                Auth::check() ?: Auth::login($model);
                clock('cabinet', (bool)Auth::check());

                return $next($request);
            } else {
              return response(['message' => __('auth.not_allowed_user')], 403);
            }
          } else if (in_array($response['status'], Constant::ASU_ERRORS) ) {
            return response(['message' => "Cabinet: {$response['result']}"], 401);
        } else {
            throw new HttpException ( 500, $response);
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
