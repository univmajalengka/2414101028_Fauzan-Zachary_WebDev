<?php
session_start();
include 'data/menu.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Rumah Makan</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<nav>
  <h2>Dapur Cita Rasa</h2>
  <div class="nav-right">
     <div class="cart-container">
      <div class="cart-icon" onclick="toggleCartDropdown()">🛒 <span id="cart-count">0</span></div>
      <div id="cart-dropdown" class="cart-dropdown">
        <p id="empty-cart">Keranjang kosong</p>
        <div id="cart-items"></div>
        <div id="cart-footer" style="display:none;">
          <p>Total: Rp <span id="cart-total">0</span></p>
          <button onclick="checkout()">Checkout</button>
        </div>
      </div>
    </div>
    <!-- Tombol Login Admin -->
    <div class="admin-login-btn" onclick="bounceButton(this)">
      <a href="admin_login.php">Login Admin</a>
    </div>
  </div>
</nav>


<div class="jumbotron">
  <h1>Selamat Datang di Dapur Cita Rasa</h1>
  <p>Pemesanan cepat lezat dan mudah!</p>
</div>

<div class="kategori">
  <button onclick="filterCategory('All')">Semua</button>
  <button onclick="filterCategory('Makanan')">Makanan</button>
  <button onclick="filterCategory('Minuman')">Minuman</button>
</div>

<div class="cards-container">
<?php foreach($menu as $m): ?>
  <div class="card" data-kategori="<?php echo $m['kategori'];?>">
    <img src="img/<?php echo $m['gambar'];?>" alt="<?php echo $m['nama'];?>">
    <h3><?php echo $m['nama'];?></h3>
    <p>Rp <?php echo number_format($m['harga']);?></p>
    <div class="qty-controls">
      <button onclick="updateQty(<?php echo $m['id'];?>, -1)">-</button>
      <span id="qty-<?php echo $m['id'];?>">1</span>
      <button onclick="updateQty(<?php echo $m['id'];?>, 1)">+</button>
    </div>
    <button onclick="addToCart(<?php echo $m['id'];?>,'<?php echo $m['nama'];?>',<?php echo $m['harga'];?>,'img/<?php echo $m['gambar'];?>')">Tambah ke Keranjang</button>
  </div>
<?php endforeach; ?>
</div>

<?php include 'footer.php'; ?>
<script src="script.js"></script>
<script>
window.menuData = <?php echo json_encode($menu); ?>;
</script>
</body>
</html>
