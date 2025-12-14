<?php
include 'koneksi.php';

$message = '';
$message_type = '';

// --- LOGIKA HAPUS (DELETE) ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_pesanan = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM pemesanan WHERE id_pesanan = ?");
    $stmt->bind_param("i", $id_pesanan);
    if ($stmt->execute()) {
        $message = "Data pesanan berhasil dihapus.";
        $message_type = 'success';
    } else {
        $message = "Gagal menghapus data: " . $stmt->error;
        $message_type = 'error';
    }
    $stmt->close();
}

// --- LOGIKA TAMPILKAN DAFTAR ---
$sql = "SELECT * FROM pemesanan ORDER BY id_pesanan DESC";
$result = $conn->query($sql);

$has_data = $result->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar & Modifikasi Pesanan</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="header-content">
            <h1>Bandung Tour & Travel</h1>
            <nav>
                <a href="index.php"><i class='bx bx-home'></i> Beranda</a>
                <a href="pemesanan.php"><i class='bx bxs-plane-take-off'></i> Pesan Sekarang</a>
                <a href="modifikasi_pesanan.php" class="active"><i class='bx bx-edit-alt'></i> Modifikasi Pesanan</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="form-card-container">
            <h2><i class='bx bxs-list-ul'></i> Dashboard Pesanan Pelanggan</h2>

            <?php if (!empty($message)): ?>
                <div class="alert <?php echo $message_type ?? ''; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($has_data): ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID.</th>
                            <th>Nama Pemesan</th>
                            <th>Tgl & Waktu</th>
                            <th>Detail Peserta</th>
                            <th>Layanan</th>
                            <th>Total Tagihan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while($row = $result->fetch_assoc()) {
                            // Menghitung pelayanan yang dipilih
                            $pelayanan = [];
                            if ($row['penginapan'] == 'Y') $pelayanan[] = '<span class="badge success-badge">Penginapan</span>';
                            if ($row['transportasi'] == 'Y') $pelayanan[] = '<span class="badge primary-badge">Transportasi</span>';
                            if ($row['service_makan'] == 'Y') $pelayanan[] = '<span class="badge accent-badge">Makan</span>';
                            $pelayanan_str = empty($pelayanan) ? 'Tidak Ada' : implode(' ', $pelayanan);

                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row['id_pesanan']) . '</td>';
                            echo '<td class="nama-pemesan-col">' . htmlspecialchars($row['nama_pemesan']) . '</td>';
                            echo '<td>' . date('d/m/Y', strtotime($row['tanggal_pesan'])) . '</td>';
                            echo '<td>' . htmlspecialchars($row['jumlah_peserta']) . ' Psrt / ' . htmlspecialchars($row['waktu_perjalanan_hari']) . ' Hr</td>';
                            echo '<td>' . $pelayanan_str . '</td>';
                            echo '<td class="total-tagihan-col">Rp ' . number_format($row['jumlah_tagihan'], 0, ',', '.') . '</td>';
                            echo '<td class="text-center action-col">';
                            
                            // Tombol Edit
                            echo '<a href="edit_pesanan.php?id=' . $row['id_pesanan'] . '" class="btn btn-action btn-edit" title="Edit Data"><i class="bx bx-pencil"></i> Edit</a> ';
                            
                            // Tombol Delete dengan konfirmasi JS
                            echo '<a href="#" onclick="confirmDelete(' . $row['id_pesanan'] . ')" class="btn btn-action btn-delete" title="Hapus Data"><i class="bx bx-trash"></i> Hapus</a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <style>
                /* Styling Badge Tambahan untuk Interaktivitas di Tabel */
                .badge {
                    display: inline-block;
                    padding: 4px 8px;
                    border-radius: 5px;
                    font-size: 0.8em;
                    font-weight: 600;
                    margin: 2px 0;
                    color: white;
                }
                .success-badge { background-color: var(--success-color); }
                .primary-badge { background-color: var(--primary-color); }
                .accent-badge { background-color: var(--accent-color); }
            </style>

            <?php else: ?>
                <div class="no-data-message">
                    <p><i class='bx bx-info-circle'></i> Ups! Belum ada data pemesanan yang tersimpan.</p>
                    <a href="pemesanan.php" class="btn btn-calculate btn-large">Mulai Pesanan Baru <i class='bx bx-right-arrow-alt'></i></a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        function confirmDelete(id) {
            // 
            if (confirm("Anda yakin ingin menghapus pesanan dengan ID #" + id + "? Tindakan ini tidak dapat dibatalkan.")) {
                window.location.href = 'modifikasi_pesanan.php?action=delete&id=' + id;
            }
        }
    </script>
    
    <footer>
        <p>&#169; 2025 Bandung Tour & Travel. Explore the City of Flowers.</p>
    </footer>
</body>
</html>
<?php $conn->close(); ?>