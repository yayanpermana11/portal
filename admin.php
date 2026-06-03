<?php
session_start();
if (empty($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Link - Admin</title>
    <link rel="icon" type="image/png" href="/img/logo.png">
    <style>
        :root {
            --bg: #f0f4f8; --card-bg: #fff; --text: #1a202c;
            --primary: #4f46e5; --primary-hover: #4338ca;
            --border: #e2e8f0; --danger: #ef4444;
            --radius: 12px;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: var(--bg); color: var(--text); padding: 20px; }
        .container { max-width: 1200px; margin: auto; }
        .header {
            background: var(--card-bg); padding: 16px 20px; border-radius: var(--radius);
            box-shadow: 0 1px 3px rgba(0,0,0,0.06); display: flex; justify-content: space-between;
            align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;
        }
        .btn {
            padding: 8px 16px; border-radius: 50px; border: none; cursor: pointer;
            font-weight: 600; font-size: 0.9rem; text-decoration: none;
            display: inline-flex; align-items: center; gap: 6px; transition: 0.2s;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-outline { background: transparent; border: 2px solid var(--border); color: var(--text); }
        .btn-outline:hover { border-color: var(--primary); }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-sm { padding: 6px 12px; font-size: 0.8rem; }
        .btn-warning { background: #f59e0b; color: #fff; }
        .btn-warning:hover { background: #d97706; }
        table {
            width: 100%; background: var(--card-bg); border-radius: var(--radius);
            box-shadow: 0 1px 3px rgba(0,0,0,0.06); border-collapse: collapse; overflow: hidden;
        }
        th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border); }
        th { background: var(--primary); color: #fff; }
        tr:hover { background: #f8fafc; }
        /* Overlay & Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }
        .modal {
            background: var(--card-bg);
            border-radius: var(--radius);
            padding: 28px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            position: relative;
            z-index: 201;
        }
        .close-btn {
            position: absolute; top: 14px; right: 14px; width: 34px; height: 34px;
            border-radius: 50%; border: none; background: var(--bg); cursor: pointer;
            font-size: 1.1rem; display: flex; align-items: center; justify-content: center;
        }
        .close-btn:hover { background: var(--danger); color: #fff; }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 5px; font-size: 0.9rem; }
        .form-group input, .form-group select {
            width: 100%; padding: 10px 14px; border: 2px solid var(--border);
            border-radius: 8px; font-size: 0.95rem; background: var(--bg); color: var(--text); outline: none;
        }
        .form-group input:focus, .form-group select:focus { border-color: var(--primary); }
        .color-options { display: flex; gap: 8px; }
        .color-opt {
            width: 32px; height: 32px; border-radius: 50%; cursor: pointer;
            border: 3px solid transparent; transition: 0.2s;
        }
        .color-opt.selected { border-color: var(--primary); transform: scale(1.1); }
        .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
        #iconPreview {
            width: 50px; height: 50px; margin-top: 8px; border-radius: 10px;
            overflow: hidden; display: none; border: 2px dashed var(--border);
            align-items: center; justify-content: center;
        }
        #iconPreview img { width: 100%; height: 100%; object-fit: contain; }
        .upload-row { display: flex; align-items: center; gap: 10px; margin-top: 6px; }
        .upload-btn {
            background: #0ea5e9; color: #fff; padding: 8px 16px; border-radius: 8px;
            border: none; cursor: pointer; font-weight: 600; font-size: 0.9rem;
            transition: 0.2s;
        }
        .upload-btn:hover { background: #0284c7; }
        #uploadStatus { font-size: 0.85rem; color: #334155; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div style="font-weight:700; font-size:1.2rem;">⚙️ Manajemen Link</div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="index.php" class="btn btn-outline btn-sm">🏠 Portal</a>
            <button class="btn btn-outline btn-sm" id="importBtn">📤 Impor</button>
            <input type="file" id="importFile" accept=".json" hidden>
            <button class="btn btn-outline btn-sm" id="exportBtn">📥 Ekspor</button>
            <button class="btn btn-outline btn-sm" id="changeLogoBtn">🖼️ Ubah Logo</button>
            <button class="btn btn-primary" id="addLinkBtn">➕ Tambah Link</button>
            <a href="logout.php" class="btn btn-danger btn-sm">🚪 Logout</a>
        </div>
    </div>

    <table>
        <thead><tr><th>Ikon</th><th>Nama</th><th>URL</th><th>Kategori</th><th>Aksi</th></tr></thead>
        <tbody id="linkTableBody"><tr><td colspan="5" style="text-align:center;">Memuat data...</td></tr></tbody>
    </table>
</div>

<!-- MODAL TAMBAH/EDIT LINK -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal" id="linkModal">
        <button class="close-btn" id="modalClose">✕</button>
        <h3 id="modalTitle">➕ Tambah Link Baru</h3>
        <form id="linkForm" onsubmit="return false;">
            <input type="hidden" id="linkId">
            <div class="form-group">
                <label>🏷️ Nama Link *</label>
                <input type="text" id="linkName" required placeholder="Contoh: Google Classroom">
            </div>
            <div class="form-group">
                <label>🔗 URL *</label>
                <input type="text" id="linkUrl" required placeholder="https://...">
            </div>
            <div class="form-group">
                <label>📂 Kategori (tahan Ctrl untuk pilih lebih dari satu)</label>
                <select id="linkCategory" multiple size="5" style="height:auto; min-height:100px;"></select>
                <small style="color: #64748b;">Gunakan <strong>Ctrl+klik</strong> (Windows) atau <strong>Command+klik</strong> (Mac) untuk memilih beberapa.</small>
            </div>
            <div class="form-group">
                <label>🎨 Ikon (Emoji atau URL gambar)</label>
                <input type="text" id="linkIcon" placeholder="📘 atau /img/logo.png">
                <div id="iconPreview"><img id="iconPreviewImg" src="" alt=""></div>
            </div>
            <div class="form-group" style="background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid var(--border);">
                <label style="font-size:0.9rem; margin-bottom:8px;">📁 Unggah Gambar Baru (PNG, JPG, SVG, maks 2MB)</label>
                <div class="upload-row">
                    <input type="file" id="iconFile" accept="image/*" hidden>
                    <button type="button" class="upload-btn" id="uploadIconBtn">📁 Pilih Gambar</button>
                    <span id="uploadStatus"></span>
                </div>
            </div>
            <div class="form-group">
                <label>🌈 Warna Ikon</label>
                <div class="color-options" id="colorOptions">
                    <span class="color-opt selected" data-color="color-1" style="background:#eef2ff;"></span>
                    <span class="color-opt" data-color="color-2" style="background:#ecfdf5;"></span>
                    <span class="color-opt" data-color="color-3" style="background:#fff7ed;"></span>
                    <span class="color-opt" data-color="color-4" style="background:#faf5ff;"></span>
                    <span class="color-opt" data-color="color-5" style="background:#fef2f2;"></span>
                    <span class="color-opt" data-color="color-6" style="background:#f0fdfa;"></span>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" id="modalCancel">Batal</button>
                <button type="button" class="btn btn-primary" id="saveLinkBtn">💾 Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Upload Logo -->
<div class="modal-overlay" id="logoModalOverlay" style="display:none;">
    <div class="modal" style="max-width:400px;">
        <button class="close-btn" id="logoModalClose">✕</button>
        <h3>🖼️ Ubah Logo Portal</h3>
        <p style="font-size:0.9rem; color:var(--text-secondary); margin-bottom:12px;">
            Unggah gambar untuk mengganti logo di pojok kiri atas portal.<br>
            <strong>Format:</strong> PNG, JPG, SVG (maks 2MB)<br>
            <strong>Nama file:</strong> akan disimpan sebagai <code>logo.png</code>
        </p>
        <input type="file" id="logoFile" accept="image/*">
        <div id="logoUploadStatus" style="margin-top:8px; font-size:0.9rem;"></div>
        <div class="modal-actions" style="margin-top:16px;">
            <button class="btn btn-outline" id="logoModalCancel">Batal</button>
            <button class="btn btn-primary" id="uploadLogoBtn">📤 Unggah</button>
        </div>
    </div>
</div>

<script>
    let links = [];
    let editingId = null;
    let selectedColor = 'color-1';
    const modalOverlay = document.getElementById('modalOverlay');
    const linkModal = document.getElementById('linkModal');

    // ========== LOAD DATA ==========
    async function loadLinks() {
        try {
            const res = await fetch('api.php');
            if (!res.ok) throw new Error('Gagal memuat data');
            links = await res.json();
            renderTable();
            populateCategorySelect(); // <-- tambahkan ini
        } catch (err) { ... }
    }

    // ========== RENDER ==========
    function renderIconPreview(icon) {
        if (!icon) return '🔗';
        if (/\.(png|jpg|jpeg|svg|gif|webp)(\?.*)?$/i.test(icon) || /^https?:\/\//i.test(icon)) {
            return `<img src="${escapeHTML(icon)}" onerror="this.innerHTML='🔗'" style="width:24px;height:24px;vertical-align:middle;border-radius:4px;">`;
        }
        return escapeHTML(icon);
    }

    function renderTable() {
        const tbody = document.getElementById('linkTableBody');
        if (!links.length) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Belum ada link. Klik "Tambah Link".</td></tr>';
            return;
        }
        tbody.innerHTML = links.map(l => `
            <tr>
                <td>${renderIconPreview(l.icon)}</td>
                <td>${escapeHTML(l.name)}</td>
                <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis;">${escapeHTML(l.url)}</td>
                <td>${(l.categories || []).map(c => escapeHTML(c)).join(', ')}</td>
                <td>
                    <button class="btn btn-outline btn-sm" onclick="editLink('${l.id}')">✏️</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteLink('${l.id}')">🗑️</button>
                </td>
            </tr>
        `).join('');
    }

    function updateCategorySuggestions() {
        const datalist = document.getElementById('categorySuggestions');
        const allCats = new Set();
        links.forEach(link => {
            (link.categories || []).forEach(cat => allCats.add(cat));
        });
        datalist.innerHTML = Array.from(allCats).sort().map(cat => `<option value="${escapeHTML(cat)}">`).join('');
    }
    function populateCategorySelect() {
        const select = document.getElementById('linkCategory');
        const allCats = new Set();
        links.forEach(link => {
            (link.categories || []).forEach(cat => allCats.add(cat));
        });
        // Simpan nilai yang sedang dipilih (jika ada) untuk dipulihkan nanti
        const currentSelected = Array.from(select.selectedOptions).map(opt => opt.value);
        select.innerHTML = ''; // kosongkan
        Array.from(allCats).sort().forEach(cat => {
            const option = document.createElement('option');
            option.value = cat;
            option.textContent = cat;
            if (currentSelected.includes(cat)) option.selected = true;
            select.appendChild(option);
        });
    }
    function escapeHTML(str) { const d = document.createElement('div'); d.textContent = str; return d.innerHTML; }

    // ========== MODAL ==========
    function openModal() { modalOverlay.style.display = 'flex'; }
    function closeModal() { modalOverlay.style.display = 'none'; editingId = null; clearPreview(); }
    function clearPreview() { 
        document.getElementById('iconPreview').style.display = 'none'; 
        document.getElementById('uploadStatus').textContent = ''; 
    }

    function openAddModal() {
        editingId = null;
        document.getElementById('modalTitle').textContent = '➕ Tambah Link Baru';
        document.getElementById('linkId').value = '';
        document.getElementById('linkName').value = '';
        document.getElementById('linkUrl').value = '';
        // Reset pilihan dropdown
        const select = document.getElementById('linkCategory');
        Array.from(select.options).forEach(opt => opt.selected = false);
        document.getElementById('linkIcon').value = '';
        selectedColor = 'color-1';
        updateColorSelection();
        clearPreview();
        openModal();
        document.getElementById('linkName').focus();
    }

    function editLink(id) {
        const link = links.find(l => l.id === id);
        if (!link) return;
        editingId = id;
        document.getElementById('modalTitle').textContent = '✏️ Edit Link';
        document.getElementById('linkId').value = link.id;
        document.getElementById('linkName').value = link.name;
        document.getElementById('linkUrl').value = link.url;
        
        // Tandai opsi yang sesuai
        const select = document.getElementById('linkCategory');
        const linkCats = link.categories || [];
        Array.from(select.options).forEach(opt => {
            opt.selected = linkCats.includes(opt.value);
        });
        
        document.getElementById('linkIcon').value = link.icon || '';
        selectedColor = link.color || 'color-1';
        updateColorSelection();
        // Preview gambar
        const icon = link.icon || '';
        if (/\.(png|jpg|jpeg|svg|gif|webp)(\?.*)?$/i.test(icon) || /^https?:\/\//i.test(icon)) {
            document.getElementById('iconPreviewImg').src = icon;
            document.getElementById('iconPreview').style.display = 'flex';
        } else {
            clearPreview();
        }
        openModal();
    }

    function updateColorSelection() {
        document.querySelectorAll('.color-opt').forEach(opt => 
            opt.classList.toggle('selected', opt.dataset.color === selectedColor)
        );
    }

    // ========== CRUD ==========
    async function saveLink() {
        const name = document.getElementById('linkName').value.trim();
        const url = document.getElementById('linkUrl').value.trim();
        if (!name || !url) {
            alert('Nama dan URL wajib diisi!');
            return;
        }
        const catSelect = document.getElementById('linkCategory');
        const categories = Array.from(catSelect.selectedOptions).map(opt => opt.value);
        if (categories.length === 0) categories.push('Lainnya');
        
        const data = {
            id: editingId,
            name, url,
            categories: categories,
            icon: document.getElementById('linkIcon').value.trim() || '🔗',
            color: selectedColor
        };
        const method = editingId ? 'PUT' : 'POST';
        try {
            const res = await fetch('api.php', {
                method, headers: {'Content-Type':'application/json'}, body: JSON.stringify(data)
            });
            if (res.ok) { 
                closeModal(); 
                loadLinks(); 
            } else {
                const err = await res.json();
                alert('Gagal: ' + (err.error || 'Unknown error'));
            }
        } catch (err) {
            alert('Gagal terhubung ke server.');
        }
    }

    async function deleteLink(id) {
        if (!confirm('Yakin hapus?')) return;
        try {
            await fetch('api.php', { 
                method:'DELETE', 
                headers:{'Content-Type':'application/json'}, 
                body: JSON.stringify({id}) 
            });
            loadLinks();
        } catch (err) {
            alert('Gagal menghapus.');
        }
    }

    // ========== UPLOAD GAMBAR ==========
    document.getElementById('uploadIconBtn').addEventListener('click', () => document.getElementById('iconFile').click());
    document.getElementById('iconFile').addEventListener('change', async function() {
        const file = this.files[0];
        if (!file) return;
        const formData = new FormData();
        formData.append('icon', file);
        document.getElementById('uploadStatus').textContent = 'Mengunggah...';
        try {
            const res = await fetch('upload.php', { method:'POST', body: formData });
            const data = await res.json();
            if (data.url) {
                document.getElementById('linkIcon').value = data.url;
                document.getElementById('iconPreviewImg').src = data.url;
                document.getElementById('iconPreview').style.display = 'flex';
                document.getElementById('uploadStatus').textContent = '✅ Berhasil';
            } else {
                document.getElementById('uploadStatus').textContent = '❌ ' + (data.error || 'Gagal');
            }
        } catch { 
            document.getElementById('uploadStatus').textContent = '❌ Gagal terhubung'; 
        }
        this.value = '';
    });

    // Preview ikon saat mengetik URL manual
    document.getElementById('linkIcon').addEventListener('input', function() {
        const val = this.value.trim();
        const preview = document.getElementById('iconPreview');
        if (/\.(png|jpg|jpeg|svg|gif|webp)(\?.*)?$/i.test(val) || /^https?:\/\//i.test(val)) {
            document.getElementById('iconPreviewImg').src = val;
            preview.style.display = 'flex';
        } else {
            preview.style.display = 'none';
        }
    });

    // Color selection
    document.getElementById('colorOptions').addEventListener('click', e => {
        const opt = e.target.closest('.color-opt');
        if (opt) { selectedColor = opt.dataset.color; updateColorSelection(); }
    });

    // Modal close events
    document.getElementById('modalClose').addEventListener('click', closeModal);
    document.getElementById('modalCancel').addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', e => { if (e.target === modalOverlay) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && modalOverlay.style.display === 'flex') closeModal(); });
    document.getElementById('saveLinkBtn').addEventListener('click', saveLink);
    document.getElementById('addLinkBtn').addEventListener('click', openAddModal);

    // ========== EKSPOR & IMPOR ==========
    document.getElementById('exportBtn').addEventListener('click', () => {
        const blob = new Blob([JSON.stringify(links, null, 2)], {type:'application/json'});
        const a = document.createElement('a'); 
        a.href = URL.createObjectURL(blob); 
        a.download = 'links.json'; 
        a.click();
    });
    document.getElementById('importBtn').addEventListener('click', () => document.getElementById('importFile').click());
    document.getElementById('importFile').addEventListener('change', async function() {
        const file = this.files[0];
        if (!file) return;
        const text = await file.text();
        try {
            const imported = JSON.parse(text);
            if (!Array.isArray(imported)) throw new Error('Format bukan array');
            for (const item of imported) {
                await fetch('api.php', { 
                    method:'POST', 
                    headers:{'Content-Type':'application/json'}, 
                    body: JSON.stringify(item) 
                });
            }
            loadLinks();
        } catch(e) { 
            alert('Gagal impor: ' + e.message); 
        }
        this.value = '';
    });

    // ========== UPLOAD LOGO ==========
    const logoModalOverlay = document.getElementById('logoModalOverlay');
    const logoFileInput = document.getElementById('logoFile');
    const logoUploadStatus = document.getElementById('logoUploadStatus');
    const changeLogoBtn = document.getElementById('changeLogoBtn');
    const logoModalClose = document.getElementById('logoModalClose');
    const logoModalCancel = document.getElementById('logoModalCancel');
    const uploadLogoBtn = document.getElementById('uploadLogoBtn');
    
    changeLogoBtn.addEventListener('click', () => {
        logoModalOverlay.style.display = 'flex';
        logoFileInput.value = '';
        logoUploadStatus.textContent = '';
    });
    
    function closeLogoModal() { logoModalOverlay.style.display = 'none'; }
    
    logoModalClose.addEventListener('click', closeLogoModal);
    logoModalCancel.addEventListener('click', closeLogoModal);
    logoModalOverlay.addEventListener('click', e => { if (e.target === logoModalOverlay) closeLogoModal(); });
    
    uploadLogoBtn.addEventListener('click', async () => {
        const file = logoFileInput.files[0];
        if (!file) {
            logoUploadStatus.textContent = '❌ Pilih file terlebih dahulu.';
            return;
        }
        const formData = new FormData();
        formData.append('logo', file);
        logoUploadStatus.textContent = 'Mengunggah...';
        try {
            const res = await fetch('upload_logo.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.url) {
                logoUploadStatus.textContent = '✅ Logo berhasil diubah! Refresh portal untuk melihat.';
                setTimeout(closeLogoModal, 1500);
            } else {
                logoUploadStatus.textContent = '❌ ' + (data.error || 'Gagal');
            }
        } catch (err) {
            logoUploadStatus.textContent = '❌ Gagal terhubung ke server.';
        }
    });

    // ========== START ==========
    loadLinks();
</script>
</body>
</html>