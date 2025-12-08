// ===== Modal Tambah/Edit Menu =====
const modal = document.getElementById('modal');
function showAddModal(){
    if(!modal) return;
    document.getElementById('modal-title').innerText='Tambah Menu';
    document.getElementById('formMenu').reset();
    document.getElementById('menuId').value='';
    modal.style.display='flex';
}
function openEditModal(id){
    if(!modal) return;
    const item = window.menuData.find(m=>m.id==id);
    if(!item) return;
    document.getElementById('modal-title').innerText='Edit Menu';
    document.getElementById('menuId').value=item.id;
    document.getElementById('menuNama').value=item.nama;
    document.getElementById('menuKategori').value=item.kategori;
    document.getElementById('menuHarga').value=item.harga;
    document.getElementById('menuGambar').value=item.gambar;
    modal.style.display='flex';
}
function closeModal(){if(modal) modal.style.display='none';}

document.addEventListener("DOMContentLoaded", ()=>{
    const form = document.getElementById('formMenu');
    if(form){
        form.addEventListener('submit',function(e){
            e.preventDefault();
            const id = document.getElementById('menuId').value;
            const nama = document.getElementById('menuNama').value;
            const kategori = document.getElementById('menuKategori').value;
            const harga = parseInt(document.getElementById('menuHarga').value);
            const gambar = document.getElementById('menuGambar').value;

            if(id==''){
                const newId = Math.max(...window.menuData.map(m=>m.id))+1;
                window.menuData.push({id:newId,nama,kategori,harga,gambar});
            } else{
                const index = window.menuData.findIndex(m=>m.id==id);
                window.menuData[index]={id:parseInt(id),nama,kategori,harga,gambar};
            }
            closeModal();
            location.reload();
        });
    }
});

function deleteMenu(id){
    if(confirm("Yakin hapus menu ini?")){
        window.menuData = window.menuData.filter(m=>m.id!=id);
        location.reload();
    }
}

// ===== Pesanan & Nota =====
function cetakNota(i){
    const orders = window.ordersData || [];
    const o = orders[i];
    if(!o) return;
    let w = window.open();
    w.document.write(`<h2>Nota Pesanan</h2>
        <p>Nama: ${o.nama}</p>
        <p>HP: ${o.hp}</p>
        <p>Tanggal: ${o.tanggal}</p>
        <p>Menu: ${o.nama_menu.map(m=>m.name+' x'+m.qty).join(', ')}</p>
        <p>Total: Rp ${o.nama_menu.reduce((a,b)=>a+b.price*b.qty,0)}</p>
        <p>Terima kasih!</p>`);
    w.print();
}
