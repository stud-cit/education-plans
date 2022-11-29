<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use App\Models\CatalogSubject;
use App\Models\VerificationStatuses;
use Illuminate\Support\Facades\Auth;
use App\ExternalServices\Asu\Department;
use App\Http\Resources\FacultiesResource;
use App\Http\Resources\ProfessionsResource;
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

        $catalog = CatalogSubject::with('educationLevel')
            ->select([
                'id',
                'year',
                'department_id',
                'faculty_id',
                'speciality_id',
                'catalog_education_level_id',
                'user_id'
            ])
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

    public function getItemsFilters()
    {
        $modelVerificationStatuses = new VerificationStatuses;
        $asuController = new AsuController;
        $asu = new Department();
        $user = Auth::user();
        $years = CatalogSubject::select('year')
            ->where('speciality_id', '!=', null)
            ->distinct()->orderBy('year', 'desc')
            ->get();
        $divisions = VerificationStatuses::select('id', 'title')
            ->where('type', 'speciality')
            ->get();

        $verificationsStatus = $modelVerificationStatuses->getDivisionStatuses();

        $faculties = $asu->getFaculties()->when(
            $user->possibility([User::FACULTY_INSTITUTE, User::DEPARTMENT]),
            fn ($collections) => $collections->filter(fn ($faculty) => $faculty['id'] == $user->faculty_id)
        );

        return response([
            'specialties' => $asuController->getAllSpecialities(),
            'divisions' => ProfessionsResource::collection($divisions),
            'verificationsStatus' => $verificationsStatus,
            'faculties' => FacultiesResource::collection($faculties),
            'years' => $years
        ]);
    }
}
