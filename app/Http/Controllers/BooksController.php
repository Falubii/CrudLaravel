<?php

namespace App\Http\Controllers;

use App\Models\Authors;
use App\Models\Books;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BooksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $books = Books::has('authors')->get();
        if($request->ajax()){
            $Data = DataTables::of($books)->addIndexColumn()->
            addColumn('image', function ($book) {
                return '<img src="'.asset('images/'.$book->image).'" alt="" width="100px">';
            })->addColumn('authors', function ($book) {
                $author_str = '';
                foreach ($book->authors as $author) {
                    $author_str .= $author->name . ' ' . $author->surname . ' ' . $author->patronymic . ', ';
                }
                return rtrim($author_str, ', ');
            })->addColumn('action', function($row) {
                $button = '<div class="action-table"><a href="javascript:void(0)" data-id="'.$row->id .'" class="btn btn-info" id="editBook">Edit</a>';
                $button .= '<a href="javascript:void(0)" data-id="'.$row->id .'" class="btn btn-danger" id="deleteBook">Delete</a></div>';
                return $button;
            })->rawColumns(['image', 'authors', 'action'])->make('true');
            return $Data;
        }
        return view('books', compact('books'));
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
            'title'       => 'required',
            'authors' => 'required|array',
            'image' => 'nullable|image|max:2048'
        ]);

        $book = Books::updateOrCreate(['id' => $request->book_id],
            [
                'title' => $request->title,
                'description' => $request->description,
                'publication_date' => $request->published
            ]);

        if($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            if (isset($book->image)) {
                $oldImagePath = public_path('images/'.$book->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $book->image = $imageName;
        }

        $book->save();

        $authors = Authors::find($request->authors);
        $book->authors()->sync($authors);

        return response()->json(['success' => 'Book Added Successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $book = Books::with('authors')->find($id);
        return response()->json($book);
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
        $book = Books::findOrFail($id);
        if(isset($book->image)){
            $oldImageName = $book->image;
            $oldImagePath = public_path('images/'.$oldImageName);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
        $book->authors()->detach();
        $book->delete();
        return response()->json(['success' => 'Book Successfully Deleted']);
    }
}
