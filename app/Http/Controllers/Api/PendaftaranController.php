<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pendaftaran;
use App\Http\Resources\PendaftaranResource;

class PendaftaranController extends Controller
{
     public function index()
    {
        //get all posts
        $posts = Pendaftaran::get();

        //return collection of posts as a resource
        return new PendaftaranResource(true, 'List Data Pendaftaran', $posts);
    }

     public function store(Request $request)
    {
       $post = Pendaftaran::create($request->all());

        //return response
        return new PendaftaranResource(true, 'Data Pendaftaran Berhasil Ditambahkan!', $post);
    }

    public function show($id)
    {
        //find post by ID
        $post = Pendaftaran::find($id);

        //return single post as a resource
        return new PendaftaranResource(true, 'Detail Data Pendaftaran!', $post);
    }

     public function update(Request $request, $id)
    {
        $post = Pendaftaran::find($id)->update($request->all()); 
        return new PendaftaranResource(true, 'Detail Pendaftaran Telah Diubah', $post);
    }

    public function destroy($id)
    {

        //find post by ID
        $post = Pendaftaran::find($id);

        //delete post
        $post->delete();

        //return response
        return new PendaftaranResource(true, 'Data Pendaftaran Berhasil Dihapus!', null);
    }
}
