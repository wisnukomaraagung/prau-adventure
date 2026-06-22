<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function index()
    {
        $produks = Produk::with('kategori')->latest()->paginate(15);

        $today    = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();
        foreach ($produks as $produk) {
            $produk->stok_tersedia_sekarang = $produk->stokTersedia($today, $tomorrow);
            $produk->stok_terpakai = $produk->stok - $produk->stok_tersedia_sekarang;
        }

        return view('admin.produk.index', compact('produks'));
    }

    public function create()
    {
        $kategoris = Kategori::orderBy('nama')->get();
        return view('admin.produk.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kategori_id'   => 'required|exists:kategoris,id',
            'nama'          => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'harga_per_hari'=> 'required|numeric|min:0',
            'stok'          => 'required|integer|min:0',
            'foto'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $disk = Storage::disk('cloudinary');
            $file = $request->file('foto');
            // Simpan dengan nama tanpa ekstensi untuk hindari double ekstensi di Cloudinary
            $uniqueName = uniqid('foto_', true);
            $path = $file->storeAs('produk', $uniqueName, 'cloudinary');
            // Ambil version dari metadata untuk build URL Cloudinary yang valid
            $adapter = $disk->getAdapter();
            $meta = $adapter->lastUploadMetadata();
            $cloudName = config('filesystems.disks.cloudinary.cloud_name');
            $version = $meta['version'] ?? null;
            $filePath = $meta['path'] ?? $path;
            if ($version) {
                $data['foto'] = "https://res.cloudinary.com/{$cloudName}/image/upload/v{$version}/{$filePath}";
            } else {
                $data['foto'] = "https://res.cloudinary.com/{$cloudName}/image/upload/{$filePath}";
            }
        }

        Produk::create($data);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Produk $produk)
    {
        return view('admin.produk.show', compact('produk'));
    }

    public function edit(Produk $produk)
    {
        $kategoris = Kategori::orderBy('nama')->get();
        return view('admin.produk.edit', compact('produk', 'kategoris'));
    }

    public function update(Request $request, Produk $produk)
    {
        $data = $request->validate([
            'kategori_id'   => 'required|exists:kategoris,id',
            'nama'          => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'harga_per_hari'=> 'required|numeric|min:0',
            'stok'          => 'required|integer|min:0',
            'foto'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $disk = Storage::disk('cloudinary');
            $file = $request->file('foto');
            // Simpan dengan nama tanpa ekstensi untuk hindari double ekstensi di Cloudinary
            $uniqueName = uniqid('foto_', true);
            $path = $file->storeAs('produk', $uniqueName, 'cloudinary');
            // Ambil version dari metadata untuk build URL Cloudinary yang valid
            $adapter = $disk->getAdapter();
            $meta = $adapter->lastUploadMetadata();
            $cloudName = config('filesystems.disks.cloudinary.cloud_name');
            $version = $meta['version'] ?? null;
            $filePath = $meta['path'] ?? $path;
            if ($version) {
                $data['foto'] = "https://res.cloudinary.com/{$cloudName}/image/upload/v{$version}/{$filePath}";
            } else {
                $data['foto'] = "https://res.cloudinary.com/{$cloudName}/image/upload/{$filePath}";
            }
        }

        $produk->update($data);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {

        $produk->delete();

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil dihapus.');
    }
}
