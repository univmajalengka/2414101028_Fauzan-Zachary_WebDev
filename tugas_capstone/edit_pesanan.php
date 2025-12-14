<?php
include 'koneksi.php'; 

$message = '';
$message_type = '';
$id_pesanan = (int)($_GET['id'] ?? 0); // Ambil ID dari URL
$data_lama = null; 

// ----------------------------------------------------
// A. LOGIKA UNTUK MEMUAT DATA LAMA (saat pertama kali dibuka)
// ----------------------------------------------------
if ($id_pesanan > 0) {
    $stmt = $conn->prepare("SELECT * FROM pemesanan WHERE id_pesanan = ?");
    $stmt->bind_param("i", $id_pesanan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data_lama = $result->fetch_assoc(); // Data lama sudah di load ke sini
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
// *Logika ini harus diselesaikan agar fungsi update berjalan.*
// ----------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_pesanan'])) {
    
    // 1. Sanitasi dan Ambil data
    $id_pesanan_post = (int)($_POST['id_pesanan'] ?? 0);
    $nama_pemesan = $conn->real_escape_string($_POST['nama_pemesan']);
    $nomor_hp = $conn->real_escape_string($_POST['nomor_hp']);
    $tanggal_pesan = $conn->real_escape_string($_POST['tanggal_pesan']);
    $waktu_perjalanan = (int)($_POST['waktu_perjalanan'] ?? 1);
    $jumlah_peserta = (int)($_POST['jumlah_peserta'] ?? 1);
    
    // Cekbox akan bernilai '1' jika dicentang, atau '0' jika tidak
    $penginapan = isset($_POST['penginapan']) ? 1 : 0;
    $transportasi = isset($_POST['transportasi']) ? 1 : 0;
    $makan = isset($_POST['makan']) ? 1 : 0;
    $catatan_khusus = $conn->real_escape_string($_POST['catatan_khusus'] ?? '');

    // Hapus format Rupiah dari input (asumsi format Rupiah diterapkan di JS/PHP sebelumnya)
    $harga_paket_perjalanan_raw = str_replace(['Rp ', '.', ','], '', ($_POST['harga_paket_perjalanan'] ?? '0'));
    $jumlah_tagihan_raw = str_replace(['Rp ', '.', ','], '', ($_POST['jumlah_tagihan'] ?? '0'));

    $harga_paket_perjalanan = (int)filter_var($harga_paket_perjalanan_raw, FILTER_SANITIZE_NUMBER_INT);
    $jumlah_tagihan = (int)filter_var($jumlah_tagihan_raw, FILTER_SANITIZE_NUMBER_INT);


    // 2. Query Update
    $stmt_update = $conn->prepare("UPDATE pemesanan SET 
        nama_pemesan=?, nomor_hp=?, tanggal_pesan=?, waktu_perjalanan=?, jumlah_peserta=?, 
        penginapan=?, transportasi=?, makan=?, catatan_khusus=?, 
        harga_paket_perjalanan=?, jumlah_tagihan=? 
        WHERE id_pesanan = ?");
    
    $stmt_update->bind_param("sssiisiisiii", 
        $nama_pemesan, $nomor_hp, $tanggal_pesan, $waktu_perjalanan, $jumlah_peserta, 
        $penginapan, $transportasi, $makan, $catatan_khusus, 
        $harga_paket_perjalanan, $jumlah_tagihan, $id_pesanan_post);

    if ($stmt_update->execute()) {
        $message = "Pesanan berhasil diperbarui!";
        $message_type = 'success';
        // Redirect ke daftar setelah berhasil
        header('Location: modifikasi_pesanan.php?status=success_edit');
        exit();
    } else {
        $message = "Gagal memperbarui data: " . $stmt_update->error;
        $message_type = 'error';
        // Muat ulang data POST agar input yang gagal tidak hilang
        $data_lama = $_POST;
        $id_pesanan = $id_pesanan_post;
    }
    $stmt_update->close();
}


// ----------------------------------------------------
// C. Fungsi Bantuan untuk data lama dan format Rupiah (PENTING)
// ----------------------------------------------------
// Digunakan di HTML untuk mengisi nilai lama
function get_old_value($key, $default = '') {
    global $data_lama;
    // Ambil nilai dari $data_lama (yang berisi data dari DB atau data POST)
    return htmlspecialchars($data_lama[$key] ?? $default);
}

// Digunakan di HTML untuk mengisi checked pada checkbox
function is_checked($key) {
    global $data_lama;
    // Cek apakah nilai kolomnya adalah 1
    if (($data_lama[$key] ?? 0) == 1) {
        return 'checked';
    }
    return '';
}

// Digunakan untuk menampilkan format Rupiah (hanya untuk tampilan)
function format_rupiah($angka) {
    if (!is_numeric($angka)) {
        // Jika nilai dari DB adalah string, coba konversi ke integer
        $angka = (int)str_replace(['Rp ', '.', ','], '', $angka);
    }
    return number_format($angka, 0, ',', '.');
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pesanan | ID #<?php echo $id_pesanan; ?></title>
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
        <div class="container">
            <h2>Edit Pesanan #<?php echo $id_pesanan; ?></h2>
            
            <?php if (!empty($message)): ?>
                <div class="message <?php echo $message_type; ?>">
                    <p><?php echo htmlspecialchars($message); ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($data_lama): ?>
            
            <form action="edit_pesanan.php" method="POST" class="form-container" id="formPemesanan">
                
                <input type="hidden" name="id_pesanan" value="<?php echo htmlspecialchars($id_pesanan); ?>">
                <input type="hidden" name="id_paket" value="<?php echo get_old_value('id_paket'); ?>">

                <fieldset class="section-fieldset">
                    <legend><i class='bx bx-user'></i> Data Pemesan</legend>
                    
                    <div class="form-group-grid">
                        <div class="form-field">
                            <label for="nama_pemesan">Nama Pemesan</label>
                            <input type="text" id="nama_pemesan" name="nama_pemesan" 
                                value="<?php echo get_old_value('nama_pemesan'); ?>" required>
                        </div>
                        
                        <div class="form-field">
                            <label for="nomor_hp">Nomor HP/WhatsApp</label>
                            <input type="text" id="nomor_hp" name="nomor_hp" 
                                value="<?php echo get_old_value('nomor_hp'); ?>" required>
                        </div>
                        
                        <div class="form-field">
                            <label for="tanggal_pesan">Tanggal Keberangkatan</label>
                            <input type="date" id="tanggal_pesan" name="tanggal_pesan" 
                                value="<?php echo get_old_value('tanggal_pesan'); ?>" required>
                        </div>

                        <div class="form-field">
                            <label for="waktu_perjalanan">Waktu Perjalanan (Hari)</label>
                            <input type="number" id="waktu_perjalanan" name="waktu_perjalanan" min="1" max="30" 
                                value="<?php echo get_old_value('waktu_perjalanan', 1); ?>" required>
                        </div>
                        
                        <div class="form-field">
                            <label for="jumlah_peserta">Jumlah Peserta</label>
                            <input type="number" id="jumlah_peserta" name="jumlah_peserta" min="1" max="100" 
                                value="<?php echo get_old_value('jumlah_peserta', 1); ?>" required>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="section-fieldset">
                    <legend><i class='bx bx-briefcase'></i> Pilihan Pelayanan Tambahan</legend>
                    
                    <div class="service-cards-grid">
                        
                        <div class="service-card" id="card-penginapan">
                            <input type="checkbox" id="penginapan" name="penginapan" value="1" 
                                <?php echo is_checked('penginapan'); ?>>
                            <label for="penginapan">
                                <i class='bx bxs-hotel'></i>
                                <h4>Penginapan (Hotel/Villa)</h4>
                                <p>Fasilitas menginap yang nyaman dan strategis.</p>
                                <span class="service-price">Rp 1.000.000 / Hari / Peserta</span>
                            </label>
                        </div>
                        
                        <div class="service-card" id="card-transportasi">
                            <input type="checkbox" id="transportasi" name="transportasi" value="1"
                                <?php echo is_checked('transportasi'); ?>>
                            <label for="transportasi">
                                <i class='bx bxs-bus'></i>
                                <h4>Transportasi Lokal</h4>
                                <p>Akses mobil/bus AC untuk mobilitas selama wisata.</p>
                                <span class="service-price">Rp 1.200.000 / Hari / Peserta</span>
                            </label>
                        </div>

                        <div class="service-card" id="card-makan">
                            <input type="checkbox" id="makan" name="makan" value="1"
                                <?php echo is_checked('makan'); ?>>
                            <label for="makan">
                                <i class='bx bxs-bowl-hot'></i>
                                <h4>Jatah Makan</h4>
                                <p>Paket makan 3x sehari selama perjalanan.</p>
                                <span class="service-price">Rp 500.000 / Hari / Peserta</span>
                            </label>
                        </div>
                        
                    </div>
                    
                    <div class="form-field full-width">
                        <label for="catatan_khusus">Catatan Khusus (Alergi makanan, kebutuhan khusus, dll.)</label>
                        <textarea id="catatan_khusus" name="catatan_khusus" rows="4"><?php echo get_old_value('catatan_khusus'); ?></textarea>
                    </div>
                </fieldset>

                <fieldset class="section-fieldset calculation-section">
                    <legend><i class='bx bx-calculator'></i> Perhitungan Tagihan</legend>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-calculate" onclick="hitungTagihan()"><i class='bx bx-calculator'></i> Hitung Ulang Tagihan</button>
                    </div>

                    <div class="form-group-grid calculation-results">
                        <div class="form-field result-field">
                            <label for="harga_paket_perjalanan">Harga Paket Perjalanan (HPP)</label>
                            <input type="text" id="harga_paket_perjalanan" name="harga_paket_perjalanan" readonly 
                                value="Rp <?php echo format_rupiah(get_old_value('harga_paket_perjalanan', 0)); ?>">
                        </div>

                        <div class="form-field result-field">
                            <label for="jumlah_tagihan">Jumlah Tagihan Akhir</label>
                            <input type="text" id="jumlah_tagihan" name="jumlah_tagihan" readonly 
                                value="Rp <?php echo format_rupiah(get_old_value('jumlah_tagihan', 0)); ?>">
                        </div>
                    </div>
                </fieldset>

                <div class="form-actions">
                    <button type="submit" name="update_pesanan" class="btn btn-submit"><i class='bx bxs-save'></i> Simpan Perubahan</button>
                    <a href="modifikasi_pesanan.php" class="btn btn-reset"><i class='bx bx-arrow-back'></i> Kembali ke Daftar</a>
                </div>
            </form>
            
            <?php endif; ?>

        </div>
    </main>

    <script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Panggil fungsi status kartu saat dokumen dimuat agar checkbox terlihat terpilih
            // (Asumsi fungsi updateServiceCardStatus ada di script.js atau Anda tambahkan manual)
            updateServiceCardStatus('penginapan'); 
            updateServiceCardStatus('transportasi'); 
            updateServiceCardStatus('makan'); 
            
            // Panggil hitungTagihan() setelah form dimuat (untuk inisialisasi dan memastikan format Rupiah)
            hitungTagihan(); 
        });
    </script>

    <footer>
        <p>&#169; 2025 Bandung Tour & Travel. Explore the City of Flowers.</p>
    </footer>
</body>
</html>
<?php $conn->close(); ?>