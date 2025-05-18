<?php
require_once 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $kode = isset($_POST['kode']) ? htmlspecialchars(trim($_POST['kode'])) : '';
    $nama_barang = isset($_POST['nama_barang']) ? htmlspecialchars(trim($_POST['nama_barang'])) : '';
    $deskripsi = isset($_POST['deskripsi']) ? htmlspecialchars(trim($_POST['deskripsi'])) : '';
    $harga_satuan = isset($_POST['harga_satuan']) ? filter_var(trim($_POST['harga_satuan']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '';
    $jumlah = isset($_POST['jumlah']) ? filter_var(trim($_POST['jumlah']), FILTER_SANITIZE_NUMBER_INT) : '';

    $nama_file_foto = ""; 

    $errors = [];
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

    if (empty($errors) && isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";

        $ekstensi_file = strtolower(pathinfo(basename($_FILES["foto"]["name"]), PATHINFO_EXTENSION));
        $nama_file_unik = uniqid('foto_', true) . '.' . $ekstensi_file;
        $target_file = $target_dir . $nama_file_unik;

        $uploadOk = 1;
        $imageFileType = $ekstensi_file;

        $check = getimagesize($_FILES["foto"]["tmp_name"]);
        if ($check !== false) {
            // echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            $errors[] = "File yang diupload bukan gambar.";
            $uploadOk = 0;
        }

        // Cek ukuran file (misalnya, maks 2MB)
        if ($_FILES["foto"]["size"] > 2000000) { // 2MB dalam bytes
            $errors[] = "Maaf, ukuran file terlalu besar (maks 2MB).";
            $uploadOk = 0;
        }

        // Hanya izinkan format file tertentu
        $allowed_types = ['jpg', 'png', 'jpeg', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            $errors[] = "Maaf, hanya format JPG, JPEG, PNG & GIF yang diperbolehkan.";
            $uploadOk = 0;
        }

        // Cek jika $uploadOk bernilai 0 karena ada error
        if ($uploadOk == 0) {
        // Jika semua OK, coba upload file
        } else {
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                // echo "File ". htmlspecialchars(basename($_FILES["foto"]["name"])). " berhasil diupload sebagai " . $nama_file_unik;
                $nama_file_foto = $nama_file_unik;
            } else {
                $errors[] = "Maaf, terjadi error saat mengupload file Anda.";
            }
        }
    } elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] != UPLOAD_ERR_NO_FILE && $_FILES['foto']['error'] != UPLOAD_ERR_OK) {
        $errors[] = "Terjadi error pada file yang diupload (Error code: " . $_FILES['foto']['error'] . ").";
    }

    if (empty($errors)) {
        $sql_insert = "INSERT INTO barang (kode, nama_barang, deskripsi, harga_satuan, jumlah, foto) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($koneksi, $sql_insert);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssdis", $kode, $nama_barang, $deskripsi, $harga_satuan, $jumlah, $nama_file_foto);

            // Eksekusi statement
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                mysqli_close($koneksi);
                header("Location: index.php?status=sukses_tambah");
                exit();
            } else {
                $errors[] = "Gagal menyimpan data ke database: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            $errors[] = "Gagal mempersiapkan statement database: " . mysqli_error($koneksi);
        }
    }

    if (!empty($errors)) {
        $error_query_string = http_build_query(['errors' => $errors, 'prev_data' => $_POST]);
        header("Location: tambah_barang.php?" . $error_query_string);
        exit();
        /*
        echo "<h2>Terjadi Error:</h2>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
        echo "<a href='tambah_barang.php'>Kembali ke Form</a>";
        */
    }

} else {
    header("Location: tambah_barang.php");
    exit();
}

if (isset($koneksi)) {
    mysqli_close($koneksi);
}

?>