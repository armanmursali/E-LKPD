<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKelasRequest;
use App\Http\Requests\UpdateKelasRequest;
use App\Models\Kelas;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('kelas/Index', [
            'kelas' => Kelas::where('user_id', Auth::id())->latest()->get(),
        ]);
    }

    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('kelas/Form', [
            'kelas' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKelasRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('public_hero_image')) {
            $data['public_hero_image'] = $request->file('public_hero_image')->store('kelas-hero', 'public');
        }
        $request->user()->kelas()->create($data);

        return to_route('kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kelas)
    {
        $this->authorize('view', $kelas);

        if (! $kelas->public_token) {
            $kelas->update(['public_token' => (string) Str::uuid()]);
        }

        return Inertia::render('kelas/Show', [
            'kelas' => $kelas,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kelas)
    {
        $this->authorize('update', $kelas);

        return Inertia::render('kelas/Form', [
            'kelas' => $kelas->only(['id', 'nama', 'deskripsi', 'public_hero_image']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKelasRequest $request, Kelas $kelas)
    {
        $this->authorize('update', $kelas);

        $data = $request->validated();
        if ($request->hasFile('public_hero_image')) {
            if ($kelas->public_hero_image && ! str_starts_with($kelas->public_hero_image, 'http')) {
                Storage::disk('public')->delete($kelas->public_hero_image);
            }
            $data['public_hero_image'] = $request->file('public_hero_image')->store('kelas-hero', 'public');
        }
        $kelas->update($data);

        return to_route('kelas.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kelas)
    {
        $this->authorize('delete', $kelas);

        $kelas->delete();

        return to_route('kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
