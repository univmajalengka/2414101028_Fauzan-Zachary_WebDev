<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit;
}
include 'data/menu.php'; // data menu
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin</title>
<link rel="stylesheet" href="style.css">
<style>
/* ===== Body dan Layout ===== */
body{
    margin:0;
    font-family:sans-serif;
    background: linear-gradient(135deg,#f5a623,#f76b1c);
}

.dashboard-container{
    display:flex;
    flex-direction:column;
    align-items:center;
    padding:20px;
}

/* ===== Card Main Content ===== */
.main-content{
    width:90%;
    max-width:1000px;
    background:white;
    border-radius:20px;
    padding:30px;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
    animation:fadeIn 0.5s ease;
}

/* ===== Sidebar (opsional untuk versi mobile) ===== */
.sidebar{
    display:flex;
    justify-content:center;
    margin-bottom:20px;
}
.sidebar button{
    margin:0 10px;
    background:#f76b1c;
    color:white;
    border:none;
    padding:10px 15px;
    border-radius:10px;
    cursor:pointer;
    font-weight:bold;
    transition:0.3s;
}
.sidebar button:hover{
    background:#f5a623;
    color:#333;
}

/* ===== Section Card ===== */
.section{
    background:white;
    border-radius:15px;
    padding:20px;
    margin-bottom:20px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
    animation:fadeIn 0.5s ease;
}
.section h2{
    color:#f76b1c;
    margin-bottom:15px;
}

/* ===== Table Modern ===== */
table{
    width:100%;
    border-collapse:collapse;
    border-radius:10px;
    overflow:hidden;
}
table th, table td{
    padding:12px 15px;
    text-align:left;
}
table tr:nth-child(even){background:#f9f9f9;}
table tr:hover{background:#f5f5f5;}
table th{background:#f5a623;color:white;}

/* ===== Tombol ===== */
button.action-btn{
    background:#f76b1c;
    color:white;
    border:none;
    padding:8px 12px;
    border-radius:10px;
    cursor:pointer;
    transition:0.3s;
}
button.action-btn:hover{
    background:#f5a623;
    color:#333;
}

/* ===== Modal ===== */
.modal{
    display:none;
    position:fixed;
    top:0;left:0;width:100%;height:100%;
    background: rgba(0,0,0,0.5);
    justify-content:center;align-items:center;
}
.modal-content{
    background:white;
    padding:30px;
    border-radius:15px;
    width:400px;
    box-shadow:0 10px 30px rgba(0,0,0,0.2);
    animation:fadeIn 0.5s ease;
}
.close{float:right;font-size:24px;cursor:pointer;}

/* ===== Animasi ===== */
@keyframes fadeIn{
    from{opacity:0; transform: translateY(-20px);}
    to{opacity:1; transform: translateY(0);}
}

/* ===== Responsive ===== */
@media(max-width:768px){
    .main-content{padding:15px;}
    .sidebar{flex-direction:column;}
    .sidebar button{margin:5px 0;}
}
</style>
</head>
<body>

<div class="dashboard-container">

    <!-- Sidebar / Navigation -->
    <div class="sidebar">
        <button onclick="showSection('menu-section')">Daftar Menu</button>
        <button onclick="showSection('orders-section')">Pesanan</button>
        <button onclick="showSection('revenue-section')">Hasil Pendapatan</button>
        <button onclick="window.location.href='admin_logout.php'">Logout</button>
    </div>

    <!-- Main Content Card -->
    <div class="main-content">

        <!-- Daftar Menu -->
        <section id="menu-section" class="section active">
            <h2>Daftar Menu</h2>
            <button class="action-btn" onclick="showAddModal()">Tambah Menu</button>
            <table>
                <tr><th>ID</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Gambar</th><th>Aksi</th></tr>
                <?php foreach($menu as $m): ?>
                <tr>
                    <td><?php echo $m['id'];?></td>
                    <td><?php echo $m['nama'];?></td>
                    <td><?php echo $m['kategori'];?></td>
                    <td>Rp <?php echo number_format($m['harga']);?></td>
                    <td><img src="img/<?php echo $m['gambar'];?>" width="50"></td>
                    <td>
                        <button class="action-btn" onclick="openEditModal(<?php echo $m['id'];?>)">Edit</button>
                        <button class="action-btn" onclick="deleteMenu(<?php echo $m['id'];?>)">Hapus</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <!-- Pesanan -->
        <section id="orders-section" class="section">
            <h2>Daftar Pesanan</h2>
            <div id="orders-list">Belum ada pesanan</div>
        </section>

        <!-- Hasil Pendapatan -->
        <section id="revenue-section" class="section">
            <h2>Hasil Pendapatan</h2>
            <div id="revenue-total">Menghitung...</div>
        </section>

    </div>
</div>

<!-- Modal Tambah/Edit Menu -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span onclick="closeModal()" class="close">&times;</span>
        <h2 id="modal-title">Tambah Menu</h2>
        <form id="formMenu">
            <input type="hidden" id="menuId">
            <input type="text" id="menuNama" placeholder="Nama Menu" required><br><br>
            <input type="text" id="menuKategori" placeholder="Kategori" required><br><br>
            <input type="number" id="menuHarga" placeholder="Harga" required><br><br>
            <input type="text" id="menuGambar" placeholder="Nama File Gambar" required><br><br>
            <button type="submit" class="action-btn" id="modal-submit">Simpan</button>
        </form>
    </div>
</div>

<script src="admin_dashboard.js"></script>
<script>
window.menuData = <?php echo json_encode($menu); ?>;
window.ordersData = <?php echo json_encode($_SESSION['orders']); ?>;

// Render orders & revenue
renderOrders();
function showSection(id){
    document.querySelectorAll('.section').forEach(s=>s.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    if(id==='revenue-section') showRevenue();
}
</script>
</body>
</html>
