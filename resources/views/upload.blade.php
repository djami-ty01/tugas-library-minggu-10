<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload File - BKPM Laravel</title>
</head>
<body>
    <h2>Upload File</h2>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('file.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="document">Pilih File (JPG, PNG, PDF, Max 2MB):</label><br><br>
            <input type="file" name="document" id="document" required>
        </div>
        <br>
        <button type="submit">Upload</button>
    </form>
</body>
</html>