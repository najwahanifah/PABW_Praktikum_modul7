<?php
namespace App\Http\Controllers;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk mengambil data useryang sedang login

use Illuminate\Support\Facades\Storage; // Untuk mengelola(menghapus) file di storage
class UploadController extends Controller
{
// READ: menampilkan daftar berkas milik user yang sedang login
public function index()
{
$uploads = Upload::where('user_id', Auth::id())->latest()->paginate(5);

return view('uploads.index', compact('uploads'));
}
// CREATE: menampilkan form unggah berkas
public function create()
{
return view('uploads.create');
}
// CREATE: memvalidasi dan menyimpan berkas baru
public function store(Request $request)
{
$request->validate([
'judul' => 'required|string|max:255',
'keterangan' => 'nullable|string',

'file' =>

'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048', //maks. 2 MB
]);
$file = $request->file('file'); // Ambil file dariform
$path = $file->store('uploads', 'public'); // Simpan kestorage/app/public/uploads

Upload::create([
'user_id' => Auth::id(),
'judul' => $request->judul,
'keterangan' => $request->keterangan,
'filename' => $file->getClientOriginalName(), // Nama asli file

'filepath' => $path, // Pathfile di storage

]);
return redirect()->route('uploads.index')->with('success',

'Berkas berhasil diunggah.');

}
// READ: menampilkan detail satu berkas
public function show(Upload $upload)
{
abort_if($upload->user_id !== Auth::id(), 403); // Hanya pemilik yang boleh melihat

return view('uploads.show', compact('upload'));
}
// UPDATE: menampilkan form edit berkas
public function edit(Upload $upload)
{
abort_if($upload->user_id !== Auth::id(), 403);
return view('uploads.edit', compact('upload'));
}
// UPDATE: menyimpan perubahan (file baru bersifat opsional)
public function update(Request $request, Upload $upload)
{
abort_if($upload->user_id !== Auth::id(), 403);
$request->validate([
'judul' => 'required|string|max:255',
'keterangan' => 'nullable|string',
'file' =>

'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',

]);

$data = $request->only('judul', 'keterangan');
if ($request->hasFile('file')) { // Jika user memilih file baru

Storage::disk('public')->delete($upload->filepath); //Hapus file lama

$file = $request->file('file');
$data['filename'] = $file->getClientOriginalName();
$data['filepath'] = $file->store('uploads', 'public');
}
$upload->update($data);
return redirect()->route('uploads.index')->with('success',

'Berkas berhasil diperbarui.');

}
// DELETE: menghapus file di storage dan data di database
public function destroy(Upload $upload)
{
abort_if($upload->user_id !== Auth::id(), 403);
Storage::disk('public')->delete($upload->filepath); // Hapus file fisik

$upload->delete(); // Hapus data di tabel uploads

return redirect()->route('uploads.index')->with('success',

'Berkas berhasil dihapus.');

}
}