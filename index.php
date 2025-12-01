<?php

// 1. Pembuatan Fungsi (Prosedur)
/**
 * Fungsi untuk menghitung diskon berdasarkan total belanja.
 * Menerima satu parameter: totalBelanja (nominal Rupiah).
 * Mengembalikan nilai diskon dalam bentuk nominal Rupiah.
 *
 * Logika Diskon:
 * - >= Rp. 100.000: Diskon 10%
 * - >= Rp. 50.000 dan < Rp. 100.000: Diskon 5%
 * - < Rp. 50.000: Tidak ada diskon (Rp. 0)
 */
function hitungDiskon($totalBelanja) {
    $diskon = 0; // Inisialisasi diskon [cite: 49]

    // Kondisi 1: Jika total belanja lebih dari atau sama dengan Rp. 100.000
    if ($totalBelanja >= 100000) {
        $diskon = 0.1 * $totalBelanja; // Diskon 10% 
    }
    // Kondisi 2: Jika total belanja lebih dari atau sama dengan Rp. 50.000 (dan otomatis kurang dari Rp. 100.000 karena tidak masuk kondisi if sebelumnya)
    elseif ($totalBelanja >= 50000) {
        $diskon = 0.05 * $totalBelanja; // Diskon 5% 
    }
    // Kondisi 3: Jika total belanja kurang dari Rp. 50.000 (diskon tetap Rp. 0 seperti inisialisasi awal) [cite: 86, 69, 42]

    return $diskon; // Mengembalikan nilai diskon nominal [cite: 88, 55, 27]
}


// 4. Eksekusi dan Output
// Deklarasikan variabel total belanja dengan contoh nilai Rp. 120.000 [cite: 91, 58, 30]
$totalBelanja = 120000;

// Panggil fungsi hitungDiskon() dan simpan hasilnya [cite: 92, 59, 31]
$diskon = hitungDiskon($totalBelanja);

// Hitung total yang harus dibayar [cite: 93]
$totalBayar = $totalBelanja - $diskon;

// Tampilkan hasil ke layar [cite: 94, 60, 61, 62, 32, 33, 34]
echo "--- Hasil Perhitungan Diskon ---" . "<br>";
echo "Total Belanja Awal: Rp. " . number_format($totalBelanja, 0, ',', '.') . "<br>";
echo "Diskon Diterima: Rp. " . number_format($diskon, 0, ',', '.') . "<br>";
echo "Total Yang Harus Dibayar: Rp. " . number_format($totalBayar, 0, ',', '.') . "<br>";
echo "------------------------------" . "<br>";


// Contoh output untuk total belanja Rp. 45.000 (tidak ada diskon)
$totalBelanja2 = 45000;
$diskon2 = hitungDiskon($totalBelanja2);
$totalBayar2 = $totalBelanja2 - $diskon2;

echo "<br>--- Contoh Lain (Belanja Rp. 45.000) ---" . "<br>";
echo "Total Belanja Awal: Rp. " . number_format($totalBelanja2, 0, ',', '.') . "<br>";
echo "Diskon Diterima: Rp. " . number_format($diskon2, 0, ',', '.') . "<br>";
echo "Total Yang Harus Dibayar: Rp. " . number_format($totalBayar2, 0, ',', '.') . "<br>";
echo "---------------------------------------" . "<br>";
?>