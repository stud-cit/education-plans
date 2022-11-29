<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use Illuminate\Http\Request;
use App\Models\CatalogSubject;
use App\Http\Requests\CatalogSpeciality\IndexRequest;
use App\Http\Requests\CatalogSpeciality\StoreRequest;
use App\Http\Resources\CatalogSpeciality\CatalogSpecialityResource;

class CatalogSpecialityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexRequest $request)
    {
        $validated = $request->validated();

        $perPage = Helpers::getPerPage('items_per_page', $validated);

        $catalog = CatalogSubject
            ::select(['id', 'year', 'department_id', 'faculty_id', 'speciality_id', 'user_id'])
            ->filterBy($validated)
            ->where('selective_discipline_id', CatalogSubject::SPECIALITY);

        return CatalogSpecialityResource::collection($catalog->paginate($perPage));
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();
        $validated['selective_discipline_id'] = CatalogSubject::SPECIALITY;

        CatalogSubject::create($validated);
        return $this->success(__('messages.Created'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CatalogSubject  $catalogSubject
     * @return \Illuminate\Http\Response
     */
    public function show(CatalogSubject $catalogSubject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CatalogSubject  $catalogSubject
     * @return \Illuminate\Http\Response
     */
    public function edit(CatalogSubject $catalogSubject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CatalogSubject  $catalogSubject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CatalogSubject $catalogSubject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CatalogSubject  $catalogSubject
     * @return \Illuminate\Http\Response
     */
    public function destroy(CatalogSubject $catalogSubject)
    {
        //
    }
}
