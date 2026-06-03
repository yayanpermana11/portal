<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Link MIS & SMP Plus Al-Jamila</title>
    <link rel="icon" type="image/png" href="/img/logo.png">
    <style>
        :root {
            --bg: #f0f4f8;
            --card-bg: #ffffff;
            --text: #1a202c;
            --text-secondary: #4a5568;
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --border: #e2e8f0;
            --shadow: 0 1px 3px rgba(0,0,0,0.06);
            --shadow-hover: 0 10px 25px rgba(0,0,0,0.1);
            --tag-bg: #eef2ff;
            --tag-text: #4f46e5;
        }
        .dark-mode {
            --bg: #0f172a;
            --card-bg: #1e293b;
            --text: #e2e8f0;
            --text-secondary: #94a3b8;
            --border: #334155;
            --shadow: 0 1px 3px rgba(0,0,0,0.3);
            --shadow-hover: 0 10px 25px rgba(0,0,0,0.4);
            --tag-bg: #312e81;
            --tag-text: #a5b4fc;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI', system-ui, sans-serif; background:var(--bg); color:var(--text); min-height:100vh; transition:0.3s; line-height:1.6; }
        .header { background:var(--card-bg); border-bottom:1px solid var(--border); padding:16px 24px; position:sticky; top:0; z-index:100; box-shadow:var(--shadow); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; }
        .logo { font-weight:700; font-size:1.25rem; display:flex; align-items:center; gap:8px; }
        .logo-icon { width:40px; height:40px; background:linear-gradient(135deg, #6366f1, #8b5cf6); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; color:#fff; }
        .search-box { position:relative; flex:1; min-width:220px; max-width:400px; }
        .search-box input { width:100%; padding:10px 16px 10px 40px; border:2px solid var(--border); border-radius:50px; background:var(--bg); color:var(--text); outline:none; }
        .search-box span { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-secondary); }
        .header-actions { display:flex; gap:8px; align-items:center; }
        .btn { padding:8px 16px; border-radius:50px; border:none; cursor:pointer; font-weight:600; font-size:0.9rem; text-decoration:none; display:inline-flex; align-items:center; gap:4px; }
        .btn-primary { background:var(--primary); color:#fff; }
        .btn-outline { background:transparent; border:2px solid var(--border); color:var(--text); }
        .btn-outline:hover { border-color:var(--primary); }
        .container { max-width:1300px; margin:0 auto; padding:20px; }
        .stats { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:20px; }
        .stat-card { background:var(--card-bg); border-radius:14px; padding:14px 18px; box-shadow:var(--shadow); border:1px solid var(--border); flex:1; min-width:140px; display:flex; align-items:center; gap:12px; }
        .stat-icon { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; }
        .stat-icon.blue{ background:#eef2ff; color:#4f46e5; } .stat-icon.green{ background:#ecfdf5; color:#10b981; } .stat-icon.orange{ background:#fff7ed; color:#f97316; } .stat-icon.purple{ background:#faf5ff; color:#8b5cf6; }
        .category-tabs { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px; }
        .cat-tab { padding:8px 14px; border-radius:50px; border:2px solid var(--border); background:var(--card-bg); color:var(--text-secondary); cursor:pointer; font-weight:600; font-size:0.85rem; }
        .cat-tab.active { background:var(--primary); color:#fff; border-color:var(--primary); }
        .links-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(280px,1fr)); gap:14px; }
        .link-card { background:var(--card-bg); border-radius:14px; padding:16px 18px; box-shadow:var(--shadow); border:1px solid var(--border); display:flex; gap:12px; cursor:pointer; transition:0.2s; animation:fadeUp 0.4s ease; }
        @keyframes fadeUp { from{opacity:0;transform:translateY(20px);} to{opacity:1;transform:translateY(0);} }
        .link-card:hover { transform:translateY(-3px); box-shadow:var(--shadow-hover); }
        .link-icon { width:46px; height:46px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; flex-shrink:0; }
        .link-icon.color-1{ background:#eef2ff; color:#4f46e5; } .link-icon.color-2{ background:#ecfdf5; color:#10b981; } .link-icon.color-3{ background:#fff7ed; color:#f97316; } .link-icon.color-4{ background:#faf5ff; color:#8b5cf6; } .link-icon.color-5{ background:#fef2f2; color:#ef4444; } .link-icon.color-6{ background:#f0fdfa; color:#14b8a6; }
        /* Tambahan untuk gambar di dalam link-icon */
        .link-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 12px;
        }
        .link-info h4 { margin-bottom:2px; }
        .link-url { font-size:0.8rem; color:var(--text-secondary); word-break:break-all; overflow:hidden; display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; }
        .link-category { font-size:0.7rem; background:var(--tag-bg); color:var(--tag-text); padding:2px 10px; border-radius:20px; display:inline-block; margin-top:6px; }
        .empty { grid-column:1/-1; text-align:center; padding:40px; color:var(--text-secondary); }
        .admin-badge { background:var(--primary); color:#fff; padding:4px 12px; border-radius:20px; font-size:0.8rem; display:inline-flex; align-items:center; gap:4px; }
        @media (max-width:768px) { .links-grid { grid-template-columns:1fr; } }
        
        .logo-img {
            height: 40px;      /* tinggi konsisten */
            width: auto;       /* lebar otomatis mengikuti proporsi */
            max-width: 200px;  /* batas maksimal agar tidak terlalu lebar */
            object-fit: contain;
            border-radius: 8px;
        }
    </style>
</head>
<body class="<?= isset($_COOKIE['darkMode']) && $_COOKIE['darkMode'] === '1' ? 'dark-mode' : '' ?>">
    <header class="header">
        <div class="logo">
            <?php
            $logoFile = __DIR__ . '/img/logo.png';
            if (file_exists($logoFile)) {
                echo '<img src="/img/logo.png" alt="Logo" class="logo-img">';
            } else {
                echo '<span class="logo-icon">📚</span>';
            }
            ?>
            Portal Link MIS & SMP Plus Al-Jamila
        </div>
        <!--<div class="search-box"><span>🔍</span><input type="text" id="searchInput" placeholder="Cari link..."></div>-->
        <div class="header-actions">
            <button class="btn btn-outline" id="darkToggle" title="Mode Gelap">🌓</button>
            <button class="btn btn-outline" id="exportBtn">📥 Ekspor</button>
            <?php if (!empty($_SESSION['logged_in'])): ?>
                <span class="admin-badge">👑 Admin</span>
                <a href="admin.php" class="btn btn-primary">⚙️ Kelola</a>
                <a href="logout.php" class="btn btn-outline">🚪 Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary">🔑 Login Admin</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="container">
        <!--<div class="stats" id="statsRow">-->
        <!--    <div class="stat-card"><div class="stat-icon blue">🔗</div><div><div class="stat-value" id="statTotal">0</div><small>Total Link</small></div></div>-->
        <!--    <div class="stat-card"><div class="stat-icon green">📂</div><div><div class="stat-value" id="statCat">0</div><small>Kategori</small></div></div>-->
        <!--    <div class="stat-card"><div class="stat-icon orange">⭐</div><div><div class="stat-value" id="statPopuler">-</div><small>Terpopuler</small></div></div>-->
        <!--    <div class="stat-card"><div class="stat-icon purple">🕐</div><div><div class="stat-value" id="statBaru">-</div><small>Terbaru</small></div></div>-->
        <!--</div>-->
        <div class="category-tabs" id="categoryTabs"></div>
        <div class="search-box"><span>🔍</span><input type="text" id="searchInput" placeholder="Cari link..."></div></br>
        <div class="links-grid" id="linksGrid"></div>
        
    </div>

    <script>
        let allLinks = [];
        let currentCat = 'semua';

        async function fetchLinks() {
            const res = await fetch('api.php');
            allLinks = await res.json();
            // Urutkan berdasarkan abjad (nama link) – A ke Z
            allLinks.sort((a, b) => a.name.localeCompare(b.name, 'id', { sensitivity: 'base' }));
            renderUI();
        }

        function renderUI() {
            renderCategories();
            renderLinks();
            updateStats();
        }

function renderCategories() {
    // Kumpulkan semua kategori unik
    const allCats = new Set();
    allLinks.forEach(link => {
        (link.categories || []).forEach(cat => allCats.add(cat));
    });
    const cats = ['semua', ...Array.from(allCats).sort()];
    
    const container = document.getElementById('categoryTabs');
    container.innerHTML = cats.map(cat => {
        const count = cat === 'semua' ? allLinks.length : allLinks.filter(l => (l.categories || []).includes(cat)).length;
        return `<button class="cat-tab ${cat === currentCat ? 'active' : ''}" data-cat="${cat}">${cat === 'semua' ? '📋 Semua' : cat} (${count})</button>`;
    }).join('');
    
    document.querySelectorAll('.cat-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            currentCat = btn.dataset.cat;
            renderUI();
        });
    });
}

        // Fungsi untuk mendeteksi apakah ikon adalah gambar
        function renderIcon(link) {
            const icon = link.icon || '🔗';
            // Deteksi jika icon adalah path gambar (relatif atau absolut) atau URL
            if (/\.(png|jpg|jpeg|svg|gif|webp)(\?.*)?$/i.test(icon) || icon.startsWith('/img/') || /^https?:\/\//i.test(icon)) {
                return `<img src="${escapeHTML(icon)}" alt="" style="width:100%; height:100%; object-fit:contain; border-radius:10px;" onerror="this.parentElement.innerHTML='🔗'">`;
            } else {
                // Selain itu anggap sebagai emoji/teks
                return escapeHTML(icon);
            }
        }

            function renderLinks() {
                const search = document.getElementById('searchInput').value.toLowerCase();
                let filtered = allLinks.filter(l => {
                    const matchCat = currentCat === 'semua' || (l.categories || []).includes(currentCat);
                    const matchSearch = !search || l.name.toLowerCase().includes(search) || l.url.toLowerCase().includes(search) || (l.categories || []).some(cat => cat.toLowerCase().includes(search));
                    return matchCat && matchSearch;
                });
            const grid = document.getElementById('linksGrid');
            if (filtered.length === 0) {
                grid.innerHTML = '<div class="empty">📭 Tidak ada link ditemukan</div>';
                return;
            }
            grid.innerHTML = filtered.map((l, i) => `
                <div class="link-card" style="animation-delay:${i*0.03}s" onclick="window.open('${escapeHTML(l.url)}', '_blank')">
                    <div class="link-icon ${l.color || 'color-1'}">${renderIcon(l)}</div>
                    <div class="link-info">
                        <h4>${escapeHTML(l.name)}</h4>
                        <div class="link-url">${escapeHTML(l.url)}</div>
                        <span class="link-category">${(l.categories || []).map(c => escapeHTML(c)).join(' • ')}</span>
                    </div>
                </div>
            `).join('');
        }

        function updateStats() {
            document.getElementById('statTotal').textContent = allLinks.length;
            const cats = new Set(allLinks.map(l => l.category));
            document.getElementById('statCat').textContent = cats.size;
            const sortedByClicks = [...allLinks].sort((a,b) => (b.clicks||0) - (a.clicks||0));
            document.getElementById('statPopuler').textContent = sortedByClicks[0]?.clicks > 0 ? truncate(sortedByClicks[0].name, 18) : '-';
            const sortedByDate = [...allLinks].sort((a,b) => b.createdAt - a.createdAt);
            document.getElementById('statBaru').textContent = sortedByDate[0] ? truncate(sortedByDate[0].name, 18) : '-';
        }

        function escapeHTML(str) { const d = document.createElement('div'); d.textContent = str; return d.innerHTML; }
        function truncate(str, max) { return str.length > max ? str.substring(0,max)+'…' : str; }

        // Search
        document.getElementById('searchInput').addEventListener('input', () => {
            currentCat = 'semua';
            renderUI();
        });

        // Dark mode toggle
        const darkToggle = document.getElementById('darkToggle');
        darkToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            document.cookie = 'darkMode=' + (isDark ? '1' : '0') + ';path=/;max-age=31536000';
        });

        // Ekspor
        document.getElementById('exportBtn').addEventListener('click', () => {
            const blob = new Blob([JSON.stringify(allLinks, null, 2)], {type:'application/json'});
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'portal-link-sekolah.json';
            a.click();
        });

        fetchLinks();
    </script>
</body>
</html>