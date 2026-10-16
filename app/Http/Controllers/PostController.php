<?php

namespace App\Http\Controllers;

use App\Models\posts;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PostController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $posts = posts::latest()->paginate(10);

        return view('admin.beranda', compact('posts'));
    }

    public function create(): View|RedirectResponse
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        return view('post.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $validated = $request->validate([
            'Foto' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'Judul' => ['required', 'string', 'min:5', 'max:255'],
            'Deskripsi' => ['required', 'string', 'min:10', 'max:255'],
        ]);

        $imageName = null;
        if ($request->hasFile('Foto')) {
            $imageName = $this->storeImage($request);
        }

        posts::create([
            'Foto' => $imageName,
            'Judul' => $validated['Judul'],
            'Deskripsi' => $validated['Deskripsi'],
        ]);

        return redirect()->route('admin.beranda')->with('success', 'Postingan berhasil ditambahkan.');
    }

    public function show(posts $post): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        return view('post.show', [
            'post' => $post,
            'isAdmin' => Auth::user()->role === 'admin',
        ]);
    }

    public function edit(posts $post): View|RedirectResponse
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        return view('post.edit', compact('post'));
    }

    public function update(Request $request, posts $post): RedirectResponse
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $validated = $request->validate([
            'Foto' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'Judul' => ['required', 'string', 'min:5', 'max:255'],
            'Deskripsi' => ['required', 'string', 'min:10', 'max:255'],
        ]);

        $oldImage = $post->Foto;
        $post->Judul = $validated['Judul'];
        $post->Deskripsi = $validated['Deskripsi'];

        if ($request->hasFile('Foto')) {
            $post->Foto = $this->storeImage($request);
        }

        $post->save();

        if ($request->hasFile('Foto') && $oldImage) {
            $this->deleteImage($oldImage);
        }

        return redirect()->route('admin.beranda')->with('success', 'Postingan berhasil diperbarui.');
    }

    public function destroy(posts $post): RedirectResponse
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $image = $post->Foto;
        $post->delete();

        if ($image) {
            $this->deleteImage($image);
        }

        return redirect()->route('admin.beranda')->with('success', 'Postingan berhasil dihapus.');
    }

    private function requireAdmin(): ?RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        abort_unless(Auth::user()->role === 'admin', 403);

        return null;
    }

    private function storeImage(Request $request): string
    {
        $image = $request->file('Foto');
        $imageName = $image->hashName();
        $storedPath = $image->storeAs('post', $imageName, 'public');

        if ($storedPath === false) {
            throw new RuntimeException('Gagal menyimpan gambar postingan.');
        }

        return $imageName;
    }

    private function deleteImage(string $imageName): void
    {
        $imagePath = 'post/'.$imageName;
        $disk = Storage::disk('public');

        if ($disk->exists($imagePath) && ! $disk->delete($imagePath)) {
            throw new RuntimeException('Gagal menghapus gambar postingan.');
        }
    }
}
