<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notebook;

class NotebookController extends Controller
{
    /**
     * Display a listing of the user's notes.
     *
     * @return \Illuminate\Http\Response
     */
	public function getIndex()
	{
		$notes = Notebook::where('user_id', auth()->id())->get();
	
		return view('notebook.index', compact('notes'));
	}
	
    /**
     * Show the form for creating a new note.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('notebook.create');
    }

    /**
     * Store a newly created note in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $note = new Notebook;
        $note->user_id = auth()->id();
        $note->title = $request->title;
        $note->content = $request->content;
        $note->type = $request->type;
        $note->save();

        return redirect()->route('notebook.get-index');
    }

	public function destroy($id)
	{
		$note = Notebook::findOrFail($id);
		$note->delete();

		return redirect()->route('notebook.get-index');
	}

}
