<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Postingan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <h1>Edit Postingan</h1>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @if ($post->Foto)
            <p>Gambar saat ini:</p>
            <img src="{{ asset('storage/post/' . $post->Foto) }}" alt="Gambar {{ $post->Judul }}" width="180">
        @endif

        <div>
            <label for="Foto">Ganti gambar (opsional)</label>
            <input type="file" id="Foto" name="Foto" accept="image/jpeg,image/png">
        </div>

        <div>
            <label for="Judul">Judul</label>
            <input type="text" id="Judul" name="Judul" value="{{ old('Judul', $post->Judul) }}" maxlength="255" required>
        </div>

        <div>
            <label for="Deskripsi">Deskripsi</label>
            <textarea id="Deskripsi" name="Deskripsi" maxlength="255" required>{{ old('Deskripsi', $post->Deskripsi) }}</textarea>
        </div>

        <button type="submit">Simpan Perubahan</button>
    </form>

    <a href="{{ route('admin.beranda') }}">Kembali</a>
</body>
</html>