<?php
require_once 'config/database.php';

$pesan_status = '';
if (isset($_GET['status'])) {
    $status = $_GET['status']; // Ambil status sekali saja
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
    // Tambahkan status lain jika dibutuhkan
}

$sql = "SELECT id, kode, nama_barang, deskripsi, harga_satuan, jumlah, foto FROM barang ORDER BY dibuat_pada DESC";

$result = mysqli_query($koneksi, $sql);

if (!$result) {
    die("Query Error: " . mysqli_error($koneksi));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Stok Barang - Daftar Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    </head>
<body>
    <div class="container mt-4">
        <?php echo $pesan_status; ?>
        <h1 class="mb-3">Daftar Barang</h1>
        <p>Berikut adalah daftar barang yang tersedia.</p>
        <a href="tambah_barang.php" class="btn btn-primary mb-3">Tambah Barang Baru</a>
        <br><br>

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
                <?php
                if (mysqli_num_rows($result) > 0) {
                    $nomor = 1;
                    while ($row = mysqli_fetch_assoc($result)) { // Loop untuk setiap baris data
                        echo "<tr>";
                        echo "<td>" . $nomor++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['kode']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nama_barang']) . "</td>";
                        echo "<td>Rp " . number_format($row['harga_satuan'], 0, ',', '.') . "</td>";
                        echo "<td>" . htmlspecialchars($row['jumlah']) . "</td>";
                        
                        // Untuk foto
                        if (!empty($row['foto'])) {
                            echo "<td>" . htmlspecialchars($row['foto']) . "</td>";
                        } else {
                            echo "<td>Tidak ada foto</td>";
                        }
                        
                        // Kolom aksi (Edit dan Hapus) dengan class Bootstrap
                        echo "<td>";
                        echo "<a href='edit_barang.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm me-1'>Edit</a>";
                        echo "<a href='hapus_barang.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus barang ini?\");'>Hapus</a>";
                        echo "</td>";
                        
                        echo "</tr>";
                    }
                } else {
                    // Jika tidak ada data
                    echo "<tr><td colspan='7' class='text-center'>Belum ada data barang.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <?php
    if (isset($koneksi)) {
        mysqli_close($koneksi);
    }
    ?>
</body>
</html>