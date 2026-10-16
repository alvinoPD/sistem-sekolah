<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Pengguna</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header>
        <h1>Postingan Sekolah</h1>
        <p>Selamat datang, {{ auth()->user()->name }}.</p>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </header>

    <main>
        @forelse ($posts as $post)
            <article>
                @if ($post->Foto)
                    <img src="{{ asset('storage/post/' . $post->Foto) }}" alt="Gambar {{ $post->Judul }}" width="180">
                @endif
                <h2>{{ $post->Judul }}</h2>
                <p>{{ \Illuminate\Support\Str::limit($post->Deskripsi, 180) }}</p>
                <a href="{{ route('posts.show', $post) }}">Baca selengkapnya</a>
            </article>
        @empty
            <p>Belum ada postingan sekolah.</p>
        @endforelse
    </main>

    {{ $posts->links() }}
</body>
</html>