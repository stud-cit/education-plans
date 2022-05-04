<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormOrganization;
use App\Http\Resources\FormOrganizationResource;
use App\Http\Requests\StoreFormOrganizationRequest;

class FormOrganizationController extends Controller
{
    public function index()
    {
        return FormOrganizationResource::collection(FormOrganization::select('id', 'title')->get());
    }

    public function store(StoreFormOrganizationRequest $request)
    {
        $validated = $request->validated();

        FormOrganization::create($validated);

        return $this->success(__('messages.Created'), 201);
    }

    public function show(FormOrganization $formOrganization)
    {
        return new FormOrganizationResource($formOrganization);
    }

    public function update(StoreFormOrganizationRequest $request, FormOrganization $formOrganization)
    {
        $validated = $request->validated();

        $formOrganization->update($validated);

        return $this->success(__('messages.Updated'), 200);
    }

    public function destroy(FormOrganization $formOrganization)
    {
        try {
            $formOrganization->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
        return $this->success(__('messages.Deleted'), 200);

    }
}
