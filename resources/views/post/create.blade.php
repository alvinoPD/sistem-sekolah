<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Postingan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <h2>Tambah Postingan</h2>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div>
            <label for="Foto">Gambar</label>
            <input type="file" id="Foto" name="Foto" accept="image/jpeg,image/png">
            <small>Opsional. Format JPG atau PNG, maksimal 2 MB.</small>
        </div>

        <div>
            <label for="Judul">Judul</label>
            <input type="text" id="Judul" name="Judul" value="{{ old('Judul') }}" maxlength="255" required>
        </div>

        <div>
            <label for="Deskripsi">Deskripsi</label>
            <textarea id="Deskripsi" name="Deskripsi" maxlength="255" required>{{ old('Deskripsi') }}</textarea>
        </div>

        <button type="submit">Simpan</button>
    </form>

    <a href="{{ route('admin.beranda') }}">Kembali</a>
</body>
</html>