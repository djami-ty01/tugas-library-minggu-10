<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    // Menampilkan form upload
    public function create()
    {
        return view('upload');
    }

    // Memproses file upload dengan validasi dan kustomisasi nama
    public function upload(Request $request)
    {
        // Validasi File (Max 2MB, ekstensi jpg, png, pdf)
        $request->validate([
            'document' => 'required|file|mimes:jpg,png,pdf|max:2048',
        ]);

        // Ambil file dari request
        $file = $request->file('document');

        // Mengubah Nama File Sebelum Disimpan (Timestamp + Nama Asli)
        $filename = time() . '_' . $file->getClientOriginalName();

        // Simpan ke direktori 'documents' pada disk 'public'
        $path = $file->storeAs('documents', $filename, 'public');

        return back()->with('success', "File berhasil diunggah: $path");
    }
}