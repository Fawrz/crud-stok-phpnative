<?php
require_once 'config/database.php';

$id_barang_diedit = null;
$barang = null;
$errors = [];

if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    $id_barang_diedit = trim($_GET['id']);
    $sql_select_by_id = "SELECT id, kode, nama_barang, deskripsi, harga_satuan, jumlah, foto FROM barang WHERE id = ?";
    $stmt_select = mysqli_prepare($koneksi, $sql_select_by_id);
    if ($stmt_select) {
        mysqli_stmt_bind_param($stmt_select, "i", $id_barang_diedit);
        mysqli_stmt_execute($stmt_select);
        $result_select = mysqli_stmt_get_result($stmt_select);
        if (mysqli_num_rows($result_select) == 1) {
            $barang = mysqli_fetch_assoc($result_select);
        } else { 
            $errors[] = "Data barang dengan ID tersebut tidak ditemukan.";
        }
        mysqli_stmt_close($stmt_select);
    } else { 
        $errors[] = "Gagal mempersiapkan statement untuk mengambil data barang.";
    }
} else { 
    $errors[] = "ID barang tidak valid atau tidak disertakan.";
}

if (isset($_GET['errors_update']) && is_array($_GET['errors_update'])) {
    foreach ($_GET['errors_update'] as $error) { 
        $errors[] = htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
    }
}
$prev_data_update = [];
if (isset($_GET['prev_data_update']) && is_array($_GET['prev_data_update'])) {
     foreach ($_GET['prev_data_update'] as $key => $value) {
        $prev_data_update[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (!$barang && empty($prev_data_update) && empty($errors)) {
    $errors[] = "Data barang tidak dapat dimuat.";
}

$form_kode = isset($prev_data_update['kode']) ? $prev_data_update['kode'] : (isset($barang['kode']) ? htmlspecialchars($barang['kode'], ENT_QUOTES, 'UTF-8') : '');
$form_nama_barang = isset($prev_data_update['nama_barang']) ? $prev_data_update['nama_barang'] : (isset($barang['nama_barang']) ? htmlspecialchars($barang['nama_barang'], ENT_QUOTES, 'UTF-8') : '');
$form_deskripsi = isset($prev_data_update['deskripsi']) ? $prev_data_update['deskripsi'] : (isset($barang['deskripsi']) ? htmlspecialchars($barang['deskripsi'], ENT_QUOTES, 'UTF-8') : '');
$form_harga_satuan = isset($prev_data_update['harga_satuan']) ? $prev_data_update['harga_satuan'] : (isset($barang['harga_satuan']) ? htmlspecialchars($barang['harga_satuan'], ENT_QUOTES, 'UTF-8') : '');
$form_jumlah = isset($prev_data_update['jumlah']) ? $prev_data_update['jumlah'] : (isset($barang['jumlah']) ? htmlspecialchars($barang['jumlah'], ENT_QUOTES, 'UTF-8') : '');
$current_foto = isset($barang['foto']) ? htmlspecialchars($barang['foto'], ENT_QUOTES, 'UTF-8') : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang - <?php echo $form_nama_barang ?: 'Data Barang'; ?> - Aplikasi Stok Barang</title>
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
        <h1 class="mb-3">Edit Data Barang</h1>

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

        <?php if ($id_barang_diedit && $barang && (empty($errors) || !empty($prev_data_update) ) ): ?>
        <form action="proses_edit_barang.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_barang" value="<?php echo isset($barang['id']) ? htmlspecialchars($barang['id'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            
            <div class="mb-3">
                <label for="kode" class="form-label">Kode Barang</label>
                <input type="text" class="form-control" id="kode" name="kode" required value="<?php echo $form_kode; ?>">
            </div>

            <div class="mb-3">
                <label for="nama_barang" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="nama_barang" name="nama_barang" required value="<?php echo $form_nama_barang; ?>">
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?php echo $form_deskripsi; ?></textarea>
            </div>

            <div class="mb-3">
                <label for="harga_satuan" class="form-label">Harga Satuan (Rp)</label>
                <input type="number" class="form-control" id="harga_satuan" name="harga_satuan" required min="0" value="<?php echo $form_harga_satuan; ?>">
            </div>

            <div class="mb-3">
                <label for="jumlah" class="form-label">Jumlah Stok</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" required min="0" value="<?php echo $form_jumlah; ?>">
            </div>

            <div class="mb-3">
                <label for="foto" class="form-label">Foto Barang (Opsional: Ganti Foto)</label>
                <input type="file" class="form-control" id="foto" name="foto_baru">
                <div class="form-text">Kosongkan jika tidak ingin mengganti foto. Ukuran maks 2MB. Format: JPG, JPEG, PNG.</div>
                <?php if (!empty($current_foto)): ?>
                    <div class="mt-2">
                        <p class="mb-1">Foto Saat Ini:</p>
                        <img src="uploads/<?php echo $current_foto; ?>" alt="Foto <?php echo $form_nama_barang; ?>" style="max-width: 150px; max-height: 150px; border:1px solid #ddd;">
                        <input type="hidden" name="foto_lama" value="<?php echo $current_foto; ?>">
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary">Update Barang</button>
            <a href="index.php" class="btn btn-secondary ms-2">Batal</a>
        </form>
        <?php elseif (!empty($errors)): ?>
            <a href="index.php" class="btn btn-primary">Kembali ke Daftar Barang</a>
        <?php else: ?>
             <div class="alert alert-warning">Data barang tidak dapat dimuat atau ID tidak valid. Silakan kembali ke daftar barang.</div>
             <a href="index.php" class="btn btn-primary">Kembali ke Daftar Barang</a>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <?php
    if (isset($koneksi) && $koneksi) {
        mysqli_close($koneksi);
    }
    ?>
</body>
</html>