<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->Judul }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main>
        <article>
            <h1>{{ $post->Judul }}</h1>
            @if ($post->Foto)
                <img src="{{ asset('storage/post/' . $post->Foto) }}" alt="Gambar {{ $post->Judul }}" width="600">
            @endif
            <p>{{ $post->Deskripsi }}</p>
            <small>Diterbitkan {{ $post->created_at->format('d M Y') }}</small>
        </article>

        @if ($isAdmin)
            <a href="{{ route('posts.edit', $post) }}">Edit Postingan</a>
            <a href="{{ route('admin.beranda') }}">Kembali ke Admin</a>
        @else
            <a href="{{ route('user.beranda') }}">Kembali ke Beranda</a>
        @endif
    </main>
</body>
</html>