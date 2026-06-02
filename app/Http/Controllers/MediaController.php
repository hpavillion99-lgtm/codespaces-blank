<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    // List all files on the dashboard
    public function index()
    {
        $files = Media::latest()->get();
        return view('dashboard', compact('files'));
    }

    // Handles standard hard-drive binary file uploads
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $path = $uploadedFile->store('uploads', 'public');

            Media::create([
                'name' => $uploadedFile->getClientOriginalName(),
                'file_path' => $path,
                'folder' => 'root',
                'file_type' => $uploadedFile->getClientMimeType(),
                'file_size' => $uploadedFile->getSize(),
            ]);

            return back()->with('success', 'Asset uploaded successfully!');
        }

        return back()->with('error', 'Upload handling failed.');
    }

    // Generates raw text/HTML/code files
    public function createFile(Request $request)
    {
        $fileName = $request->input('name', 'file.txt');
        $content = $request->input('content') ?? '';
        $folder = $request->input('folder', 'root');

        $safePath = 'uploads/' . uniqid() . '_' . $fileName;

        Storage::disk('public')->put($safePath, $content);

        Media::create([
            'name' => $fileName,
            'file_path' => $safePath,
            'folder' => $folder,
            'file_type' => 'text/plain',
            'file_size' => strlen($content),
        ]);

        return back()->with('success', 'File created successfully!');
    }

    // Handles updating existing file records (Both Name AND Code content)
    public function update(Request $request, $id)
    {
        $medium = Media::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);

        // 1. Update filename record in database
        $medium->update([
            'name' => $request->name,
        ]);

        // 2. Extract content safely (Fall back to empty string if null)
        $newContent = $request->input('content') ?? '';

        // 3. Force overwrite the storage file on the server disk
        Storage::disk('public')->put($medium->file_path, $newContent);

        // 4. Update the stored file size metadata in the database
        $medium->update([
            'file_size' => strlen($newContent),
        ]);

        return back()->with('success', 'File and code updated successfully!');
    }

    // Handle file deletion
    public function destroy($id)
    {
        $medium = Media::findOrFail($id);
        
        // Remove from physical disk
        Storage::disk('public')->delete($medium->file_path);
        
        // Remove from database
        $medium->delete();

        return back()->with('success', 'File deleted successfully!');
    }
}