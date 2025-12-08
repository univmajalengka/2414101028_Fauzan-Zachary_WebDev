<?php
$menu = [
    ["id"=>1, "nama"=>"Nasi Goreng", "kategori"=>"Makanan", "harga"=>20000, "gambar"=>"nasi-goreng.jpg"],
    ["id"=>2, "nama"=>"Mie Ayam", "kategori"=>"Makanan", "harga"=>15000, "gambar"=>"mie-ayam.jpg"],
    ["id"=>3, "nama"=>"Es Teh", "kategori"=>"Minuman", "harga"=>5000, "gambar"=>"es-teh.jpg"],
    ["id"=>4, "nama"=>"Es Jeruk", "kategori"=>"Minuman", "harga"=>12000, "gambar"=>"es-jeruk.jpg"]
];

// Simpan keranjang di session
if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if(!isset($_SESSION['orders'])) $_SESSION['orders'] = [];
?>
