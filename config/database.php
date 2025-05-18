<?php

$host = "localhost";
$username = "root";
$password = "";
$database_name = "crud_rpl";

$koneksi = mysqli_connect($host, $username, $password, $database_name);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8mb4");
echo "Koneksi database berhasil!";
?>