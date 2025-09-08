<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Akun;
use App\Http\Resources\PendaftaranResource;

class AkunController extends Controller
{
     public function index()
    {
        //get all posts
        $posts = Akun::get();

        //return collection of posts as a resource
        return new PendaftaranResource($posts);
    }

     public function store(Request $request)
    {
       $post = Akun::create($request->all());

        //return response
        return new PendaftaranResource($post);
    }

    public function show($id)
    {
        //find post by ID
        $post = Akun::find($id);

        //return single post as a resource
        return new PendaftaranResource($post);
    }

     public function update(Request $request, $id)
    {
        $post = Akun::find($id)->update($request->all()); 
        return new PendaftaranResource($post);
    }

    public function destroy($id)
    {

        //find post by ID
        $post = Akun::find($id);

        //delete post
        $post->delete();

        //return response
        return new PendaftaranResource(null);
    }
}
