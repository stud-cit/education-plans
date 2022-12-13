<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogEducationProgram\PdfCatalogRequest;
use App\Http\Resources\CatalogSpeciality\CatalogSpecialityPdfResource;
use ErrorException;
use App\Models\User;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use App\Models\VerificationStatuses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\CatalogEducationLevel;
use App\Models\CatalogEducationProgram;
use App\ExternalServices\Asu\Department;
use App\Http\Resources\FacultiesResource;
use App\Http\Resources\ProfessionsResource;
use App\Http\Requests\CatalogEducationProgram\CopyRequest;
use App\Http\Requests\CatalogEducationProgram\IndexRequest;
use App\Http\Requests\CatalogEducationProgram\OwnerRequest;
use App\Http\Requests\CatalogEducationProgram\StoreRequest;
use App\Http\Requests\CatalogEducationProgram\UpdateRequest;
use App\Http\Requests\CatalogEducationProgram\StoreSignatureRequest;
use App\Http\Resources\CatalogEducationProgram\CatalogEducationProgramResource;
use App\Http\Requests\CatalogEducationProgram\StoreCatalogEducationProgramVerificationRequest;
use App\Http\Requests\CatalogEducationProgram\ToggleCatalogEducationProgramVerificationRequest;

class CatalogEducationProgramController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(CatalogEducationProgram::class);
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

        $catalog = CatalogEducationProgram::with(['educationLevel', 'verifications'])
            ->select([
                'id',
                'user_id',
                'department_id',
                'faculty_id',
                'education_program_id',
                'catalog_education_level_id',
                'year',
                'need_verification',
            ])
            ->filterBy($validated)->orderBy('created_at', 'desc');

        return CatalogEducationProgramResource::collection($catalog->paginate($perPage));
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
        $validated['selective_discipline_id'] = CatalogEducationProgram::EDUCATION_PROGRAM;

        CatalogEducationProgram::create($validated);
        return $this->success(__('messages.Created'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CatalogEducationProgram  $catalogEducationProgram
     * @return \Illuminate\Http\Response
     */
    public function show(CatalogEducationProgram $catalogEducationProgram)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CatalogEducationProgram  $catalogEducationProgram
     * @return \Illuminate\Http\Response
     */
    public function edit(CatalogEducationProgram $catalogEducationProgram)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CatalogEducationProgram  $catalogEducationProgram
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, CatalogEducationProgram $catalogEducationProgram)
    {
        $validated = $request->validated();

        $catalogEducationProgram->update($validated);

        $this->success(__('messages.Updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CatalogEducationProgram  $catalogEducationProgram
     * @return \Illuminate\Http\Response
     */
    public function destroy(CatalogEducationProgram $catalogEducationProgram)
    {
        //
    }

    public function delete(CatalogEducationProgram $catalogEducationProgram)
    {
        $model = $catalogEducationProgram->loadCount('subjects');

        if ($model->subjects_count > 0) {
            throw new ErrorException('Каталог не пустий, видаліть спочатку предмети.');
        } else {
            $catalogEducationProgram->delete();
        }
    }

    public function getItemsFilters()
    {
        $modelVerificationStatuses = new VerificationStatuses;
        $asuController = new AsuController;
        $asu = new Department();
        $user = Auth::user();
        $years = CatalogEducationProgram::select('year')
            ->where('education_program_id', '!=', null)
            ->distinct()->orderBy('year', 'desc')
            ->get();
        $divisions = VerificationStatuses::select('id', 'title')
            ->where('type', 'education-program')
            ->get();

        $verificationsStatus = $modelVerificationStatuses->getDivisionStatuses();

        $faculties = $asu->getFaculties()->when(
            $user->possibility([User::FACULTY_INSTITUTE, User::DEPARTMENT]),
            fn ($collections) => $collections->filter(fn ($faculty) => $faculty['id'] == $user->faculty_id)
        );

        return response([
            'education_programs' => $asuController->getAllEducationPrograms(),
            'divisions' => ProfessionsResource::collection($divisions),
            'verificationsStatus' => $verificationsStatus,
            'faculties' => FacultiesResource::collection($faculties),
            'years' => $years,
            'education_levels' => CatalogEducationLevel::select('id', 'title')->get(),
        ]);
    }

    /**
     * Copy education program catalog with subjects
     *
     * @param CopyRequest $request
     * @param CatalogEducationProgram $catalogEducationProgram
     * @return void
     */
    public function copy(CopyRequest $request, CatalogEducationProgram $catalogEducationProgram)
    {
        $validated = $request->validated();

        $catalog = $catalogEducationProgram->fill([
            'year' => $validated['year'],
            'education_program_id' => $validated['education_program_id'],
            'user_id' => Auth::id(),
            'need_verification' => null,
        ]);

        $catalog->duplicate();

        return $this->success(__('messages.Created'), 201);
    }

    public function owners(OwnerRequest $request, CatalogEducationProgram $catalogEducationProgram)
    {
        $validated = $request->validated();

        $catalogEducationProgram->owners()->whereNotIn('id', array_column($validated['owners'], 'id'))->delete();

        $result = array_map(function ($el) use ($validated) {
            return [
                'catalog_subject_id' => $validated['id'],
                'department_id' => $el['id'],
            ];
        }, $validated['owners']);

        $catalogEducationProgram->owners()->createMany($result);

        return $this->success(__('messages.Created'), 201);
    }

    public function storeSignatures(StoreSignatureRequest $request, CatalogEducationProgram $catalogEducationProgram)
    {
        $validated = $request->validated();

        $catalogEducationProgram->signatures()->whereNotIn('id', array_column($validated['signatures'], 'id'))->delete();

        foreach ($validated['signatures'] as $signature) {
            $catalogEducationProgram->signatures()->updateOrCreate(
                [
                    'id' => $signature['id'],
                    'catalog_subject_id' => $signature['catalog_subject_id'],
                ],
                [
                    'asu_id' =>  $signature['asu_id'],
                    'faculty_id' =>  $signature['faculty_id'],
                    'department_id' =>  $signature['department_id'],
                    'catalog_signature_type_id' =>  $signature['catalog_signature_type_id'],
                ]
            );
        }

        return $this->success(__('messages.Updated'), 201);
    }

    public function toggleToVerification(
        ToggleCatalogEducationProgramVerificationRequest $request,
        CatalogEducationProgram $catalogEducationProgram
    ) {
        if (!Gate::allows('toggle-need-verification-education-program-catalog', $catalogEducationProgram)) {
            abort(403);
        }

        $validated = $request->validated();

        $catalogEducationProgram->need_verification = $validated['need_verification'];

        $catalogEducationProgram->update();
        $catalogEducationProgram->verifications()->delete();

        return $this->success(__('messages.Updated'), 200);
    }

    public function verification(
        StoreCatalogEducationProgramVerificationRequest $request,
        CatalogEducationProgram $catalogEducationProgram
    ) {
        // TODO: MAKE GATE
        if (!Gate::allows('can-verification-education-program-catalog', $catalogEducationProgram)) {
            abort(403);
        }

        $validated = $request->validated();

        if (Auth::user()->role_id === User::ADMIN) {
            $catalogEducationProgram->need_verification = true;
            $catalogEducationProgram->update();
        }

        if (array_key_exists('comment', $validated)) {
            if ($validated['comment'] !== null) {
                $catalogEducationProgram->need_verification = null;
                $catalogEducationProgram->update();
            }
        }

        $catalogEducationProgram->verifications()->updateOrCreate(
            [
                'verification_status_id' => $validated['verification_status_id'],
                'catalog_id' => $validated['catalog_id']
            ],
            [
                'status' => $validated['status'],
                'comment' => isset($validated['comment']) ? $validated['comment'] : null,
                'user_id' => $validated['user_id'],
            ]
        );

        return $this->success(__('messages.Updated'), 200);
    }

    public function pdf(PdfCatalogRequest $request)
    {
        $validated = $request->validated();

        $catalog = CatalogEducationProgram::with(['subjects', 'signatures'])
            ->where('id', $validated['catalog_id'])->first();
        return new CatalogSpecialityPdfResource($catalog);
    }
}
