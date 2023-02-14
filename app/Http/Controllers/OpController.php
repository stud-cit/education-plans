<?php

namespace App\Http\Controllers;

use App\ExternalServices\Op\OP;
use Illuminate\Http\Request;

class OpController extends Controller
{
    public function programs(Request $request)
    {

        // --+--------------------------------
        //  1|Молодший спеціаліст               '0' => 'Молодший спеціаліст',
        //  2|перший (бакалаврський) рівень     '2' => 'Бакалавр',
        //  3|Спеціаліст                            '1' => 'Молодший бакалавр', ??
        //  4|другий (магістерський) рівень     '3' => 'Магістр',
        //  8|третій (освітньо-науковий) рівень '4' => 'Доктор філософії'

        $bridge = [
            1 => 0,
            2 => 2,
            3 => 1,
            4 => 3,
            8 => 4,
        ];

        $validated = $request->validate([
            'year' => 'required|date_format:Y',
            'degree' => 'required|numeric'
        ]);

        if (array_key_exists($validated['degree'], $bridge)) {
            $validated['degree'] = $bridge[$validated['degree']];
        }

        $model = new OP();
        $data = $model->getPrograms($validated);

        return response()->json($data);
    }

    public function programId(Request $request)
    {
        $model = new OP();
        $data = $model->getProgramId($request);
        return response()->json($data);
    }
}
