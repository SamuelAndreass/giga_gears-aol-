<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears — Analytics & Report</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <style>
    :root{ --gg-primary:#5b8cff; --gg-muted:#6b7280; --gg-radius:14px; }
    body{
      font-family:"Inter",system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      background:
        radial-gradient(1100px 500px at 85% -10%, rgba(91,140,255,.25), transparent 60%),
        linear-gradient(180deg,#f3f7ff 0%, #ffffff 46%, #f7fbff 100%);
    }
    .fw-black{font-weight:900!important}
    .text-muted-gg{color:var(--gg-muted)!important}

    /* Sidebar & nav */
    .side{background:#fff;border-right:1px solid rgba(91,140,255,.1);min-height:100vh;position:sticky;top:0}
    .nav-gg .nav-link{border-radius:12px;color:#10204d;font-weight:600;padding:.65rem .85rem}
    .nav-gg .nav-link .bi{width:1.1rem;margin-right:.5rem}
    .nav-gg .nav-link:hover{background:#f3f6ff}
    .nav-gg .nav-link.active{background:#e8f0ff;color:#0b2258;font-weight:800;box-shadow:inset 0 0 0 1px rgba(91,140,255,.15)}

    /* Appbar & cards */
    .appbar{background:rgba(255,255,255,.75);backdrop-filter:blur(8px);
      border:1px solid rgba(91,140,255,.08);border-radius:16px;box-shadow:0 10px 26px rgba(20,30,105,.08)}
    .gg-card{background:#fff;border:1px solid rgba(91,140,255,.08);border-radius:var(--gg-radius);box-shadow:0 10px 26px rgba(24,42,117,.08)}
    .gg-soft{background:linear-gradient(180deg,#f6f9ff 0%, #ffffff 100%);border:1px solid rgba(91,140,255,.06);border-radius:var(--gg-radius)}
    .chip{display:inline-flex;align-items:center;gap:.45rem;background:#eef4ff;border:1px solid rgba(91,140,255,.2);
      color:#0e2c73;padding:.25rem .6rem;border-radius:999px;font-size:.8125rem;font-weight:700}
    .empty-box{background:#f7f9ff;border:1px dashed rgba(91,140,255,.35);border-radius:16px;color:#6a7693}
    .foot{color:#93a0c0}

    /* Forms */
    .form-control,.form-select{background:#f6f8fd;border:1px solid #e8ecf4;border-radius:12px;font-weight:500}
    .form-control:focus,.form-select:focus{background:#fff;border-color:var(--gg-primary);box-shadow:0 0 0 .25rem rgba(91,140,255,.15)}
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row g-0">
      <!-- SIDEBAR -->
      <aside class="col-12 col-lg-2 side p-3 p-lg-4 d-none d-lg-block">
        <a class="text-decoration-none fw-black fs-5 text-dark d-inline-block mb-3" href="#">GIGAGEARS</a>
        <nav class="nav flex-column nav-gg">
          <a class="nav-link" href="dashboard.html"><i class="bi bi-grid-1x2"></i>Dashboard</a>
          <a class="nav-link" href="order.html"><i class="bi bi-bag"></i>Order</a>
          <a class="nav-link" href="product-add.html"><i class="bi bi-box"></i>Products</a>
          <a class="nav-link" href="balance.html"><i class="bi bi-wallet2"></i>Balance & Withdraw</a>
          <a class="nav-link active" href="analytics.html"><i class="bi bi-bar-chart"></i>Analytics & Report</a>
          <a class="nav-link" href="inbox.html"><i class="bi bi-inbox"></i>Inbox</a>
          <hr>
          <a class="nav-link" href="settings.html"><i class="bi bi-gear"></i>Settings</a>
        </nav>
        <div class="mt-4">
          <button class="btn btn-outline-danger w-100"><i class="bi bi-box-arrow-right me-1"></i> Log Out</button>
        </div>
      </aside>

      <!-- MAIN -->
      <main class="col-12 col-lg-10 p-3 p-md-4 p-lg-5">

        <!-- APP BAR -->
        <div class="appbar d-flex align-items-center justify-content-between px-3 py-2 mb-3">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-light d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#mSide">
              <i class="bi bi-list"></i>
            </button>
            <span class="d-none d-md-inline text-muted-gg">Analytics Center</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary"><i class="bi bi-download"></i> Export</button>
            <div class="dropdown">
              <button class="btn btn-outline-secondary d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width:34px;height:34px">TW</span>
                <span class="d-none d-sm-inline">{{Auth::user()->name}}</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="settings.html"><i class="bi bi-gear me-2"></i>Settings</a></li>
                <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i>Log Out</a></li>
              </ul>
            </div>
          </div>
        </div>

        <!-- TITLE -->
        <section class="gg-card p-4 mb-4">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
              <h1 class="fw-black mb-1">Analytics & Report</h1>
              <p class="text-muted-gg mb-0">Track your sales, funnel performance, and traffic sources.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
              <span class="chip"><i class="bi bi-cloud-download"></i> Auto-sync</span>
              <span class="chip"><i class="bi bi-shield-check"></i> Verified</span>
            </div>
          </div>
        </section>

        <!-- FILTERS -->
        <section class="gg-soft p-3 p-md-4 mb-3">
          <div class="row g-2 align-items-center">
            <div class="col-12 col-lg-4">
              <div class="input-group">
                <span class="input-group-text bg-transparent"><i class="bi bi-calendar3"></i></span>
                <input id="dateFrom" type="date" class="form-control">
                <span class="input-group-text">to</span>
                <input id="dateTo" type="date" class="form-control">
              </div>
            </div>
            <div class="col-8 col-lg-4">
              <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-outline-secondary btn-sm">Today</button>
                <button class="btn btn-outline-secondary btn-sm">Last 7d</button>
                <button class="btn btn-outline-secondary btn-sm">Last 30d</button>
                <button class="btn btn-outline-secondary btn-sm">This Year</button>
              </div>
            </div>
            <div class="col-4 col-lg-4 text-end">
              <div class="btn-group">
                <button class="btn btn-outline-secondary"><i class="bi bi-arrow-repeat"></i></button>
                <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Apply</button>
              </div>
            </div>
          </div>
        </section>

        <!-- ROW 1: Sales Overview + Top Products -->
        <section class="row g-3">
          <div class="col-12 col-xxl-7">
            <div class="gg-card p-3 p-md-4 h-100">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="mb-0">Sales Overview</h5>
                <select class="form-select" style="width:auto">
                  <option>Revenue</option>
                  <option>Orders</option>
                  <option>Units Sold</option>
                </select>
              </div>
              <!-- Empty chart placeholder -->
              <div class="empty-box ratio ratio-16x9 d-flex align-items-center justify-content-center">
                <div class="text-center p-3">
                  <div class="display-6 mb-2"><i class="bi bi-activity"></i></div>
                  <p class="mb-2 fw-semibold">No data yet</p>
                  <p class="text-muted-gg small mb-0">Connect your analytics backend to render charts here.</p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-xxl-5">
            <div class="gg-card p-3 p-md-4 h-100">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="mb-0">Top Products</h5>
                <a href="#" class="small text-decoration-none disabled" tabindex="-1">Export CSV</a>
              </div>
              <div class="table-responsive">
                <table class="table align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Product</th><th class="text-end">Units</th><th class="text-end">Revenue</th>
                    </tr>
                  </thead>
                  <tbody id="topProductsBody">
                    <tr>
                      <td colspan="3" class="py-5">
                        <div class="empty-box d-flex flex-column align-items-center justify-content-center py-5">
                          <div class="fs-4 mb-2"><i class="bi bi-box-seam"></i></div>
                          <div class="text-muted-gg">No products yet.</div>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </section>

        <!-- ROW 2: Funnel + Traffic -->
        <section class="row g-3 mt-1">
          <div class="col-12 col-xxl-6">
            <div class="gg-card p-3 p-md-4 h-100">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="mb-0">Conversion Funnel</h5>
                <select class="form-select" style="width:auto">
                  <option>All traffic</option><option>Ads</option><option>Organic</option><option>Social</option>
                </select>
              </div>
              <div class="empty-box d-flex align-items-center justify-content-center" style="height:320px">
                <div class="text-center">
                  <div class="fs-2 mb-2"><i class="bi bi-filter-square"></i></div>
                  <div class="fw-semibold">No data to visualize</div>
                  <div class="text-muted-gg small">Connect events: view → add-to-cart → checkout → purchase</div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-xxl-6">
            <div class="gg-card p-3 p-md-4 h-100">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="mb-0">Traffic Sources</h5>
                <select class="form-select" style="width:auto">
                  <option>Last 30 days</option><option>This Month</option><option>This Year</option>
                </select>
              </div>
              <div class="empty-box d-flex align-items-center justify-content-center" style="height:320px">
                <div class="text-center">
                  <div class="fs-2 mb-2"><i class="bi bi-pie-chart"></i></div>
                  <div class="fw-semibold">No data to visualize</div>
                  <div class="text-muted-gg small">When data is available, this will show a pie chart.</div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- ACTIONS -->
        <section class="mt-3 d-flex flex-wrap gap-2">
          <button class="btn btn-outline-secondary"><i class="bi bi-download me-1"></i>Export CSV</button>
          <button class="btn btn-outline-secondary"><i class="bi bi-filetype-pdf me-1"></i>Export PDF</button>
          <button class="btn btn-primary"><i class="bi bi-envelope me-1"></i>Email Report</button>
        </section>

        <p class="text-center mt-4 foot small">© 2025 GigaGears. All rights reserved.</p>
      </main>
    </div>
  </div>

  <!-- Mobile offcanvas sidebar -->
  <div class="offcanvas offcanvas-start" tabindex="-1" id="mSide">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title fw-black">GIGAGEARS</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <nav class="nav flex-column nav-gg">
        <a class="nav-link" href="dashboard.html"><i class="bi bi-grid-1x2"></i>Dashboard</a>
        <a class="nav-link" href="order.html"><i class="bi bi-bag"></i>Order</a>
        <a class="nav-link" href="product-add.html"><i class="bi bi-box"></i>Products</a>
        <a class="nav-link" href="balance.html"><i class="bi bi-wallet2"></i>Balance & Withdraw</a>
        <a class="nav-link active" href="analytics.html"><i class="bi bi-bar-chart"></i>Analytics & Report</a>
        <a class="nav-link" href="inbox.html"><i class="bi bi-inbox"></i>Inbox</a>
        <hr>
        <a class="nav-link" href="settings.html"><i class="bi bi-gear"></i>Settings</a>
      </nav>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- NOTE untuk backend:
    - GET /analytics/sales?from=&to=&metric= (revenue/orders/units) → series data untuk chart (gantikan empty-box)
    - GET /analytics/top-products?from=&to=&limit= → [{name, units, revenue}]
    - GET /analytics/funnel?from=&to=&channel= → {view, add_to_cart, checkout, purchase}
    - GET /analytics/traffic?from=&to= → breakdown by channel untuk pie
    Semua panel saat ini empty state agar mudah diisi nanti.
  -->
</body>
</html>