<?php

namespace App\Http\Controllers;

use App\Http\Constant;
use App\Http\Requests\IndexCatalogGroupRequest;
use App\Models\CatalogGroup;
use App\Http\Resources\CatalogGroupResource;
use App\Http\Requests\StoreCatalogGroupRequest;
use App\Http\Requests\UpdateCatalogGroupRequest;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class CatalogGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexCatalogGroupRequest $request)
    {
        $validated = $request->validated();

        $perPage = array_key_exists('items_per_page', $validated)
            ? $validated['items_per_page']
            : Constant::PAGINATE;

        $catalog = CatalogGroup::withTrashed()->select('id', 'title', 'deleted_at')->paginate($perPage);
        return CatalogGroupResource::collection($catalog);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCatalogGroupRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCatalogGroupRequest $request)
    {
        $validated = $request->validated();

        CatalogGroup::create($validated);

        return $this->success(__('messages.Created'), 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCatalogGroupRequest  $request
     * @param  \App\Models\CatalogGroup  $catalogGroup
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCatalogGroupRequest $request, CatalogGroup $catalogGroup)
    {
        $validated = $request->validated();

        $catalogGroup->update($validated);

        return $this->success(__('messages.Updated'), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CatalogGroup  $catalogGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(CatalogGroup $catalogGroup)
    {
        try {
            $catalogGroup->delete();

            return $this->success(__('messages.Deleted'), 201);
        } catch (\Illuminate\Database\QueryException $exception) {
            return $this->error($exception->getMessage(), $exception->getCode());
        }
    }

    public function restore(Request $request)
    {
        CatalogGroup::withTrashed()->where('id', $request->id)->restore();

        return $this->success(__('messages.Unzipped'), 201);
    }
}
