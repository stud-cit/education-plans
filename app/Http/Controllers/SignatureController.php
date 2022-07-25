<?php

namespace App\Http\Controllers;

use App\Models\Signature;
use App\Http\Requests\StoreSignatureRequest;
use App\Http\Requests\UpdateSignatureRequest;

class SignatureController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreSignatureRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreSignatureRequest $request)
    {
        $validated = $request->validated();

        $signature = Signature::create($validated);

        return response()->json($signature->fresh(), 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Signature  $signature
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSignatureRequest $request, Signature $signature)
    {
        $validated = $request->validated();

        $signature->update($validated);

        return $this->success(__('messages.Updated'), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Signature  $signature
     * @return \Illuminate\Http\Response
     */
    public function destroy(Signature $signature)
    {
        try {
            $signature->delete();
        } catch (\Illuminate\Database\QueryException $exception) {
            return $this->error($exception->getMessage(), $exception->getCode());
        }
        return $this->success(__('messages.Deleted'), 200);
    }
}
