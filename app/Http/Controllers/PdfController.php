<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    public function index()
    {
        return Storage::download('doc/manual.pdf');
    }

    public function upload(Request $request)
    {
        $validated = $request->validate([
            'doc' => 'required|file|mimes:pdf',
        ]);

        $request->file('doc')->storeAs('doc', 'manual.pdf');

        return $this->success(__('messages.Updated'));
    }
}
