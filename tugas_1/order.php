<?php
include 'data/menu.php';
$id=$_GET['id']; $nama=$_GET['nama']; $hp=$_GET['hp']; $tanggal=$_GET['tanggal'];
$item=array_values(array_filter($menu, fn($m)=>$m['id']==$id))[0];
?>
<h1>Nota Pesanan</h1>
<p>Nama: <?php echo $nama;?></p>
<p>HP: <?php echo $hp;?></p>
<p>Tanggal Kunjungan: <?php echo $tanggal;?></p>
<table border="1">
<tr><th>Menu</th><th>Harga</th></tr>
<tr><td><?php echo $item['nama'];?></td><td><?php echo number_format($item['harga']);?></td></tr>
</table>
<p>Total: <?php echo number_format($item['harga']);?></p>
<button onclick="window.print()">Cetak Nota</button>
<a href="index.php">Kembali</a>
