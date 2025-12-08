<?php
session_start();
include 'data/menu.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $id=$_POST['id']; $action=$_POST['action'];
    if($action==='add') $_SESSION['cart'][$id]=($_SESSION['cart'][$id]??0)+1;
    elseif($action==='decrease'){ $_SESSION['cart'][$id]--; if($_SESSION['cart'][$id]<=0) unset($_SESSION['cart'][$id]);}
    elseif($action==='remove') unset($_SESSION['cart'][$id]);
    exit;
}

$total=0;
?>
<h1>Keranjang</h1>
<table border="1">
<tr><th>Nama</th><th>Qty</th><th>Harga</th><th>Subtotal</th><th>Aksi</th></tr>
<?php foreach($_SESSION['cart'] as $id=>$qty):
$item = array_values(array_filter($menu, fn($m)=>$m['id']==$id))[0];
$subtotal=$item['harga']*$qty;$total+=$subtotal;?>
<tr>
<td><?php echo $item['nama'];?></td>
<td><?php echo $qty;?></td>
<td><?php echo number_format($item['harga']);?></td>
<td><?php echo number_format($subtotal);?></td>
<td>
<form method="POST">
<input type="hidden" name="id" value="<?php echo $id;?>">
<button name="action" value="decrease">➖</button>
<button name="action" value="add">➕</button>
<button name="action" value="remove">Hapus</button>
</form>
</td>
</tr>
<?php endforeach; ?>
<tr><td colspan="3">Total</td><td colspan="2"><?php echo number_format($total);?></td></tr>
</table>
<a href="index.php">Kembali</a>
