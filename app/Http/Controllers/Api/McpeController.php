<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\McpeResource;
use App\Models\mcpe;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class McpeController extends Controller
{
    public function index()
    {
        $mcpe = mcpe::latest()->paginate(5);

        return new McpeResource(true, 'List Data Mcpe', $mcpe);
    }

    public function store(Request $request)
    {
        // devine validation rules
        $Validator = Validator ::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'item' => 'required',
            'deskripsi' => 'required',
        ]);

        // oke
        // check if validations fails
        if ($Validator->fails()) {
            return response()->json($Validator->errors(), 422);
        }

        // upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        // create mcpe
        $mcpe = mcpe::create([
            'image' => $image->hashName(),
            'item' => $request->item,
            'deskripsi' => $request->deskripsi,
        ]);

        // return response
        return new McpeResource(true, 'Data Mcpe Berhasil Di Tambahkan', $mcpe);
    }

    public function show($id)
    {
        $mcpe = mcpe::find($id);

        return new McpeResource(true, 'Detail Data Mcpe By ID', $mcpe);
    }

    public function update(Request $request, $id)
    {
        // devine validation rules
        $Validator = Validator ::make($request->all(), [
            'item' => 'required',
            'deskripsi' => 'required',
        ]);

        // check if validations fails
        if ($Validator->fails()) {
            return response()->json($Validator->errors(), 422);
        }
        // find mcpe by ID
        $mcpe = mcpe::find($id);

        if ($request->hasFile('image')) {

        // upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());
        
        // delete old image
        Storage::delete('public/posts' . basename($mcpe->image));

        // update mcpe with new image
        $mcpe -> update([
            'image' => $image->hashName(),
            'item' => $request->item,
            'deskripsi' => $request->deskripsi,
        ]);

    }else{

        // Update mcpe without image
        $mcpe->update([
            'item' => $request->item,
            'deskripsi' => $request->deskripsi,
        ]);
    }

        // return response
        return new McpeResource(true, 'Data Mcpe Berhasil Di Ubah', $mcpe);
    }

    public function destroy($id)
    {
    //find mcpe by id
    $mcpe = mcpe::find($id);

    // delete old image
    Storage::delete('public/posts' . basename($mcpe->image));

    // delete mcpe
    $mcpe->delete();

         // return response
        return new McpeResource(true, 'Data Mcpe Berhasil Di Hapus', null);
    }
}
