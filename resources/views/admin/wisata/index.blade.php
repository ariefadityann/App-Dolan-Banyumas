<!DOCTYPE html>
<html>
<head>
    <title>Wisata</title>
</head>
<body>
    <h1>Data Wisata</h1>

    @foreach ($wisatas as $wisata)
        <p>{{ $wisata->nama }}</p>
    @endforeach
</body>
</html>
