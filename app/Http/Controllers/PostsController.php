<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Post;

class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')->paginate(10);
        return view('posts.index')->with('posts', $posts);
    }

    public function create()
    {
        return view('posts.create');
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
            'cover_image' => 'image|nullable|max:1999'
        ]);

        // Handle file upload
        if ($request->hasFile('cover_image')) {
            // Get filename with extension
            $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just extension
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            // Filename to store
            $filenameToStore = $filename . '_' . time() . '.' . $extension;
            // Upload img
            $path = $request->file('cover_image')->storeAs('public/cover_images', $filenameToStore);
        } else {
            $filenameToStore = 'noimage.jpg';
        }

        // Create Post
        $post = new Post;
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        $post->user_id = auth()->user()->id;
        $post->cover_image = $filenameToStore;
        $post->save();

        return redirect('/posts')->with('success', 'Post Created');
    }

    public function show($id)
    {
        $post = Post::find($id);
        return view('posts.show')->with('post', $post);
    }
   
    public function edit($id)
    {
        $post = Post::find($id);

        // Check for correct user
        if (auth()->user()->id !== $post->user_id) {
            return redirect('/posts')->with('error', 'Unauthorized Page');
        }

        return view('posts.edit')->with('post', $post);
    }
  
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required'
        ]);

        // Handle file upload
        if ($request->hasFile('cover_image')) {
            // Get filename with extension
            $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just extension
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            // Filename to store
            $filenameToStore = $filename . '_' . time() . '.' . $extension;
            // Upload img
            $path = $request->file('cover_image')->storeAs('public/cover_images', $filenameToStore);
        }

        // Find Post and Update
        $post = Post::find($id);
        $post->title = $request->input('title');
        $post->body = $request->input('body');

        if ($request->hasFile('cover_image')) {
            $post->cover_image = $filenameToStore;
        }

        $post->save();

        return redirect('/posts')->with('success', 'Post Updated');
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        // Check for correct user
        if (auth()->user()->id !== $post->user_id) {
            return redirect('/posts')->with('error', 'Unauthorized Page');
        }

        if ($post->cover_image != 'noimage.jpg') {
            // Delete img
            Storage::delete('public/cover_images/' . $post->cover_image);
        }
        
        $post->delete();
        return redirect('/posts')->with('success', 'Post Removed');
    }
}
