<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use App\Models\Subject;
use App\Models\HoursModules;
use App\Models\PlanVerification;
use App\Models\SemestersCredits;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreSubjectRequest;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json('200');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\StoreSubjectRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSubjectRequest $request)
    {
        $validated = $request->validated();

        if ($request['selectiveDiscipline']) {
            $validated['asu_id'] = null;
        }

        DB::transaction(function () use ($validated) {
            $subject = Subject::create($validated);

            $hoursModules = collect($validated['hours_modules']);
            $semestersCredits = collect($validated['semesters_credits']);

            $hoursModules->transform(function ($item, $key) use ($subject) {
                // unset($item['checkHour']);
                // unset($item['hasTask']);
                $item['subject_id'] = $subject->id;
                return $item;
            });

            $semestersCredits->transform(function ($item, $key) use ($subject) {
                $item['subject_id'] = $subject->id;
                return $item;
            });

            Plan::find($validated['plan_id'])->update([
                'need_verification' => false
            ]);

            $user = Auth::user();
            if ($user->role_id == User::FACULTY_INSTITUTE || $user->role_id == User::DEPARTMENT) {
                PlanVerification::where("plan_id", $validated['plan_id'])->delete();
            }

            HoursModules::insert($hoursModules->toArray());
            SemestersCredits::insert($semestersCredits->toArray());
        });

        return $this->success(__('messages.Created'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\StoreSubjectRequest  $request
     * @param  App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function update(StoreSubjectRequest $request, Subject $subject)
    {
        $validated = $request->validated();

        if ($request['selectiveDiscipline']) {
            $validated['asu_id'] = null;
        } else {
            $validated['selective_discipline_id'] = null;
        }

        $subject->update($validated);

        HoursModules::where('subject_id', $subject->id)->delete();
        SemestersCredits::where('subject_id', $subject->id)->delete();

        $hoursModules = [];
        $semestersCredits = [];
        foreach ($request['hours_modules'] as $key => $value) {
            array_push($hoursModules, [
                "course" => $value['course'],
                "form_control_id" => $value['form_control_id'],
                "hour" => $value['hour'],
                "individual_task_id" => $value['individual_task_id'],
                "module" => $value['module'],
                "semester" => $value['semester'],
                "subject_id" => $subject->id
            ]);
        }
        foreach ($request['semesters_credits'] as $key => $value) {
            array_push($semestersCredits, [
                "course" => $value['course'],
                "credit" => $value['credit'],
                "semester" => $value['semester'],
                "subject_id" => $subject->id
            ]);
        }
        HoursModules::insert($hoursModules);
        SemestersCredits::insert($semestersCredits);

        Plan::find($request['plan_id'])->update([
            'need_verification' => false
        ]);

        $user = Auth::user();
        if ($user->role_id == 6 || $user->role_id == 7) {
            PlanVerification::where("plan_id", $request['plan_id'])->delete();
        }

        Subject::with('cycle')->whereHas('cycle', function ($queryCycle) use ($request) {
            $queryCycle->where('plan_id', $request['plan_id']);
        })->update([
            'verification' => 1
        ]);

        return $this->success(__('messages.Updated'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subject $subject)
    {
        try {
            $subject->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
        return $this->success(__('messages.Deleted'), 200);
    }
}
