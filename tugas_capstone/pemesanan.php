<?php
include 'koneksi.php'; 

$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- Logika PHP untuk Penyimpanan Data (Tetap Sama) ---
    if (empty($_POST['nama_pemesan']) || empty($_POST['nomor_hp']) || empty($_POST['tanggal_pesan']) || empty($_POST['waktu_perjalanan']) || empty($_POST['jumlah_peserta'])) {
        $message = "Gagal: Mohon lengkapi semua data form pemesanan yang wajib diisi.";
        $message_type = 'error';
    } else if (empty($_POST['harga_paket_perjalanan']) || empty($_POST['jumlah_tagihan'])) {
        $message = "Gagal: Harga Paket dan Jumlah Tagihan belum dihitung. Silakan klik tombol Hitung.";
        $message_type = 'error';
    } else {
        // Ambil dan sanitasi data (perlu membersihkan format Rupiah dari JS)
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

        // Simpan ke database
        $stmt = $conn->prepare("INSERT INTO pemesanan (nama_pemesan, nomor_hp, tanggal_pesan, waktu_perjalanan_hari, jumlah_peserta, penginapan, transportasi, service_makan, harga_paket, jumlah_tagihan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiisssii", $nama_pemesan, $nomor_hp, $tanggal_pesan, $waktu_perjalanan, $jumlah_peserta, $penginapan, $transportasi, $service_makan, $harga_paket, $jumlah_tagihan);

        if ($stmt->execute()) {
            $message = "Pemesanan berhasil disimpan! Total Tagihan: Rp " . number_format($jumlah_tagihan, 0, ',', '.') . ".";
            $message_type = 'success';
            $_POST = array(); 
        } else {
            $message = "Error penyimpanan data: " . $stmt->error;
            $message_type = 'error';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesan Paket Wisata Bandung - Interaktif</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'> 
</head>
<body>
    <header>
        <div class="header-content">
            <h1>Bandung Tour & Travel</h1>
            <nav>
                <a href="index.php"><i class='bx bx-home'></i> Beranda</a>
                <a href="modifikasi_pesanan.php"><i class='bx bx-edit-alt'></i> Modifikasi Pesanan</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="form-card-container">
            <h2><i class='bx bxs-plane-take-off'></i> Isi Detail Pemesanan Anda</h2>

            <?php if (!empty($message)): ?>
                <div class="alert <?php echo $message_type ?? ''; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form id="formPemesanan" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validasiForm()">
                
                <fieldset class="data-group">
                    <legend><i class='bx bxs-user-detail'></i> Data Pemesan</legend>
                    <div class="form-group-grid">
                        <div class="form-field">
                            <label for="nama_pemesan">Nama Pemesan <span class="required">*</span></label>
                            <input type="text" id="nama_pemesan" name="nama_pemesan" required value="<?php echo htmlspecialchars($_POST['nama_pemesan'] ?? ''); ?>" placeholder="Nama Anda">
                        </div>

                        <div class="form-field">
                            <label for="nomor_hp">Nomor HP/Telp <span class="required">*</span></label>
                            <input type="tel" id="nomor_hp" name="nomor_hp" required value="<?php echo htmlspecialchars($_POST['nomor_hp'] ?? ''); ?>" placeholder="08xxxxxxxxxx">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="data-group">
                    <legend><i class='bx bxs-calendar-check'></i> Detail Perjalanan</legend>
                    <div class="form-group-grid" style="grid-template-columns: 1fr 1fr 1fr;"> 
                        <div class="form-field">
                            <label for="tanggal_pesan">Tanggal Pesan <span class="required">*</span></label>
                            <input type="date" id="tanggal_pesan" name="tanggal_pesan" required value="<?php echo htmlspecialchars($_POST['tanggal_pesan'] ?? date('Y-m-d')); ?>">
                        </div>
                        
                        <div class="form-field">
                            <label for="waktu_perjalanan">Waktu Perjalanan (Hari) <span class="required">*</span></label>
                            <input type="number" id="waktu_perjalanan" name="waktu_perjalanan" min="1" required value="<?php echo htmlspecialchars($_POST['waktu_perjalanan'] ?? 1); ?>">
                        </div>
                        
                        <div class="form-field">
                            <label for="jumlah_peserta">Jumlah Peserta <span class="required">*</span></label>
                            <input type="number" id="jumlah_peserta" name="jumlah_peserta" min="1" required value="<?php echo htmlspecialchars($_POST['jumlah_peserta'] ?? 1); ?>">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="service-group">
                    <legend><i class='bx bxs-hand-right'></i> Pilih Pelayanan Tambahan</legend>
                    <p class="service-desc">Pilih layanan yang Anda inginkan. Harga paket dihitung berdasarkan pilihan di bawah.</p>
                    
                    <div class="service-card-group">
                        
                        <label for="penginapan" class="service-card" id="card-penginapan">
                            <input type="checkbox" id="penginapan" name="penginapan" value="1" hidden <?php echo isset($_POST['penginapan']) ? 'checked' : ''; ?>>
                            <div class="card-content">
                                <i class='bx bxs-hotel icon-large'></i>
                                <h4>Penginapan</h4>
                                <p>Rp 1.000.000</p>
                            </div>
                        </label>
                        
                        <label for="transportasi" class="service-card" id="card-transportasi">
                            <input type="checkbox" id="transportasi" name="transportasi" value="1" hidden <?php echo isset($_POST['transportasi']) ? 'checked' : ''; ?>>
                            <div class="card-content">
                                <i class='bx bxs-bus icon-large'></i>
                                <h4>Transportasi</h4>
                                <p>Rp 1.200.000</p>
                            </div>
                        </label>
                        
                        <label for="makan" class="service-card" id="card-makan">
                            <input type="checkbox" id="makan" name="makan" value="1" hidden <?php echo isset($_POST['makan']) ? 'checked' : ''; ?>>
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
                            <input type="text" id="harga_paket_perjalanan" name="harga_paket_perjalanan" readonly value="<?php echo htmlspecialchars($_POST['harga_paket_perjalanan'] ?? '0'); ?>">
                        </div>

                        <div class="form-field result-field">
                            <label for="jumlah_tagihan">Jumlah Tagihan Akhir</label>
                            <input type="text" id="jumlah_tagihan" name="jumlah_tagihan" readonly value="<?php echo htmlspecialchars($_POST['jumlah_tagihan'] ?? '0'); ?>">
                        </div>
                    </div>
                </fieldset>

                <div class="form-actions">
                    <button type="submit" name="simpan" class="btn btn-submit"><i class='bx bxs-check-circle'></i> Selesaikan & Simpan Pesanan</button>
                    <button type="reset" class="btn btn-reset"><i class='bx bx-trash'></i> Bersihkan Form</button>
                </div>
            </form>
        </div>
    </main>

    <script src="script.js"></script>
    <footer>
        <p>&#169; 2025 Bandung Tour & Travel. Explore the City of Flowers.</p>
    </footer>
</body>
</html>

<?php $conn->close(); ?>