<?php

namespace App\Http\Controllers;

use App\Models\Books;
use Illuminate\Http\Request;
use App\Models\Authors;
use Yajra\DataTables\DataTables;

class AuthorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $authors = Authors::all();
        if($request->ajax()){
            $Data = DataTables::of($authors)->addIndexColumn()->addColumn('action', function($row) {
                $button = '<div class="action-table"><a href="javascript:void(0)" data-id="'.$row->id .'" class="btn btn-info" id="editAuthor">Edit</a>';
                $button .= '<a href="javascript:void(0)" data-id="'.$row->id .'" class="btn btn-danger" id="deleteAuthor">Delete</a></div>';
                return $button;
            })->rawColumns(['action'])->make('true');
            return $Data;
        }
        return view('authors', compact('authors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required',
            'surname' => 'required|min:3',
            'patronymic'=>'nullable'
        ]);
       Authors::updateOrCreate(['id' => $request->author_id],
           [
               'name' => $request->name,
               'surname' => $request->surname,
               'patronymic' => $request->patronymic
           ]);
       return response()->json(['success' => 'Author Added Successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $authors = Authors::find($id);
        return response()->json($authors);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $author = Authors::findOrFail($id);
        $author->books()->detach();
        $author->delete();
        return response()->json(['success' => 'Author Deleted Successfully']);
    }
}
