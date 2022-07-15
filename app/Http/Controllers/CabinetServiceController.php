<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CabinetServiceController extends Controller
{
    public function __invoke(Request $request)
    {
        $info = "Каталог навчальних планів";  // Service description
        $icon =  public_path() . "/service.png";    // Service icon (48x48)
        $mask = 13;      // Service modes 3,2,0 (1101 bits)

        switch($request->mode) {
            case 2:
                header('Content-Type: image/png');
                readfile($icon);
                break;
            case 3:
                echo $info;
                break;
            case 100;
                header('X-Cabinet-Support: ' . $mask);
            default:
                break;
        }

        return response('Ok');
    }
}
