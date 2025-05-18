<?php
require_once 'config/database.php';

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
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .container {
            width: 80%;
            margin: auto;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Daftar Barang</h1>
        <p>Berikut adalah daftar barang yang tersedia.</p>
        <a href="tambah_barang.php">Tambah Barang Baru</a>
        <br><br>

        <table>
            <thead>
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
                        
                        // Kolom aksi (Edit dan Hapus)
                        echo "<td>";
                        echo "<a href='edit_barang.php?id=" . $row['id'] . "'>Edit</a> | ";
                        echo "<a href='hapus_barang.php?id=" . $row['id'] . "' onclick='return confirm(\"Apakah Anda yakin ingin menghapus barang ini?\");'>Hapus</a>";
                        echo "</td>";
                        
                        echo "</tr>";
                    }
                } else {
                    // Jika tidak ada data
                    echo "<tr><td colspan='7' style='text-align:center;'>Belum ada data barang.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php
    if (isset($koneksi)) {
        mysqli_close($koneksi);
    }
    ?>
</body>
</html>