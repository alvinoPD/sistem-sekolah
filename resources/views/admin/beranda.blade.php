<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Postingan Sekolah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header>
        <h1>Kelola Postingan Sekolah</h1>
        <p>Login sebagai {{ auth()->user()->name }} (Admin)</p>
        <a href="{{ route('posts.create') }}">Tambah Postingan</a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </header>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <main>
        @forelse ($posts as $post)
            <article>
                @if ($post->Foto)
                    <img src="{{ asset('storage/post/' . $post->Foto) }}" alt="Gambar {{ $post->Judul }}" width="180">
                @endif
                <h2>{{ $post->Judul }}</h2>
                <p>{{ \Illuminate\Support\Str::limit($post->Deskripsi, 180) }}</p>
                <a href="{{ route('posts.show', $post) }}">Lihat</a>
                <a href="{{ route('posts.edit', $post) }}">Edit</a>
                <form action="{{ route('posts.destroy', $post) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Hapus postingan ini?')">Hapus</button>
                </form>
            </article>
        @empty
            <p>Belum ada postingan sekolah.</p>
        @endforelse
    </main>

    {{ $posts->links() }}
</body>
</html>