let cart = {};
let qtys = {};

function updateQty(id, delta){
    if(!qtys[id]) qtys[id]=1;
    qtys[id] += delta;
    if(qtys[id]<1) qtys[id]=1;
    document.getElementById('qty-'+id).innerText = qtys[id];
}

function addToCart(id, name, price, img){
    let qty = qtys[id] || 1;
    if(cart[id]) cart[id].qty += qty;
    else cart[id] = {name, price, qty: qty, img};
    renderCart();

    const cartIcon = document.querySelector('.cart-icon');
    cartIcon.classList.add('bounce');
    setTimeout(()=>cartIcon.classList.remove('bounce'),200);
}

function renderCart(){
    const cartItems = document.getElementById('cart-items');
    const cartCount = document.getElementById('cart-count');
    const cartTotalEl = document.getElementById('cart-total');
    const cartFooter = document.getElementById('cart-footer');
    const emptyCart = document.getElementById('empty-cart');

    cartItems.innerHTML='';
    let total=0;
    let count=0;
    for(let id in cart){
        let item = cart[id];
        total += item.price*item.qty;
        count += item.qty;
        cartItems.innerHTML += `
            <div class="cart-item">
                <img src="${item.img}" alt="${item.name}"/>
                <span>${item.name} x${item.qty}</span>
                <span>Rp ${item.price*item.qty}</span>
            </div>`;
    }
    cartCount.innerText = count;
    cartTotalEl.innerText = total;
    if(count>0){cartFooter.style.display='block';emptyCart.style.display='none';}
    else{cartFooter.style.display='none';emptyCart.style.display='block';}
}

function toggleCartDropdown(){
    const dropdown = document.getElementById('cart-dropdown');
    dropdown.style.display = (dropdown.style.display==='block')?'none':'block';
}

function checkout(){
    if(Object.keys(cart).length===0){ alert("Keranjang kosong!"); return; }
    let nama = prompt("Nama Pemesan:");
    let hp = prompt("Nomor HP:");
    let tanggal = prompt("Tanggal Kunjungan (YYYY-MM-DD):");
    if(!nama||!hp||!tanggal){ alert("Data harus lengkap!"); return; }

    // Simpan ke session melalui AJAX (simulasi)
    fetch('admin_add_menu.php',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({nama,nama_menu:Object.values(cart),hp,tanggal})
    });

    alert("Pesanan berhasil! Silakan cek dashboard admin untuk cetak nota.");
    cart={}; qtys={};
    renderCart();
    toggleCartDropdown();
}

function filterCategory(kategori){
    const cards = document.querySelectorAll('.card');
    cards.forEach(c=>{
        c.style.display = (kategori==='All'||c.dataset.kategori===kategori)?'block':'none';
    });
}

