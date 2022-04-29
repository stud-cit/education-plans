<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Helpers\Tree;
use App\Models\Cycle;
use App\Http\Constant;
use Illuminate\Http\Request;
use App\Http\Resources\PlanResource;
use App\Http\Requests\indexPlanRequest;
use App\Http\Requests\StoreCycleRequest;
use App\Http\Resources\PlanShowResource;
use App\Http\Requests\UpdateCycleRequest;

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

    public function index(IndexPlanRequest $request)
    {
        $validated = $request->validated();

        $perPage = array_key_exists('items_per_page', $validated) ? $validated['items_per_page'] : Constant::PAGINATE;

        $plans = Plan::select('id', 'title', 'year', 'faculty_id', 'department_id', 'created_at')
            ->filterBy($validated)
            ->when($validated['sort_by'] ?? false, function ($query) use ($validated) {
                return $query->orderBy($validated['sort_by'], $this->ordering($validated['sort_desc']));
            })
            ->paginate($perPage);

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

    
    public function cycleStore(StoreCycleRequest $request, Plan $plan)
    {
        $validated = $request->validated();

        $plan->cycles()->create($validated);

        $data = Tree::makeTree($plan->cycles);

        return response()->json(['data' => $data], 201);
    }

    public function cycleUpdate(UpdateCycleRequest $request, Plan $plan, Cycle $cycle)
    {
        $validated = $request->validated();

        if ($plan->id === $cycle->plan_id) {
            $cycle->update($validated);
        }

        return $this->success(__('messages.Updated'), 202);
    }

    public function cycleDestroy(Plan $plan, Cycle $cycle)
    {
        try {
            Cycle::where('id', $cycle->id )->where('plan_id', $plan->id)->delete();
        } catch(\Illuminate\Database\QueryException $e) {
            $this->error($e->getMessage(), $e->getCode());
        }

        return $this->success(__('messages.Deleted'), 200);
    }
}
