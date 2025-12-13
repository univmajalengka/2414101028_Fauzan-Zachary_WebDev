// script.js
function hitungTagihan() {
    const penginapan = document.getElementById('penginapan').checked;
    const transportasi = document.getElementById('transportasi').checked;
    const makan = document.getElementById('makan').checked;
    const waktuPerjalanan = parseInt(document.getElementById('waktu_perjalanan').value) || 0;
    const jumlahPeserta = parseInt(document.getElementById('jumlah_peserta').value) || 0;

    let hargaPaketPerjalanan = 0;

    // Menghitung Harga Paket Perjalanan berdasarkan pilihan pelayanan [cite: 21, 22, 23, 53, 54, 56]
    if (penginapan) {
        hargaPaketPerjalanan += 1000000; // Rp 1.000.000
    }
    if (transportasi) {
        hargaPaketPerjalanan += 1200000; // Rp 1.200.000
    }
    if (makan) {
        hargaPaketPerjalanan += 500000; // Rp 500.000
    }

    // Menghitung Jumlah Tagihan [cite: 28, 58]
    // Jumlah Tagihan = Waktu Perjalanan (Hari) x Jumlah Peserta x Harga Paket Perjalanan
    const jumlahTagihan = waktuPerjalanan * jumlahPeserta * hargaPaketPerjalanan;

    // Menampilkan hasil
    document.getElementById('harga_paket_perjalanan').value = hargaPaketPerjalanan;
    document.getElementById('jumlah_tagihan').value = jumlahTagihan;
}

// Tambahkan event listener untuk memanggil fungsi hitungTagihan saat input berubah
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formPemesanan');
    if (form) {
        form.addEventListener('change', hitungTagihan);
        form.addEventListener('input', hitungTagihan);
    }
});

// Fungsi Validasi dasar (Opsional, dapat diperluas)
function validasiForm() {
    const requiredFields = ['nama_pemesan', 'nomor_hp', 'tanggal_pesan', 'waktu_perjalanan', 'jumlah_peserta'];
    for (let i = 0; i < requiredFields.length; i++) {
        const field = document.getElementById(requiredFields[i]);
        if (!field.value) {
            alert('Mohon lengkapi semua data form pemesanan.'); // Pesan data harus terisi [cite: 50]
            field.focus();
            return false;
        }
    }
    // Pastikan Harga Paket dan Jumlah Tagihan sudah dihitung
    if (parseInt(document.getElementById('harga_paket_perjalanan').value) === 0 || parseInt(document.getElementById('jumlah_tagihan').value) === 0) {
        alert('Mohon pilih pelayanan dan isi Waktu Perjalanan/Jumlah Peserta untuk menghitung harga.');
        return false;
    }
    return true;
}