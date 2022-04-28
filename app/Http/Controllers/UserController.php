<?php

namespace App\Http\Controllers;

use App\ExternalServices\ASU;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\WorkersResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UserResource::collection(User::select('id', 'asu_id', 'department_id', 'faculty_id', 'role_id')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $validated['offices_id'] = 1; //Todo unknown field

        User::create($validated);

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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRoleRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->update($validated);

        return $this->success(__('messages.Updated'), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => __('messages.Deleted')], 201);
    }

    public function workers()
    {
        $users = User::select('asu_id')->pluck('asu_id');
        $asu = new ASU();
        $_workers = $asu->getAllWorkers();

        $workers = $_workers->map(function ($worker) use ($users) {

            return [
                'asu_id' => $worker['asu_id'],
                'full_name' => $worker['last_name'] .' '. $worker['first_name'] .' '. $worker['patronymic'],
                'department_id' => $worker['department_id'],
                'department' => $worker['department'],
                'faculty_id' => $faculty['id'] ?? null,
                'faculty' => $faculty['name'] ?? null,
                'disabled' => $users->contains($worker['asu_id'])
            ];
        });

        return response()->json($workers->sortBy('full_name')->values());
    }

    public function getFacultyByWorker(Request $request) {
        $asu = new ASU();

        $validated = $request->validate([
            'department_id' => 'required|numeric',
        ]);

        $faculty = $asu->searchFacultyByDepartmentId($validated['department_id']);

        return response()->json($faculty);
    }
}
