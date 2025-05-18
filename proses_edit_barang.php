<?php

require_once 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil semua data dari form
    $id_barang = isset($_POST['id_barang']) ? trim($_POST['id_barang']) : '';
    $kode = isset($_POST['kode']) ? htmlspecialchars(trim($_POST['kode'])) : '';
    $nama_barang = isset($_POST['nama_barang']) ? htmlspecialchars(trim($_POST['nama_barang'])) : '';
    $deskripsi = isset($_POST['deskripsi']) ? htmlspecialchars(trim($_POST['deskripsi'])) : '';
    $harga_satuan = isset($_POST['harga_satuan']) ? filter_var(trim($_POST['harga_satuan']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '';
    $jumlah = isset($_POST['jumlah']) ? filter_var(trim($_POST['jumlah']), FILTER_SANITIZE_NUMBER_INT) : '';
    $foto_lama = isset($_POST['foto_lama']) ? trim($_POST['foto_lama']) : ''; // Nama file foto yang sudah ada

    $nama_file_foto_db = $foto_lama;
    $errors = [];

    // Validasi dasar
    if (empty($id_barang) || !is_numeric($id_barang)) {
        $errors[] = "ID barang tidak valid.";
    }
    if (empty($kode)) {
        $errors[] = "Kode barang wajib diisi.";
    }
    if (empty($nama_barang)) {
        $errors[] = "Nama barang wajib diisi.";
    }
    if (empty($harga_satuan) || !is_numeric($harga_satuan) || $harga_satuan < 0) {
        $errors[] = "Harga satuan wajib diisi dengan angka positif.";
    }
    if (empty($jumlah) || !is_numeric($jumlah) || $jumlah < 0) {
        $errors[] = "Jumlah stok wajib diisi dengan angka positif.";
    }

    // Proses Upload Foto Baru (jika ada file 'foto_baru' yang diupload)
    if (empty($errors) && isset($_FILES['foto_baru']) && $_FILES['foto_baru']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $ekstensi_file_baru = strtolower(pathinfo(basename($_FILES["foto_baru"]["name"]), PATHINFO_EXTENSION));
        $nama_file_unik_baru = uniqid('foto_', true) . '.' . $ekstensi_file_baru;
        $target_file_baru = $target_dir . $nama_file_unik_baru;
        $uploadOk_baru = 1;
        $imageFileType_baru = $ekstensi_file_baru;

        $check_baru = getimagesize($_FILES["foto_baru"]["tmp_name"]);
        if ($check_baru === false) {
            $errors[] = "File baru yang diupload bukan gambar.";
            $uploadOk_baru = 0;
        }

        if ($_FILES["foto_baru"]["size"] > 2000000) { // 2MB
            $errors[] = "Maaf, ukuran file foto baru terlalu besar (maks 2MB).";
            $uploadOk_baru = 0;
        }

        $allowed_types = ['jpg', 'png', 'jpeg', 'gif'];
        if (!in_array($imageFileType_baru, $allowed_types)) {
            $errors[] = "Maaf, hanya format JPG, JPEG, PNG & GIF yang diperbolehkan untuk foto baru.";
            $uploadOk_baru = 0;
        }

        if ($uploadOk_baru == 1) {
            if (move_uploaded_file($_FILES["foto_baru"]["tmp_name"], $target_file_baru)) {
                $nama_file_foto_db = $nama_file_unik_baru; // Gunakan nama file baru
                // Hapus foto lama jika ada dan berbeda dengan nama file default (jika ada)
                if (!empty($foto_lama) && file_exists($target_dir . $foto_lama)) {
                    unlink($target_dir . $foto_lama);
                }
            } else {
                $errors[] = "Maaf, terjadi error saat mengupload file foto baru Anda.";
            }
        }
    } elseif (isset($_FILES['foto_baru']) && $_FILES['foto_baru']['error'] != UPLOAD_ERR_NO_FILE && $_FILES['foto_baru']['error'] != UPLOAD_ERR_OK) {
        $errors[] = "Terjadi error pada file foto baru yang diupload (Error code: " . $_FILES['foto_baru']['error'] . ").";
    }

    if (empty($errors)) {
        $sql_update = "UPDATE barang SET kode = ?, nama_barang = ?, deskripsi = ?, harga_satuan = ?, jumlah = ?, foto = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($koneksi, $sql_update);

        if ($stmt_update) {
            mysqli_stmt_bind_param($stmt_update, "sssdisi", $kode, $nama_barang, $deskripsi, $harga_satuan, $jumlah, $nama_file_foto_db, $id_barang);

            if (mysqli_stmt_execute($stmt_update)) {
                mysqli_stmt_close($stmt_update);
                mysqli_close($koneksi);
                header("Location: index.php?status=sukses_edit");
                exit();
            } else {
                $errors[] = "Gagal mengupdate data barang: " . mysqli_stmt_error($stmt_update);
            }
            mysqli_stmt_close($stmt_update);
        } else {
            $errors[] = "Gagal mempersiapkan statement update database: " . mysqli_error($koneksi);
        }
    }

    // Jika ada error, redirect kembali ke form edit dengan pesan error dan data sebelumnya
    if (!empty($errors)) {
        // Ambil data yang disubmit untuk dikirim kembali
        $prev_data_from_post = $_POST; 
        // Hapus password atau data sensitif lain jika ada sebelum dikirim via GET
        // unset($prev_data_from_post['password_sensitif']); 

        $error_query_string = http_build_query(['errors_update' => $errors, 'prev_data_update' => $prev_data_from_post]);
        header("Location: edit_barang.php?id=" . $id_barang . "&" . $error_query_string);
        exit();
    }

} else {
    // Jika halaman diakses langsung tanpa POST data, redirect ke index
    header("Location: index.php");
    exit();
}

if (isset($koneksi)) {
    mysqli_close($koneksi);
}
?>