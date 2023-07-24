<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Movie;
use Illuminate\Support\Facades\Storage;


class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::all();
        return view('admin.movies', ['movies' => $movies]);
    }

    public function create()
    {
        return view('admin.movie-create');
    }

    public function edit($id)
    {
        $movie = Movie::find($id);
        return view('admin.movie-edit', ['movie' => $movie]);
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');

        $request->validate([
            'title' => 'required|string',
            'small_thumbnail' => 'required|image|mimes:jpeg,jpg,png',
            'large_thumbnail' => 'required|image|mimes:jpeg,jpg,png',
            'trailer' => 'required|url',
            'movie' => 'required|url',
            'casts' => 'required|string',
            'categories' => 'required|string',
            'release_date' => 'required|string',
            'about' => 'required|string',
            'short_about' => 'required|string',
            'duration' => 'required|string',
            'featured' => 'required'
        ]);

        $smallThumbnail = $request->file('small_thumbnail');
        $largeThumbnail = $request->file('large_thumbnail');
        $originalSmallThumbnailName = Str::random(10).$smallThumbnail->getClientOriginalName();
        $originalLargeThumbnailName = Str::random(10).$largeThumbnail->getClientOriginalName();

        $smallThumbnail->storeAs('public/thumbnails/small', $originalSmallThumbnailName);
        $largeThumbnail->storeAs('public/thumbnails/large', $originalLargeThumbnailName);
        
        $data['small_thumbnail'] = $originalSmallThumbnailName;
        $data['large_thumbnail'] = $originalLargeThumbnailName;

        Movie::create($data);

        // dd($data);

        return redirect()->route('admin.movie')->with('success', 'Movie created successfully!');

    }

    public function update(Request $request, $id){
        $data = $request->except('_token');

        $request->validate([
            'title' => 'required|string',
            'small_thumbnail' => 'image|mimes:jpeg,jpg,png',
            'large_thumbnail' => 'image|mimes:jpeg,jpg,png',
            'trailer' => 'required|url',
            'movie' => 'required|url',
            'casts' => 'required|string',
            'categories' => 'required|string',
            'release_date' => 'required|string',
            'about' => 'required|string',
            'short_about' => 'required|string',
            'duration' => 'required|string',
            'featured' => 'required'
        ]);

        $movie = Movie::find($id);

        if ($request->small_thumbnail) {
            // save new image
            $smallThumbnail = $request->file('small_thumbnail');
            $originalSmallThumbnailName = Str::random(10).$smallThumbnail->getClientOriginalName();
            $smallThumbnail->storeAs('public/thumbnails/small', $originalSmallThumbnailName);
            $data['small_thumbnail'] = $originalSmallThumbnailName;

            // delete old image
            Storage::delete('public/thumbnail/small/'.$movie->small_thumbnail);
        }

        if ($request->large_thumbnail) {
            // save new image
            $largeThumbnail = $request->file('large_thumbnail');
            $originalLargeThumbnailName = Str::random(10).$largeThumbnail->getClientOriginalName();
            $largeThumbnail->storeAs('public/thumbnails/large', $originalLargeThumbnailName);
            $data['large_thumbnail'] = $originalLargeThumbnailName;

            // delete old image
            Storage::delete('public/thumbnail/large/'.$movie->large_thumbnail); 
        }

        $movie->update($data);

        return redirect()->route('admin.movie')->with('success', 'Movie updated successfully!');
    }

    public function destroy($id)
    {
        Movie::find($id)->delete();
         
        return redirect()->route('admin.movie')->with('success', 'Movie deleted successfully!');
    }
}
