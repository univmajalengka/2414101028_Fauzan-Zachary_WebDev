document.addEventListener('DOMContentLoaded', function() {
    // --- Smooth Scrolling & ScrollSpy ---
    const sections = document.querySelectorAll('section');
    const navLi = document.querySelectorAll('header nav ul li a');

    function handleScroll() {
        let current = '';
        const offset = 200;
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (pageYOffset >= sectionTop - offset) {
                current = section.getAttribute('id');
            }
        });

        navLi.forEach(li => {
            li.classList.remove('active');
            if (li.getAttribute('href').includes(current)) {
                li.classList.add('active');
            }
        });
    }

    window.addEventListener('scroll', handleScroll);

    navLi.forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
            navLi.forEach(link => {
                link.classList.remove('active');
            });
            this.classList.add('active');
        });
    });


    // --- Modal Fungsionalitas ---
    const modal = document.getElementById('myModal');
    const closeBtn = document.querySelector('.close-btn');
    const cards = document.querySelectorAll('.card');
    const youtubeContainer = document.getElementById('youtubeContainer');

    // Fungsi untuk menampilkan modal dengan detail dan video
    function showModal(judul, kategori, deskripsi, lokasi, harga, jamBuka, youtubeID) {
        document.getElementById('modalJudul').textContent = judul;
        document.getElementById('modalKategori').textContent = kategori;
        document.getElementById('modalDeskripsiLengkap').textContent = deskripsi;
        document.getElementById('modalLokasi').textContent = lokasi;
        document.getElementById('modalHarga').textContent = harga;
        document.getElementById('modalJamBuka').textContent = jamBuka;

        // **LOGIKA UNTUK MENAMPILKAN TAUTAN YOUTUBE**
        if (youtubeID) {
            const youtubeLink = `https://www.youtube.com/watch?v=${youtubeID}`;
            
            youtubeContainer.innerHTML = `
                <a href="${youtubeLink}" target="_blank" class="btn" style="background-color: #ff0000; margin-top: 10px;">
                    ▶️ Tonton Video
                </a>
                <p style="margin-top: 10px; font-size: 0.9em; color: #777;">ID Video: ${youtubeID}</p>
            `;
            document.getElementById('modalYouTubeLink').style.display = 'block';

        } else {
            youtubeContainer.innerHTML = '<p style="color: #999;">Tautan video YouTube tidak tersedia untuk destinasi ini.</p>';
            document.getElementById('modalYouTubeLink').style.display = 'block';
        }

        modal.style.display = 'block';
    }

    // Event listener untuk setiap kartu
    cards.forEach(card => {
        card.addEventListener('click', function() {
            const judul = this.dataset.judul;
            const kategori = this.dataset.kategori;
            const deskripsi = this.dataset.deskripsi;
            const lokasi = this.dataset.lokasi;
            const harga = this.dataset.harga;
            const jamBuka = this.dataset.jamBuka;
            const youtubeID = this.dataset.youtube; 

            showModal(judul, kategori, deskripsi, lokasi, harga, jamBuka, youtubeID);
        });
    });

    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });
});