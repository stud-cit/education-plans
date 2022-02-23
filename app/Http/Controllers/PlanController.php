<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlanResource;
use App\Http\Resources\PlanShowResource;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PlanController extends Controller
{
    private function stringToBoolean(string $string): bool
    {
        return filter_var($string, FILTER_VALIDATE_BOOLEAN);
    }

    private function ordering($method): string
    {
        $method = $method ?? 'false';
        $input = $this->stringToBoolean($method);
        return $input ? 'desc' : 'asc';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'integer',
            'items_per_page' => 'integer',
            'sort_by' => ['nullable', Rule::in(['title', 'year', 'created_at'])],
            'sort_desc' => ['nullable', Rule::in(['true', 'false'])],
        ]);

        $validated = $validator->validated();

        $plans = Plan::select('id', 'title', 'year', 'faculty_id', 'department_id', 'created_at')
            ->filterBy(request()->all())
            ->when($validated['sort_by'], function ($query) use ($validated) {
                return $query->orderBy($validated['sort_by'], $this->ordering($validated['sort_desc']));
            })
            ->paginate($validated['items_per_page']);

        return PlanResource::collection($plans);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function show(Plan $plan)
    {
        $model = $plan->load(['formStudy', 'educationLevel', 'formOrganization']);
        return new PlanShowResource($model);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Plan $plan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Plan $plan)
    {
        $plan->delete();
        return response()->json(['message' => __('messages.Deleted')], 204);
    }

    public function copy(Plan $plan)
    {
        $plan->replicateRow();

        return $this->success(__('messages.Copied'), 201);
    }
}
