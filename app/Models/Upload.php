<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Upload extends Model
{
protected $table = 'uploads'; // Nama tabel yang dihubungkan
// Kolom yang boleh diisi secara massal (mass assignment)
protected $fillable = [
'user_id',
'judul',
'keterangan',
'filename',
'filepath',
];
// Relasi: setiap berkas dimiliki oleh satu user
public function user()
{
return $this->belongsTo(User::class);
}
}