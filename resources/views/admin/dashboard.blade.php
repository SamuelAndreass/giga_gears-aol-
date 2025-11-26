<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears — Admin Dashboard</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="css/admin.css">

  <style>
    /* 1. Fix Lebar & Layout Sidebar */
    .admin-side {
        width: 280px; 
        height: 100vh; /* Tinggi pas setinggi layar */
        background: #fff;
        border-right: 1px solid #E6ECFB;
        flex-shrink: 0;
        transition: transform 0.3s ease-in-out;
        
        /* --- PERBAIKAN UTAMA DISINI --- */
        display: flex;           /* Aktifkan Flexbox */
        flex-direction: column;  /* Susun vertikal */
        overflow-y: auto;        /* Izinkan SCROLL jika layar pendek */
        position: sticky;        /* Tetap di tempat untuk Desktop */
        top: 0;
    }

    /* Agar Menu Mengisi Ruang Kosong (Logout terdorong ke bawah) */
    .nav-admin {
        flex: 1; 
        margin-bottom: 20px;
    }

    /* 2. Logika Tampilan Mobile (HP) */
    @media (max-width: 991.98px) {
        .admin-side {
            position: fixed; /* Melayang di HP */
            top: 0; left: 0; bottom: 0;
            z-index: 1050; 
            transform: translateX(-100%); /* Sembunyi */
            box-shadow: none;
        }
        .admin-side.show {
            transform: translateX(0); /* Muncul */
            box-shadow: 4px 0 24px rgba(0,0,0,0.15);
        }
    }

    /* 3. Overlay Hitam */
    .sidebar-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); z-index: 1040;
        display: none; opacity: 0; transition: opacity 0.3s;
    }
    .sidebar-overlay.show { display: block; opacity: 1; }
  </style>
</head>
<body>
  
  <div class="container-fluid p-0 d-flex" style="min-height: 100vh; overflow-x: hidden;">
    
    <aside class="admin-side" id="adminSidebar">
        <a href="dashboard.html" class="brand-link" aria-label="GigaGears">
          <img src="../assets/gigagears-logo.png" alt="GigaGears" class="brand-logo">
        </a>

        <nav class="nav flex-column nav-admin">
          <a class="nav-link active" href="dashboard.html"><i class="bi bi-grid-1x2"></i>Dashboard</a>
          <a class="nav-link" href="customer.html"><i class="bi bi-people"></i>Data Customer</a>
          <a class="nav-link" href="seller.html"><i class="bi bi-person-badge"></i>Data Seller</a>
          <a class="nav-link" href="promotion.html"><i class="bi bi-ticket-perforated"></i>Promotion/Voucher</a>
          <a class="nav-link" href="transaction.html"><i class="bi bi-receipt"></i>Data Transaction</a>
          <a class="nav-link" href="analytics.html"><i class="bi bi-bar-chart"></i>Analytics & Report</a>
          <a class="nav-link" href="product.html"><i class="bi bi-box"></i>Products</a>
          <a class="nav-link" href="inbox.html"><i class="bi bi-inbox"></i>Inbox</a>
          <a class="nav-link" href="payment-verif.html"><i class="bi bi-shield-check"></i>Payment Verification</a>
          <a class="nav-link" href="shipping.html"><i class="bi bi-truck"></i>Shipping Settings</a>
        </nav>

        <div class="mt-auto pb-4 px-3">
          <button class="btn btn-logout w-100"><i class="bi bi-box-arrow-right me-1"></i> Log Out</button>
        </div>
    </aside>

    <main class="main-wrap flex-grow-1" style="min-width: 0;">

        <div class="appbar px-3 px-md-4 py-3 mb-4 d-flex align-items-center justify-content-between">
          
          <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light d-lg-none shadow-sm p-1 px-2" id="btnToggle" style="border-radius: 8px;">
                <i class="bi bi-list fs-3"></i>
            </button>
            <div>
                <div class="small opacity-75 mb-1">Welcome back 👋</div>
                <h1 class="wc-title mb-0">Admin</h1>
            </div>
          </div>

          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-light btn-sm"><i class="bi bi-bell"></i></button>
            <span class="badge-chip d-inline-flex align-items-center gap-2">
              <span class="rounded-circle bg-white text-primary fw-bold d-inline-flex align-items-center justify-content-center" style="width:28px;height:28px">A</span>
              Admin
            </span>
          </div>
        </div>

        <div class="gg-card p-3 p-md-4 mb-3">
          <div class="fw-semibold mb-2">Notifikasi & Activity Log</div>
          <div class="row g-2">
            <div class="col-12 col-md-6">
              <div class="alert alert-warning d-flex align-items-center gap-2 mb-0">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <b>5</b> new products pending verification
                <button class="btn btn-sm btn-outline-secondary ms-auto">Verif</button>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <div class="alert alert-danger d-flex align-items-center gap-2 mb-0">
                <i class="bi bi-exclamation-octagon-fill"></i>
                <b>2</b> withdraw requests awaiting approval
                <button class="btn btn-sm btn-outline-secondary ms-auto">Verif</button>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-12 col-md-6 col-xl-3">
            <div class="kpi">
              <span class="kpi-icon"><i class="bi bi-people"></i></span>
              <div><div class="small text-muted-gg">Total Customer</div><div class="fs-4 fw-black">12,450</div></div>
            </div>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <div class="kpi">
              <span class="kpi-icon"><i class="bi bi-person-badge"></i></span>
              <div><div class="small text-muted-gg">Total Sellers</div><div class="fs-4 fw-black">530</div></div>
            </div>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <div class="kpi">
              <span class="kpi-icon"><i class="bi bi-cash-coin"></i></span>
              <div><div class="small text-muted-gg">Total Transactions</div><div class="fs-4 fw-black">$98,500</div></div>
            </div>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <div class="kpi">
              <span class="kpi-icon"><i class="bi bi-hourglass-split"></i></span>
              <div><div class="small text-muted-gg">Pending Verifications</div><div class="fs-4 fw-black">34</div></div>
            </div>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-12 col-xl-8">
            <section class="gg-card p-3 p-md-4">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="fw-semibold">Customer Growth</div>
                <div class="btn-group btn-group-sm">
                  <button class="btn btn-outline-secondary active">This Year</button>
                  <button class="btn btn-outline-secondary">This Month</button>
                </div>
              </div>
              <canvas id="chartGrowth" height="120"></canvas>
            </section>
          </div>
          <div class="col-12 col-xl-4">
            <section class="gg-card p-3 p-md-4">
              <div class="fw-semibold mb-2">Revenue by Category</div>
              <canvas id="chartPie" height="180"></canvas>
              <ul class="mt-3 mb-0 small">
                <li><span style="display:inline-block;width:10px;height:10px;background:var(--pie-1);border-radius:3px;margin-right:8px"></span>Headset — 20%</li>
                <li><span style="display:inline-block;width:10px;height:10px;background:var(--pie-2);border-radius:3px;margin-right:8px"></span>Laptop — 25%</li>
                <li><span style="display:inline-block;width:10px;height:10px;background:var(--pie-3);border-radius:3px;margin-right:8px"></span>Accessories — 15%</li>
                <li><span style="display:inline-block;width:10px;height:10px;background:var(--pie-4);border-radius:3px;margin-right:8px"></span>Software — 20%</li>
                <li><span style="display:inline-block;width:10px;height:10px;background:var(--pie-5);border-radius:3px;margin-right:8px"></span>Mobile — 20%</li>
              </ul>
            </section>
          </div>
        </div>

        <p class="text-center mt-4 foot small mb-0">© 2025 GigaGears. Admin Panel.</p>
    </main>
  </div>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  
  <script>
    const btnToggle = document.getElementById('btnToggle');
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function toggleSidebar() {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    }

    if(btnToggle) btnToggle.addEventListener('click', toggleSidebar);
    if(overlay) overlay.addEventListener('click', toggleSidebar);
  </script>

  <script>
    const cssVar = (n)=>getComputedStyle(document.documentElement).getPropertyValue(n).trim();

    // Bar
    const g = document.getElementById('chartGrowth');
    if (g){
      const base = cssVar('--gg-primary') || '#1357FF';
      new Chart(g, {
        type:'bar',
        data:{ labels:['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
          datasets:[{ label:'Total Customer', data:[220,480,760,430,580,320,720,980,660,740,820,900],
            backgroundColor: base+'33', borderColor: base, borderWidth: 1.5 }]},
        options:{ plugins:{legend:{display:false}}, scales:{x:{grid:{display:false}}, y:{beginAtZero:true,ticks:{stepSize:200}}} }
      });
    }

    // Pie
    const p = document.getElementById('chartPie');
    if (p){
      const colors = [cssVar('--pie-1'),cssVar('--pie-2'),cssVar('--pie-3'),cssVar('--pie-4'),cssVar('--pie-5')];
      new Chart(p, {
        type:'pie',
        data:{ labels:['Headset','Laptop','Accessories','Software','Mobile'],
          datasets:[{ data:[20,25,15,20,20], backgroundColor: colors, borderColor:'#fff', borderWidth:2 }]},
        options:{ plugins:{legend:{display:false}} }
      });
    }
  </script>
</body>
</html>