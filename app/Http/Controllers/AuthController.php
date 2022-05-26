<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\ExternalServices\Asu;
use App\Helpers\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class AuthController extends Controller
{
    protected $cabinet_api = "https://cabinet.sumdu.edu.ua/api/";
    protected $cabinet_service = "https://cabinet.sumdu.edu.ua/index/service/";
    protected $cabinet_service_token;
    protected $cabinet_service_key;

    function __construct() {
      $this->cabinet_service_token = config('app.token');
      $this->cabinet_service_key = config('app.service_key');
      $this->asu  = new ASU;
    }

    function logout(Request $request) {
        $request->session()->forget('key');
        $request->session()->forget('person');
        return response('ok', 200);
    }

    function register(Request $request) {

        $key = $this->cabinet_service_key;
        $personCabinet = json_decode(file_get_contents($this->cabinet_api . 'getPersonInfo?key=' . $key . '&token=' . $this->cabinet_service_token), true);
        
        if(!User::where("asu_id", $personCabinet['result']['guid'])->exists()) {
            
            $userModel = new User();
            $personDivisions = $this->getDivisionsInfo($personCabinet['result']);

            $userModel->create([
              'name' => $personCabinet['result']['surname'] . " " . $personCabinet['result']['name'] . " " . $personCabinet['result']['patronymic'],
              'asu_id' => $personCabinet['result']['guid'],
              'faculty_id' => $personDivisions['faculty_id'],
              'faculty_name' => $personDivisions['faculty_name'],
              'department_id' => $personDivisions['department_id'],
              'department_name' => $personDivisions['department_name'],
              'offices_id' => $personDivisions['department_id'],
              'email' => $personCabinet['result']['email'],
              'remember_token' => $personCabinet['result']['token'],
              'role_id' => 1
             
            ]);  
            
            return response()->json('ok', 200);
        } else {
            return response()->json([
                "message" => "Користувач вже зареєстрований в системі"
            ]);
        }
    }

    function checkAuth(Request $request) {
      $checkPerson = $request->session()->exists('person');
      
      if($checkPerson) {
        $person = $request->session()->get('person');
        $userData = User::where("asu_id", $person['guid'])->first();
        return response()->json($userData, 200);
      }

      else{ 
        
        return response()->json('not authorized', 401);
      }
      

    }

    function index(Request $request) {

    //   $this->mode($request);
    $request->session()->forget('person');
    $key = $this->cabinet_service_key;

      if($request->key) {

        $key = $request->key;
        $request->session()->put('key', $request->key);
      }

      if($key == '' && $request->session()->get('key')) {

        $key = $request->session()->get('key');
      }
      
      
      $personCabinet = json_decode(file_get_contents($this->cabinet_api . 'getPersonInfo?key=' . $key . '&token=' . $this->cabinet_service_token), true);
    
      if($personCabinet['status'] == 'OK') {

        $userModel = User::where("asu_id", $personCabinet['result']['guid']);
        $personDivisions = $this->getDivisionsInfo($personCabinet['result']);
        
        if($userModel->exists()) {
    
          $data = $userModel->first();
        

          if(!$request->session()->exists('person')) {
            

            // if($key) {
            //   $image = file_get_contents('https://cabinet.sumdu.edu.ua/api/getPersonPhoto?key=' . $key . '&token=' . $this->cabinet_service_token);
            //   Storage::disk('local')->put('public/' . $personCabinet['result']['guid'] . '.png', $image, 'public');  
            // }

            $testData = $userModel->updateOrCreate([
              'name' => $personCabinet['result']['surname'] . " " . $personCabinet['result']['name'] . " " . $personCabinet['result']['patronymic'],
              'faculty_id' => $personDivisions['faculty_id'],
              'faculty_name' => $personDivisions['faculty_name'],
              'department_id' => $personDivisions['department_id'],
              'department_name' => $personDivisions['department_name'],
              'email' => $personCabinet['result']['email'],
              'remember_token' => $personCabinet['result']['token'],
            ]);
            $request->session()->put('person', $personCabinet['result']);
          }

          // ActivityLog::addToLog('Auth User, update users data table');
          // dd($request->session()->get('person'));
          return response()->json($testData, 200);
        } else {

          $request->session()->forget('person');
          return response()->json(['message' => 'Користувач не зареєстрований в системі']);
        }
      } else {

        $request->session()->forget('key');
        $request->session()->forget('person');
        return response()->json(['message' => 'невірний ключ'], 401);
      }
    }

    // function mode($request) {
    //     $info = 'Інформаційний сервіс «Наукові публікації» дозволить Вам зручно вести облік Ваших наукових та науково-методичних публікацій'; // Service description
    //     $icon = public_path() . "/service.png"; // Service icon (48x48)
    //     $mask = 13; // Service modes 3,2,0 (1101 bits)

    //     $mode = !empty($request->mode) ? $request->mode : 0;

    //     // В зависимости от режима (mode) возвращаем или иконку, или описание, или специальный заголовок
    //     switch($mode) {
    //         case 0:
    //             break;
    //         case 2:
    //             header('Content-Type: image/png');
    //             readfile($icon);
    //             exit;
    //         case 3:
    //             echo $info;
    //             exit;
    //         case 100;
    //             header('X-Cabinet-Support: ' . $mask);
    //         default:
    //             exit;
    //     }
    // }


    function getDivisionsInfo($userInfo) {
        
        $departments = $this->asu->getDepartments()->toArray();
        $typeFaculty = 9;
        $typeInstitute = 7;
        $typeDepartment = 2;
        $departmentId = 0;
        $departmentName = '';
        $facultyName = '';

        if(isset($userInfo['info2'])){

            foreach ($userInfo['info2'] as $k => $v) {

                if(($v['KOD_STATE'] == 1) && ($v['KOD_SYMP'] == 1 || $v['KOD_SYMP'] == 5)) {

                    $departmentId = $v['KOD_DIV'];
                }
                
            }

        } else {

            return response()->json([
                "message" => "User is not sumdu worker"
            ]);

        }

        $divIndex = array_search($departmentId, array_column($departments, 'id'));
        $divType = $departments[$divIndex]['unit_type'];
        $facultyId = $departments[$divIndex]['faculty_id'];

        

        if($divType == $typeFaculty || $divType == $typeInstitute){

            $facultyId = $departmentId;
            $facultyName= $departments[$divIndex]['name'];
            $departmentId = 0;

        } else if($facultyId != 0) {

            $facultyId = $departments[$divIndex]['faculty_id'];
            $facultyIndex= array_search($facultyId, array_column($departments, 'id'));
            $facultyName= $departments[$facultyIndex]['name'];
            $departmentName = $departments[$divIndex]['name'];
            

        } else {
            $departmentName = $departments[$divIndex]['name'];
        }


        return [
            'department_id' => $departmentId,
            'department_name' => $departmentName,
            'faculty_id' => $facultyId,
            'faculty_name' => $facultyName
        ];
        
    }
}
