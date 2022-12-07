<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use App\Models\CatalogSpeciality;
use App\Models\VerificationStatuses;
use Illuminate\Support\Facades\Auth;
use App\Models\CatalogEducationLevel;
use App\ExternalServices\Asu\Department;
use App\Http\Resources\FacultiesResource;
use App\Http\Resources\ProfessionsResource;
use App\Http\Requests\CatalogSpeciality\CopyRequest;
use App\Http\Requests\CatalogSpeciality\IndexRequest;
use App\Http\Requests\CatalogSpeciality\OwnerRequest;
use App\Http\Requests\CatalogSpeciality\StoreRequest;
use App\Http\Requests\CatalogSpeciality\UpdateRequest;
use App\Http\Resources\CatalogSpeciality\CatalogSpecialityResource;
use ErrorException;

class CatalogSpecialityController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(CatalogSpeciality::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexRequest $request)
    {
        $validated = $request->validated();

        $perPage = Helpers::getPerPage('items_per_page', $validated);

        $catalog = CatalogSpeciality::with(['educationLevel', 'verifications'])
            ->select([
                'id',
                'user_id',
                'department_id',
                'faculty_id',
                'speciality_id',
                'catalog_education_level_id',
                'year',
                'need_verification',
            ])
            ->filterBy($validated);

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
        $validated['selective_discipline_id'] = CatalogSpeciality::SPECIALITY;

        CatalogSpeciality::create($validated);
        return $this->success(__('messages.Created'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CatalogSpeciality  $catalogSpeciality
     * @return \Illuminate\Http\Response
     */
    public function show(CatalogSpeciality $catalogSpeciality)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CatalogSpeciality  $catalogSpeciality
     * @return \Illuminate\Http\Response
     */
    public function edit(CatalogSpeciality $catalogSpeciality)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CatalogSpeciality  $catalogSpeciality
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, CatalogSpeciality $catalogSpeciality)
    {
        $validated = $request->validated();

        $catalogSpeciality->update($validated);

        $this->success(__('messages.Updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CatalogSpeciality  $catalogSpeciality
     * @return \Illuminate\Http\Response
     */
    public function destroy(CatalogSpeciality $catalogSpeciality)
    {
        // TODO: $catalogSpeciality null
        // 🤷‍♀️ now use method delete
    }

    public function delete(CatalogSpeciality $catalogSpeciality)
    {
        $model = $catalogSpeciality->loadCount('subjects');

        if ($model->subjects_count > 0) {
            throw new ErrorException('Каталог не пустий, видаліть спочатку предмети.');
        } else {
            $catalogSpeciality->delete();
        }
    }

    public function getItemsFilters()
    {
        $modelVerificationStatuses = new VerificationStatuses;
        $asuController = new AsuController;
        $asu = new Department();
        $user = Auth::user();
        $years = CatalogSpeciality::select('year')
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
            'years' => $years,
            'education_levels' => CatalogEducationLevel::select('id', 'title')->get(),
        ]);
    }

    /**
     * Copy speciality catalog with subjects
     *
     * @param CopyRequest $request
     * @param CatalogSpeciality $catalogSpeciality
     * @return void
     */
    public function copy(CopyRequest $request, CatalogSpeciality $catalogSpeciality)
    {
        $validated = $request->validated();

        $catalog = $catalogSpeciality->fill([
            'year' => $validated['year'],
            'speciality_id' => $validated['speciality_id'],
            'user_id' => Auth::id(),
            'need_verification' => null,
        ]);

        $catalog->duplicate();

        return $this->success(__('messages.Created'), 201);
    }

    public function owners(OwnerRequest $request, CatalogSpeciality $catalogSpeciality)
    {
        $validated = $request->validated();

        $catalogSpeciality->owners()->upsert(
            $validated['owners'],
            ['catalog_subject_id', 'department_id'],
            ['department_id']
        );

        return $this->success(__('messages.Created'), 201);
    }
}
