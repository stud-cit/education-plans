<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSignatureRequest;
use App\Models\Signature;
use Illuminate\Http\Request;

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

        Signature::create($validated);

        return $this->success(__('messages.Created'), 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Signature  $signature
     * @return \Illuminate\Http\Response
     */
    public function update(StoreSignatureRequest $request, Signature $signature)
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
