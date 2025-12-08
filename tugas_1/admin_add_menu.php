<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){ header("Location: admin_login.php"); exit; }

include 'data/menu.php';

$nama = $_POST['nama'];
$kategori = $_POST['kategori'];
$harga = $_POST['harga'];

$gambar = $_FILES['gambar']['name'];
move_uploaded_file($_FILES['gambar']['tmp_name'], 'img/'.$gambar);

$id_baru = end($menu)['id'] + 1;
$menu[] = ["id"=>$id_baru,"nama"=>$nama,"kategori"=>$kategori,"harga"=>$harga,"gambar"=>$gambar];

header('Location: admin_dashboard.php');
?>
