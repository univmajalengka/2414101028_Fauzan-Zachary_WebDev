document.addEventListener('DOMContentLoaded', function() {
    // --- Smooth Scrolling & ScrollSpy ---
    const sections = document.querySelectorAll('section');
    const navLi = document.querySelectorAll('header nav ul li a');
    
    // Sesuaikan offset karena hero padding besar
    const offset = 200; 

    function handleScroll() {
        let current = '';
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            // Gunakan window.scrollY atau pageYOffset
            if (window.scrollY >= sectionTop - offset) { 
                current = section.getAttribute('id');
            }
        });

        // Hapus class active dari navLi dan tambahkan ke yang aktif
        navLi.forEach(li => {
            li.classList.remove('active');
            if (li.getAttribute('href').includes(current)) {
                li.classList.add('active');
            }
            // Khusus untuk home/beranda
            if (current === 'hero' && li.getAttribute('href').includes('index.html')) {
                 li.classList.add('active');
            }
        });
    }

    window.addEventListener('scroll', handleScroll);

    navLi.forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            // Hanya lakukan smooth scroll jika link mengarah ke section di halaman yang sama
            if (href.startsWith('#')) {
                e.preventDefault();
                document.querySelector(href).scrollIntoView({
                    behavior: 'smooth'
                });
                // Update active class
                navLi.forEach(link => {
                    link.classList.remove('active');
                });
                this.classList.add('active');
            }
            // Jika link mengarah ke file lain (pemesanan.php/modifikasi_pesanan.php), biarkan default
        });
    });


    // --- Logika Card Click diubah ---
    // Sesuai spesifikasi, klik pada paket wisata (atau tombol) harus memicu Form Pemesanan. [cite: 139]
    // Karena Form Pemesanan idealnya ada di halaman terpisah (pemesanan.php),
    // kita akan memastikan tombol 'Pesan Sekarang!' mengarah ke sana.
    
    const pesanBtns = document.querySelectorAll('.btn-pesan');
    
    // Tambahkan event listener untuk tombol pemesanan
    pesanBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Kita biarkan browser mengikuti href="pemesanan.php"
            // Tidak ada pencegahan default (e.preventDefault())
            
            // Opsional: Anda bisa mengambil data paket dan menyimpannya ke localStorage
            // sebelum redirect, jika Anda ingin data paket terisi otomatis di form pemesanan.
            // Contoh:
            /*
            const card = this.closest('.card');
            const dataPaket = {
                judul: card.dataset.judul,
                harga: card.dataset.harga
                // ... data lain
            };
            localStorage.setItem('selectedPackage', JSON.stringify(dataPaket));
            */
            
            // Redirect ke halaman pemesanan.php
            window.location.href = 'pemesanan.php';
        });
    });
    
    // Logika Modal dihilangkan karena tidak digunakan untuk menampilkan detail destinasi lagi.
    // Anda akan membuat pemesanan.php (dengan PHP) untuk Form Pemesanan.

});