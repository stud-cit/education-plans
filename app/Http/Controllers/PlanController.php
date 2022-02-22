<?php

namespace App\Http\Controllers;

use App\Http\Constant;
use App\Http\Resources\PlanResource;
use App\Http\Resources\PlanShowResource;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PlanController extends Controller
{
    private function stringToBoolean(string $string): bool
    {
        return filter_var($string, FILTER_VALIDATE_BOOLEAN);
    }

    private function ordering($method): string
    {
        $input = $this->stringToBoolean($method);
        return $input ? 'desc' : 'asc';
    }

    public function paginateCollection($items, $perPage = 15, $page = null, $options = [])
    {
        $perPage = $perPage ?? 15; // if null given
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->page;
        $perPage = $request->items_per_page;

        $plans = Plan::select('id', 'title', 'year', 'faculty_id', 'department_id', 'created_at')
            ->filterBy(request()->all())
            ->get()
            ->when($request->sort_by, function ($collection) use ($request) {
                return $collection->sortBy([$request->sort_by, $this->ordering($request->sort_desc ?? false)]);
            })->paginateCollection($perPage, $page);

//        clock("sort_by: {$request->sort_by}, method: {$this->ordering($request->sort_desc)}");

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
