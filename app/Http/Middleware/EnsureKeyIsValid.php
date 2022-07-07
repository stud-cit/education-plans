<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EnsureKeyIsValid
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

        if ($params['key'] == null) {
            return response(['message' => 'Key empty'])->statusCode(500);
        }

        $response = Http::retry(3, 100)->get('https://cabinet.sumdu.edu.ua/api/getPersonInfo', $params)->json();
        clock($response);

        if($response['status'] === 'OK') {
            $user = $response['result'];

            $model = User::where("asu_id", $user['guid'])->first();

            clock("model", $model ? $model->toArray() : 'user not found');

            if($model) {

                if (empty($model->tokens)) {
                    $model->createToken('admin')->plainTextToken();
                    return $next($request);
                } else {
                    clock("tokens", explode(" ", $model->tokens));
                    return $next($request);
                    // foreach ($model->tokens as $token) {
                    //     //
                    // }
                }
            }
        } else {
            clock('not ok');
        }


    }
}
