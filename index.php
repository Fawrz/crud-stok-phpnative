<?php
require_once 'config/database.php';

$pesan_status = '';
if (isset($_GET['status'])) {
    $status = $_GET['status'];
    if ($status == 'sukses_tambah') {
        $pesan_status = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                            Data barang berhasil ditambahkan!
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                         </div>";
    } elseif ($status == 'sukses_edit') {
        $pesan_status = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                            Data barang berhasil diperbarui!
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                         </div>";
    } elseif ($status == 'sukses_hapus') {
        $pesan_status = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                            Data barang berhasil dihapus!
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                         </div>";
    } elseif (in_array($status, ['gagal_hapus', 'id_tidak_ditemukan', 'id_tidak_valid'])) {
        $pesan_status = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                            Gagal menghapus data barang. Penyebab: " . htmlspecialchars($status) . "
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                         </div>";
    }
}

$sql = "SELECT id, kode, nama_barang, deskripsi, harga_satuan, jumlah, foto FROM barang ORDER BY dibuat_pada DESC";
$result = mysqli_query($koneksi, $sql);

if (!$result) {
    error_log("Query Error: " . mysqli_error($koneksi));
    die("Terjadi kesalahan saat mengambil data. Silakan coba lagi nanti.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Barang - Daftar Barang</title>
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
                        <a class="nav-link active" aria-current="page" href="index.php">Daftar Barang Tersedia</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4 main-content-container">
        <?php if (!empty($pesan_status)) { echo $pesan_status; } ?>
        
        <div class="d-flex justify-content-between align-items-center page-header-controls mb-3">
            <h1>Daftar Stok Barang yang Tersedia</h1>
            <a href="tambah_barang.php" class="btn btn-success">Tambah Barang Baru</a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No.</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Harga Satuan</th>
                        <th>Jumlah</th>
                        <th>Foto</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $nomor = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $nomor++; ?></td>
                                <td><?php echo htmlspecialchars($row['kode']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                                <td>Rp <?php echo number_format($row['harga_satuan'], 0, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($row['jumlah']); ?></td>
                                <td>
                                    <?php if (!empty($row['foto'])): ?>
                                        <?php
                                        $path_foto = 'uploads/' . htmlspecialchars($row['foto']);
                                        if (file_exists($path_foto)) {
                                            echo htmlspecialchars($row['foto']); 
                                        } else {
                                            echo "File tidak ditemukan.";
                                        }
                                        ?>
                                    <?php else: ?>
                                        Tidak ada foto
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="edit_barang.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm me-1 mb-1">Edit</a>
                                    <a href="hapus_barang.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini: <?php echo htmlspecialchars(addslashes($row['nama_barang'])); ?>?');">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data barang.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <?php
    if (isset($koneksi) && $koneksi) {
        mysqli_close($koneksi);
    }
    ?>
</body>
</html>