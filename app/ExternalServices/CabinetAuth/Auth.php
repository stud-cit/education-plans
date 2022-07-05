<?php

namespace App\ExternalServices\CabinetAuth;

use App\ExternalServices\Asu\Department;
use App\Http\Constant;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Auth
{
    private $key = null;
    private $token = null;
    private $host = 'https://cabinet.sumdu.edu.ua/api/getPersonInfo';
    private $user;
    private $model;
    private $errorMessage = null;
    private $errorCode;

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->token = config('app.cabinet_app_token');
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    private function getUser()
    {
        $params = [
            'key' => $this->key,
            'token' => $this->token,
        ];

        clock("Authorization: {$params['key']}");
        clock("Token: {$params['token']}");

        $response = Http::retry(3, 100)->get($this->host, $params)->json();

        if ($response['status'] === 'OK') {
            $this->user = $response['result'];

        } else if (in_array($response['status'], Constant::ASU_ERRORS)) {

            $this->error = "Cabinet: {$response['result']}";
        } else {
            throw new HttpException (500, $response);
        }
    }

    private function mutateModel () {
        $model = User::where("asu_id", $this->user['guid'])->first();
        clock("model", $model->toArray());

        if($model) {
            $asu = new Department();
            $divisions = $asu->getDepartmentInfoByUser($this->user);

            $new = [
                'name' => "{$this->user['surname']} {$this->user['name']} {$this->user['patronymic']}",
                'faculty_id' => $divisions['faculty_id'],
                'faculty_name' => $divisions['faculty_name'],
                'department_id' => $divisions['department_id'],
                'department_name' => $divisions['department_name'],
                'email' => $this->user['email'],
            ];

            if ($this->isArrayDiffByKey($model->toArray(), $new, array_keys($new)))  {
                $model->update($new);
                $model->refresh();
            }
        } else {
            $this->errorCode = 403;
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
