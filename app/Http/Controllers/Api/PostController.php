<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = post::latest()->paginate(5);

        return new PostResource(true, 'List Data Post', $posts);
    }

    public function store(Request $request)
    {
        // devine validation rules
        $Validator = Validator ::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required',
            'content' => 'required',
        ]);

        // oke
        // check if validations fails
        if ($Validator->fails()) {
            return response()->json($Validator->errors(), 422);
        }

        // upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        // create post
        $post = Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        // return response
        return new PostResource(true, 'Data Post Berhasil Di Tambahkan', $post);
    }

    public function show($id)
    {
        $post = Post::find($id);

        return new PostResource(true, 'Detail Data Post By ID', $post);
    }

    public function update(Request $request, $id)
    {
        // devine validation rules
        $Validator = Validator ::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
        ]);

        // check if validations fails
        if ($Validator->fails()) {
            return response()->json($Validator->errors(), 422);
        }
        // find post by ID
        $post = Post::find($id);

        if ($request->hasFile('image')) {

        // upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());
        
        // delete old image
        Storage::delete('public/posts' . basename($post->image));

        // update post with new image
        $post -> update([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

    }else{

        // Update post without image
        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);
    }

        // return response
        return new PostResource(true, 'Data Post Berhasil Di Ubah', $post);
    }

    public function destroy($id)
    {
    //find post by id
    $post = Post::find($id);

    // delete old image
    Storage::delete('public/posts' . basename($post->image));

    // delete post
    $post->delete();

         // return response
        return new PostResource(true, 'Data Post Berhasil Di Hapus', null);
    }
}
