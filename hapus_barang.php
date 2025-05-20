<?php

require_once 'config/database.php';

$id_barang_dihapus = null;
$pesan_redirect = "status=gagal_hapus"; // Default pesan jika gagal

if (isset($_GET['id']) && !empty(trim($_GET['id'])) && is_numeric($_GET['id'])) {
    $id_barang_dihapus = (int) trim($_GET['id']);

    $sql_select_foto = "SELECT foto FROM barang WHERE id = ?";
    $stmt_select_foto = mysqli_prepare($koneksi, $sql_select_foto);
    $nama_file_foto_lama = null;

    if ($stmt_select_foto) {
        mysqli_stmt_bind_param($stmt_select_foto, "i", $id_barang_dihapus);
        mysqli_stmt_execute($stmt_select_foto);
        $result_foto = mysqli_stmt_get_result($stmt_select_foto);
        if ($row_foto = mysqli_fetch_assoc($result_foto)) {
            $nama_file_foto_lama = $row_foto['foto'];
        }
        mysqli_stmt_close($stmt_select_foto);
    } else {
    }

    $sql_delete = "DELETE FROM barang WHERE id = ?";
    $stmt_delete = mysqli_prepare($koneksi, $sql_delete);

    if ($stmt_delete) {
        mysqli_stmt_bind_param($stmt_delete, "i", $id_barang_dihapus);

        if (mysqli_stmt_execute($stmt_delete)) {
            if (mysqli_stmt_affected_rows($stmt_delete) > 0) {
                if (!empty($nama_file_foto_lama)) {
                    $path_foto_lama = "uploads/" . $nama_file_foto_lama;
                    if (file_exists($path_foto_lama)) {
                        unlink($path_foto_lama); // Hapus file foto dari server
                    }
                }
                $pesan_redirect = "status=sukses_hapus";
            } else {
                $pesan_redirect = "status=id_tidak_ditemukan";
            }
        } else {
            // Gagal eksekusi delete
        }
        mysqli_stmt_close($stmt_delete);
    } else {
        // Gagal mempersiapkan statement delete
    }
} else {
    $pesan_redirect = "status=id_tidak_valid";
}

mysqli_close($koneksi);

header("Location: index.php?" . $pesan_redirect);
exit();
?>
