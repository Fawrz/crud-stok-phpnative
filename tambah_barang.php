<?php
$errors = [];
$prev_data = [];

if (isset($_GET['errors']) && is_array($_GET['errors'])) {
    foreach ($_GET['errors'] as $error) {
        $errors[] = htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
    }
}

if (isset($_GET['prev_data']) && is_array($_GET['prev_data'])) {
    foreach ($_GET['prev_data'] as $key => $value) {
        $prev_data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang Baru - Aplikasi Stok Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/custom_style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Aplikasi Data Stok Toko Barang</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Daftar Barang Tersedia</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4 main-content-container">
        <h1 class="mb-3">Tambah Barang Baru</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <strong>Terjadi kesalahan:</strong>
                <ul>
                    <?php foreach ($errors as $error_msg): ?>
                        <li><?php echo $error_msg; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="proses_tambah_barang.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="kode" class="form-label">Kode Barang</label>
                <input type="text" class="form-control" id="kode" name="kode" required value="<?php echo isset($prev_data['kode']) ? $prev_data['kode'] : ''; ?>">
            </div>

            <div class="mb-3">
                <label for="nama_barang" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="nama_barang" name="nama_barang" required value="<?php echo isset($prev_data['nama_barang']) ? $prev_data['nama_barang'] : ''; ?>">
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?php echo isset($prev_data['deskripsi']) ? $prev_data['deskripsi'] : ''; ?></textarea>
            </div>

            <div class="mb-3">
                <label for="harga_satuan" class="form-label">Harga Satuan (Rp)</label>
                <input type="number" class="form-control" id="harga_satuan" name="harga_satuan" required min="0" value="<?php echo isset($prev_data['harga_satuan']) ? $prev_data['harga_satuan'] : ''; ?>">
            </div>

            <div class="mb-3">
                <label for="jumlah" class="form-label">Jumlah Stok</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" required min="0" value="<?php echo isset($prev_data['jumlah']) ? $prev_data['jumlah'] : ''; ?>">
            </div>

            <div class="mb-3">
                <label for="foto" class="form-label">Foto Barang</label>
                <input type="file" class="form-control" id="foto" name="foto">
                <div class="form-text">Ukuran foto maksimal 2MB. Format: JPG, JPEG, PNG.</div>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Barang</button>
            <a href="index.php" class="btn btn-secondary ms-2">Batal</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>