<!DOCTYPE html>
<html>
<head>
    <title>Subir archivo</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2 class="mt-5">Subir archivo</h2>
    <form action="{{ env('UPLOAD_URL') }}" method="POST" enctype="multipart/form-data" class="mt-3">
            @csrf
        <div class="form-group">
            <label for="file">Seleccionar archivo:</label>
            <input type="file" name="file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Subir</button>
    </form>
</div>
</body>
</html>
