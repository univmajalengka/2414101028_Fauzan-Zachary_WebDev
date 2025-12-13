<?php
include 'koneksi.php'; 

$message = '';
$message_type = '';
$id_pesanan = (int)($_GET['id'] ?? 0); // Ambil ID dari URL

// ----------------------------------------------------
// A. LOGIKA UNTUK MEMUAT DATA LAMA (saat pertama kali dibuka)
// ----------------------------------------------------
if ($id_pesanan > 0) {
    $stmt = $conn->prepare("SELECT * FROM pemesanan WHERE id_pesanan = ?");
    $stmt->bind_param("i", $id_pesanan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data_lama = $result->fetch_assoc();
    } else {
        // Jika ID tidak ditemukan
        $message = "Error: Data pesanan dengan ID #" . $id_pesanan . " tidak ditemukan.";
        $message_type = 'error';
        $data_lama = null;
    }
    $stmt->close();
} else {
    // Jika tidak ada ID di URL
    header('Location: modifikasi_pesanan.php');
    exit();
}

// ----------------------------------------------------
// B. LOGIKA UNTUK UPDATE DATA (saat form di-submit)
// ----------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_pesanan'])) {
    
    // Validasi dasar (seperti di pemesanan.php)
    if (empty($_POST['nama_pemesan']) || empty($_POST['jumlah_tagihan'])) {
        $message = "Gagal: Mohon lengkapi semua data dan pastikan tagihan sudah dihitung.";
        $message_type = 'error';
    } else {
        // Ambil dan sanitasi data baru
        $nama_pemesan = $conn->real_escape_string($_POST['nama_pemesan']);
        $nomor_hp = $conn->real_escape_string($_POST['nomor_hp']);
        $tanggal_pesan = $conn->real_escape_string($_POST['tanggal_pesan']);
        $waktu_perjalanan = (int)$_POST['waktu_perjalanan'];
        $jumlah_peserta = (int)$_POST['jumlah_peserta'];
        
        $penginapan = isset($_POST['penginapan']) ? 'Y' : 'N';
        $transportasi = isset($_POST['transportasi']) ? 'Y' : 'N';
        $service_makan = isset($_POST['makan']) ? 'Y' : 'N';
        
        // Membersihkan string format Rupiah dari JS sebelum dimasukkan ke DB
        $harga_paket_raw = str_replace('.', '', $_POST['harga_paket_perjalanan']);
        $jumlah_tagihan_raw = str_replace('.', '', $_POST['jumlah_tagihan']);

        $harga_paket = (int)$harga_paket_raw;
        $jumlah_tagihan = (int)$jumlah_tagihan_raw;
        
        // Query UPDATE data
        $stmt_update = $conn->prepare("UPDATE pemesanan SET nama_pemesan=?, nomor_hp=?, tanggal_pesan=?, waktu_perjalanan_hari=?, jumlah_peserta=?, penginapan=?, transportasi=?, service_makan=?, harga_paket=?, jumlah_tagihan=? WHERE id_pesanan=?");
        
        $stmt_update->bind_param("sssiisssiii", 
            $nama_pemesan, $nomor_hp, $tanggal_pesan, $waktu_perjalanan, 
            $jumlah_peserta, $penginapan, $transportasi, $service_makan, 
            $harga_paket, $jumlah_tagihan, $id_pesanan);

        if ($stmt_update->execute()) {
            $message = "Pesanan ID #" . $id_pesanan . " berhasil **diperbarui**! Total Tagihan: Rp " . number_format($jumlah_tagihan, 0, ',', '.') . ".";
            $message_type = 'success';
            // Muat ulang data yang sudah diupdate agar form menampilkan data terbaru
            $data_lama = $conn->query("SELECT * FROM pemesanan WHERE id_pesanan = $id_pesanan")->fetch_assoc();
        } else {
            $message = "Error pembaruan data: " . $stmt_update->error;
            $message_type = 'error';
        }
        $stmt_update->close();
    }
}

// Gunakan data dari POST jika ada (setelah submit gagal) atau data lama (saat form dibuka)
$data = $_POST ?? $data_lama; 

// Jika $data_lama null (karena ID tidak valid), hentikan proses form
if (!$data_lama) {
    $conn->close();
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pesanan ID #<?php echo $id_pesanan; ?></title>
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
            <h2><i class='bx bx-edit-alt'></i> Edit Pesanan ID #<?php echo $id_pesanan; ?></h2>
            <p style="text-align:center;">Mengubah data pesanan yang sudah tersimpan di database.</p>

            <?php if (!empty($message)): ?>
                <div class="alert <?php echo $message_type ?? ''; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form id="formPemesanan" method="POST" action="edit_pesanan.php?id=<?php echo $id_pesanan; ?>" onsubmit="return validasiForm()">
                <input type="hidden" name="id_pesanan" value="<?php echo $id_pesanan; ?>">
                
                <fieldset class="data-group">
                    <legend><i class='bx bxs-user-detail'></i> Data Pemesan</legend>
                    <div class="form-group-grid">
                        <div class="form-field">
                            <label for="nama_pemesan">Nama Pemesan <span class="required">*</span></label>
                            <input type="text" id="nama_pemesan" name="nama_pemesan" required value="<?php echo htmlspecialchars($data['nama_pemesan'] ?? ''); ?>" placeholder="Nama Anda">
                        </div>

                        <div class="form-field">
                            <label for="nomor_hp">Nomor HP/Telp <span class="required">*</span></label>
                            <input type="tel" id="nomor_hp" name="nomor_hp" required value="<?php echo htmlspecialchars($data['nomor_hp'] ?? ''); ?>" placeholder="08xxxxxxxxxx">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="data-group">
                    <legend><i class='bx bxs-calendar-check'></i> Detail Perjalanan</legend>
                    <div class="form-group-grid" style="grid-template-columns: 1fr 1fr 1fr;"> 
                        <div class="form-field">
                            <label for="tanggal_pesan">Tanggal Pesan <span class="required">*</span></label>
                            <input type="date" id="tanggal_pesan" name="tanggal_pesan" required value="<?php echo htmlspecialchars($data['tanggal_pesan'] ?? date('Y-m-d')); ?>">
                        </div>
                        
                        <div class="form-field">
                            <label for="waktu_perjalanan">Waktu Perjalanan (Hari) <span class="required">*</span></label>
                            <input type="number" id="waktu_perjalanan" name="waktu_perjalanan" min="1" required value="<?php echo htmlspecialchars($data['waktu_perjalanan_hari'] ?? 1); ?>">
                        </div>
                        
                        <div class="form-field">
                            <label for="jumlah_peserta">Jumlah Peserta <span class="required">*</span></label>
                            <input type="number" id="jumlah_peserta" name="jumlah_peserta" min="1" required value="<?php echo htmlspecialchars($data['jumlah_peserta'] ?? 1); ?>">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="service-group">
                    <legend><i class='bx bxs-hand-right'></i> Pilih Pelayanan Tambahan</legend>
                    
                    <div class="service-card-group">
                        
                        <label for="penginapan" class="service-card" id="card-penginapan">
                            <input type="checkbox" id="penginapan" name="penginapan" value="1" hidden 
                            <?php echo (isset($data['penginapan']) && $data['penginapan'] == 'Y') ? 'checked' : ''; ?>>
                            <div class="card-content">
                                <i class='bx bxs-hotel icon-large'></i>
                                <h4>Penginapan</h4>
                                <p>Rp 1.000.000</p>
                            </div>
                        </label>
                        
                        <label for="transportasi" class="service-card" id="card-transportasi">
                            <input type="checkbox" id="transportasi" name="transportasi" value="1" hidden 
                            <?php echo (isset($data['transportasi']) && $data['transportasi'] == 'Y') ? 'checked' : ''; ?>>
                            <div class="card-content">
                                <i class='bx bxs-bus icon-large'></i>
                                <h4>Transportasi</h4>
                                <p>Rp 1.200.000</p>
                            </div>
                        </label>
                        
                        <label for="makan" class="service-card" id="card-makan">
                            <input type="checkbox" id="makan" name="makan" value="1" hidden 
                            <?php echo (isset($data['service_makan']) && $data['service_makan'] == 'Y') ? 'checked' : ''; ?>>
                            <div class="card-content">
                                <i class='bx bxs-bowl-hot icon-large'></i>
                                <h4>Service/Makan</h4>
                                <p>Rp 500.000</p>
                            </div>
                        </label>
                        
                    </div>
                </fieldset>

                <fieldset class="data-group calculation-group">
                    <legend><i class='bx bxs-wallet'></i> Rincian Tagihan Akhir</legend>
                    
                    <button type="button" onclick="hitungTagihan()" class="btn btn-calculate full-width-btn mb-15"><i class='bx bx-calculator'></i> Hitung Ulang Tagihan</button>

                    <div class="form-group-grid calculation-results">
                        <div class="form-field result-field">
                            <label for="harga_paket_perjalanan">Harga Paket Perjalanan (HPP)</label>
                            <input type="text" id="harga_paket_perjalanan" name="harga_paket_perjalanan" readonly 
                                value="<?php echo number_format($data['harga_paket'] ?? 0, 0, ',', '.'); ?>">
                        </div>

                        <div class="form-field result-field">
                            <label for="jumlah_tagihan">Jumlah Tagihan Akhir</label>
                            <input type="text" id="jumlah_tagihan" name="jumlah_tagihan" readonly 
                                value="<?php echo number_format($data['jumlah_tagihan'] ?? 0, 0, ',', '.'); ?>">
                        </div>
                    </div>
                </fieldset>

                <div class="form-actions">
                    <button type="submit" name="update_pesanan" class="btn btn-submit"><i class='bx bxs-save'></i> Simpan Perubahan</button>
                    <a href="modifikasi_pesanan.php" class="btn btn-reset"><i class='bx bx-arrow-back'></i> Kembali ke Daftar</a>
                </div>
            </form>
        </div>
    </main>

    <script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Panggil fungsi status kartu saat dokumen dimuat agar checkbox terlihat terpilih
            updateServiceCardStatus('penginapan'); 
            updateServiceCardStatus('transportasi'); 
            updateServiceCardStatus('makan'); 
            
            // Panggil hitungTagihan() setelah form dimuat untuk menampilkan nilai yang sudah terformat
            hitungTagihan(); 
        });
    </script>

    <footer>
        <p>&#169; 2025 Bandung Tour & Travel. Explore the City of Flowers.</p>
    </footer>
</body>
</html>

<?php $conn->close(); ?>