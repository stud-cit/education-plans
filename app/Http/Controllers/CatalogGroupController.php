<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\CatalogGroup;
use Illuminate\Http\Request;
use App\Http\Resources\CatalogGroupResource;
use App\Http\Requests\IndexCatalogGroupRequest;
use App\Http\Requests\StoreCatalogGroupRequest;
use App\Http\Requests\UpdateCatalogGroupRequest;
use App\Http\Resources\CatalogGroupListResource;

class CatalogGroupController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(CatalogGroup::class);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexCatalogGroupRequest $request)
    {
        $validated = $request->validated();

        $perPage = Helpers::getPerPage('items_per_page', $validated);

        $catalog = CatalogGroup::withTrashed()->select('id', 'title', 'deleted_at')->paginate($perPage);
        return CatalogGroupResource::collection($catalog);
    }

    public function list()
    {
        $catalog = CatalogGroup::select('id', 'title')->get();

        return CatalogGroupListResource::collection($catalog);
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

            return $this->success(__('messages.Zipped'), 201);
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
