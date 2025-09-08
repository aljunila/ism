<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file     = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path     = $file->storeAs('uploads', $filename, 'public'); 

            // TinyMCE butuh JSON { location: "URL" }
            return response()->json([
                'location' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
