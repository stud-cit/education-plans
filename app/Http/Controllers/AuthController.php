<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\ExternalServices\Asu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    protected $cabinet_api = "https://cabinet.sumdu.edu.ua/api/";
    protected $cabinet_service = "https://cabinet.sumdu.edu.ua/index/service/";
    protected $cabinet_service_token;

    function __construct() {
      $this->cabinet_service_token = config('app.token');
      $this->asu  = new ASU;
    }

    function logout(Request $request) {
        $request->session()->forget('key');
        $request->session()->forget('person');
        return response('ok', 200);
    }

    function register(Request $request) {
        $personCabinet = json_decode(file_get_contents($this->cabinet_api . 'getPersonInfo?key=' . 'qSP80YkNzNmfY9LHUu8woUMECxIac3MmmUxeFP9W0MVIvy845Wf5' . '&token=' . $this->cabinet_service_token), true);
        // $personCabinet = json_decode(file_get_contents($this->cabinet_api . 'getPersonInfo?key=' . $request->session()->get('key') . '&token=' . $this->cabinet_service_token), true);
  
        if(!User::where("asu_id", $personCabinet['result']['guid'])->exists()) {
            
            $userModel = new User();
            
            $personDivisions = $this->getDivisionsId($personCabinet['result']);

            

            $userModel->create([
              'name' => $personCabinet['result']['surname'] . " " . $personCabinet['result']['name'] . " " . $personCabinet['result']['patronymic'],
              'asu_id' => $personCabinet['result']['guid'],
            //   'date_bth' => $personCabinet['result']['birthday'],
              'faculty_id' => $personDivisions['faculty_id'],
              'department_id' => $personDivisions['department_id'],
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

    // function index(Request $request) {
    //   $this->mode($request);

    //   $key = "";

    //   if($request->key) {
    //     $key = $request->key;
    //     $request->session()->put('key', $request->key);
    //   }

    //   if($key == '' && $request->session()->get('key')) {
    //     $key = $request->session()->get('key');
    //   }

    //   // $personCabinet = json_decode(file_get_contents($this->cabinet_api . 'getPersonInfo?key=' . $key . '&token=' . $this->cabinet_service_token), true);

    //   $personCabinet = json_decode(Storage::get('getPerson2.json'), true);

    //   if ($personCabinet['status'] == 'OK') {
    //     $userModel = User::withCount('notifications')->where("guid", $personCabinet['result']['guid']);
    //     if($userModel->exists()) {
    //       $divisions = $this->getAllDivision();
    //       $data = $userModel->first();
    //       $person = $this->registerUser($personCabinet['result'], clone $data, $divisions);
    //       if(!$request->session()->get('person')) {
    //         $person['test_data'] = json_encode($personCabinet['result']);

    //         if($key) {
    //           $image = file_get_contents('https://cabinet.sumdu.edu.ua/api/getPersonPhoto?key=' . $key . '&token=' . $this->cabinet_service_token);
    //           Storage::disk('local')->put('public/' . $personCabinet['result']['guid'] . '.png', $image, 'public');  
    //         }

    //         $userModel->update([
    //           'name' => $person['name'],
    //           'date_bth' => $person['date_bth'],
    //           'job' => $person['job'],
    //           'job_type_id' => $person['job_type_id'],
    //           'faculty_code' => $person['faculty_code'],
    //           'department_code' => $person['department_code'],
    //           'name_div' => $person['name_div'],
    //           'academic_code' => $person['academic_code'],
    //           'categ_1' => $person['categ_1'],
    //           'categ_2' => $person['categ_2'],
    //           'kod_level' => $person['kod_level'],
    //           'test_data' => $person['test_data']
    //         ]);
    //         $request->session()->put('person', $person);
    //       }
    //       $notificationText = "";
    //       if(isset($person['name_div'])) {
    //         $notificationText .= $this->notification($person, $data, "name_div", "факультет / кафедру");
    //       }
    //       if($notificationText != "" && $request->session()->get('person')) {
    //         $notificationText = "Оновлено інформацію про автора <a href=\"/user/". $request->session()->get('person')['id'] ."\">" . $request->session()->get('person')['name'] . "</a>:<br>" . $notificationText;
    //         Audit::create([
    //           "text" => $notificationText
    //         ]);
    //       }
    //       $access = Service::select('value')->where('key', 'access')->first();
    //       return view('app', [
    //         "status" => "register",
    //         "user" => $person,
    //         "access" => $access->value
    //       ]);
    //     } else {
    //       $request->session()->forget('person');
    //       return view('app', [
    //         "status" => "login",
    //         "user" => "{
    //           name: \"". $personCabinet['result']['surname'] . ' ' . $personCabinet['result']['name'] . ' ' . $personCabinet['result']['patronymic'] ."\"
    //         }",
    //         "access" => ""
    //       ]);
    //     }
    //   } else {
    //     $request->session()->forget('key');
    //     $request->session()->forget('person');
    //     return view('app', [
    //       "status" => "unauthorized",
    //       "user" => "{}",
    //       "access" => ""
    //     ]);
    //   }
    // }

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


    function getDivisionsId($userInfo){
        $departments = $this->asu->getDepartments()->toArray();
        $departmentId = null;
        if(isset($userInfo['info2'])){
            foreach ($userInfo['info2'][0] as $k => $v) {
                if($k == 'KOD_DIV'){
                    $departmentId = $v;
                }
            }
        } else {
            return response()->json([
                "message" => "User is not sumdu worker"
            ]);
        }
        $facultyKey = array_search($departmentId, array_column($departments, 'id'));
        $facultyId = $departments[$facultyKey]['faculty_id'];
        
        return [
            'department_id' => $departmentId,
            'faculty_id' => $facultyId
        ];
        
    }
}
