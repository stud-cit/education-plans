<?php

namespace App\Http\Controllers;

use App\Http\Resources\FormOrganizationResource;
use App\Models\FormOrganization;

class FormOrganizationController extends Controller
{
    public function index()
    {
        return FormOrganizationResource::collection(FormOrganization::select('id','title')->get());
    }
}
