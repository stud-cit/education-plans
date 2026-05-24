<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Support\Carbon;
use App\Http\Resources\NoteResource;
use App\Http\Requests\{StoreNoteRequest, UpdateNoteRequest};

class NoteController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Note::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notes = Note::select('id', 'abbreviation', 'explanation', 'date')->get();
        return NoteResource::collection($notes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNoteRequest $request)
    {
        $validated = $request->validated();

        Note::create($validated);

        return $this->success(__('messages.Created'), 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateNoteRequest $request, Note $note)
    {
        $validated = $request->validated();

        $note->update($validated);

        return $this->success(__('messages.Updated'), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response | \Illuminate\Database\QueryException
     */
    public function destroy(Note $note)
    {
        try {
            $note->delete();
        } catch (\Illuminate\Database\QueryException $exception) {
            return $this->error($exception->getMessage(), $exception->getCode());
        }

        return $this->success(__('messages.Deleted'), 200);
    }


    public function rules(string $date)
    {
        return response()->json(['data' => $this->getNotes($date)]);
    }

    public function getNotes(string $date): array
    {
        $date = Carbon::parse($date . '-01-01')->format('Y-m-d');

        $notes = Note::select('id', 'abbreviation', 'explanation')
            ->where('date', null)
            ->orWhereDate('date', '<', $date)
            ->get()
            ->toArray();

        $att = array_column($notes, 'abbreviation');

        $rule = implode(',', $att);

        $arrayNotes = array_reduce($notes, function ($result, $item) {
            $result[] = "{$item['abbreviation']} - {$item['explanation']}";
            return $result;
        });


        if (empty($arrayNotes)) {
            $listNotes = '';
        } else {
            $listNotes = implode('; ', $arrayNotes) . '.';
        }

        return [
            'rule' => $rule,
            'notes' => $listNotes
        ];
    }
}
