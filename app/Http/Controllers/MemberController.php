<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $members = Member::when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis_nim', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%");

            });
        })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return inertia('Members/Index', [
            'members' => $members,
            'search' => $request->search,
        ]);
    }

    public function create()
    {
        return inertia('Members/Create');
    }

    public function store(Request $request)
    {
        $foto = $request->file('foto');

        if ($foto && !$foto->isValid()) {
            return back()->withErrors([
                'foto' => 'Upload gagal. Kode PHP: '
                    . $foto->getError()
                    . '. ' . $foto->getErrorMessage(),
            ])->withInput();
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis_nim' => 'required|string|max:50|unique:members,nis_nim',
            'no_hp' => 'nullable|digits_between:8,15',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        ]);

        if ($foto) {
            try {
                $namaFoto = $foto->store('members', 'public');

                if (!$namaFoto || !Storage::disk('public')->exists($namaFoto)) {
                    return back()->withErrors([
                        'foto' => 'Foto gagal disimpan ke folder storage.',
                    ])->withInput();
                }

                $validated['foto'] = $namaFoto;

            } catch (\Throwable $e) {
                \Log::error('Upload foto anggota gagal: ' . $e->getMessage());

                return back()->withErrors([
                    'foto' => 'Terjadi kesalahan penyimpanan foto.',
                ])->withInput();
            }
        }

        Member::create($validated);

        return redirect()
            ->route('members.index')
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function show(Member $member)
    {
        //
    }

    public function edit(Member $member)
    {
        return inertia('Members/Edit', [
            'member' => $member,
        ]);
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis_nim' => 'required|string|max:50|unique:members,nis_nim,' . $member->id,
            'no_hp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        ]);

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');

            if (!$foto->isValid()) {
                return back()->withErrors([
                    'foto' => 'Upload gagal. Kode PHP: '
                        . $foto->getError()
                        . '. ' . $foto->getErrorMessage(),
                ])->withInput();
            }

            try {
                $namaFoto = $foto->store('members', 'public');

                if (!$namaFoto) {
                    return back()->withErrors([
                        'foto' => 'Foto baru gagal disimpan.',
                    ])->withInput();
                }

                if ($member->foto) {
                    Storage::disk('public')->delete($member->foto);
                }

                $validated['foto'] = $namaFoto;

            } catch (\Throwable $e) {
                \Log::error('Update foto anggota gagal: ' . $e->getMessage());

                return back()->withErrors([
                    'foto' => 'Terjadi kesalahan saat mengganti foto.',
                ])->withInput();
            }
        } else {
            unset($validated['foto']);
        }

        $member->update($validated);

        return redirect()
            ->route('members.index')
            ->with('success', 'Anggota berhasil diperbarui.');
    }

    public function destroy(Member $member)
    {
        if ($member->foto) {
            Storage::disk('public')->delete($member->foto);
        }

        $member->delete();

        return redirect()
            ->route('members.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }
}
