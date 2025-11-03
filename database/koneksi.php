<?php
$host = "localhost";
$user = "root"; // sesuaikan dengan user MySQL kamu
$pass = "";
$db = "karyawan";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if(!$koneksi){
    die("Koneksi gagal: " . mysqli_connect_error());
}

