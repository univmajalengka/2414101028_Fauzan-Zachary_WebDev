<?php
include 'koneksi.php'; 

// Query untuk mengambil data paket wisata
$sql = "SELECT * FROM paket_wisata ORDER BY harga_dasar ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Beranda | Bandung Tour & Travel</title>
    <link rel="stylesheet" href="style.css"> 
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="header-content">
            <h1>Bandung Tour & Travel</h1>
            <nav>
                <a href="index.php" class="active"><i class='bx bx-home'></i> Beranda</a>
                <a href="pemesanan.php"><i class='bx bxs-plane-take-off'></i> Pesan Sekarang</a>
                <a href="modifikasi_pesanan.php"><i class='bx bx-edit-alt'></i> Modifikasi Pesanan</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero-section">
            <div class="hero-overlay">
                <h2>Jelajahi Pesona Kota Kembang</h2>
                <p>Rencanakan petualangan Anda dengan paket wisata terbaik, terjamin, dan tak terlupakan.</p>
                <a href="#packages" class="btn btn-submit btn-large">Temukan Paket Impian Anda <i class='bx bx-down-arrow-alt'></i></a>
            </div>
        </section>

        <section class="package-section">
            <h2 class="section-title"><i class='bx bxs-map'></i> Paket Wisata Unggulan Bandung</h2>
            <div class="package-list">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $harga_format = number_format($row['harga_dasar'], 0, ',', '.');
                        echo '<div class="package-card">';
                        echo '<div class="card-image-wrapper">';
                        // Asumsikan kolom 'gambar_url' berisi link gambar
                        $img_src = empty($row['gambar_url']) ? 'https://source.unsplash.com/400x300/?Bandung,tourism' : htmlspecialchars($row['gambar_url']);
                        echo '<img src="' . $img_src . '" alt="' . htmlspecialchars($row['nama_paket']) . '">';
                        echo '</div>';
                        
                        echo '<div class="card-body">';
                        echo '<h3>' . htmlspecialchars($row['nama_paket']) . '</h3>';
                        echo '<p class="package-desc">' . htmlspecialchars($row['deskripsi']) . '</p>';
                        
                        echo '<span class="price-tag">Mulai: Rp ' . $harga_format . '</span>';

                        echo '<div class="card-actions">';
                        // Tombol video yang mencolok
                        echo '<a href="https://www.youtube.com/watch?v=' . htmlspecialchars($row['video_youtube_id']) . '" target="_blank" class="btn btn-video" title="Lihat Video Promosi">';
                        echo '<i class="bx bxl-youtube"></i>';
                        echo '</a>';
                        // Tombol pesan yang besar dan jelas
                        echo '<a href="pemesanan.php?paket=' . $row['id_paket'] . '" class="btn btn-submit">';
                        echo 'Pesan Sekarang <i class="bx bx-right-arrow-alt"></i>';
                        echo '</a>';
                        echo '</div>'; // card-actions
                        echo '</div>'; // card-body
                        echo '</div>'; // package-card
                    }
                } else {
                    echo "<p class='no-data-message'>Belum ada paket wisata yang tersedia saat ini.</p>";
                }
                ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&#169; 2025 Bandung Tour & Travel. Explore the City of Flowers.</p>
    </footer>
</body>
</html>
<?php $conn->close(); ?>