<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\ExternalServices\Asu\Worker;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\WorkersResource;
use App\ExternalServices\Asu\Department;
use App\Http\Requests\UpdateUserRoleRequest;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $roleAdmin = $user->role_id === User::ADMIN;
        $roleInstitute = $user->role_id === User::FACULTY_INSTITUTE;

        $users = User::select('id', 'asu_id', 'department_id', 'faculty_id', 'role_id')
            ->when($roleAdmin, function ($query) {
                return $query->where('role_id', '!=', User::ROOT);
            })
            ->when($roleInstitute, function ($query) use ($user) {
                return $query->whereIn('role_id', [User::FACULTY_INSTITUTE, User::DEPARTMENT])
                    ->where('faculty_id', '=', $user->faculty_id);
            })
            ->paginate();

        return UserResource::collection($users);
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

        $user->name = $validated['full_name'];

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
        $worker = new Worker();
        $_workers = $worker->getAllWorkers();

        $workers = $_workers->map(function ($worker) use ($users) {

            return [
                'asu_id' => $worker['asu_id'],
                'full_name' => $worker['last_name'] . ' ' . $worker['first_name'] . ' ' . $worker['patronymic'],
                'disabled' => $users->contains($worker['asu_id'])
            ];
        });

        return response()->json($workers->sortBy('full_name')->values());
    }

    public function listWorkers()
    {
        $worker = new Worker();
        $workers = $worker->getAllWorkers();

        $filteredWorkers = $workers->map(function ($worker) {

            return [
                'asu_id' => $worker['asu_id'],
                'full_name' => "{$worker['last_name']} {$worker['first_name']} {$worker['patronymic']}",
            ];
        });

        return response()->json($filteredWorkers->sortBy('full_name')->values());
    }

    public function getFacultyByWorker(Request $request)
    {
        $department = new Department();

        $validated = $request->validate([
            'department_id' => 'required|numeric',
        ]);

        $faculty = $department->searchFacultyByDepartmentId($validated['department_id']);

        return response()->json($faculty);
    }
}
