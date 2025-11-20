<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears — Dashboard</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- Google Font (Chakra Petch) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- App CSS -->
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <div class="container-fluid">
    <div class="row g-0">
      <!-- ===== SIDEBAR (KONSISTEN) ===== -->
      <aside class="col-12 col-lg-2 side d-none d-lg-block">
        <a href="dashboard.html" class="brand-link" aria-label="GigaGears">
          <img src="../assets/gigagears-logo.png" alt="GigaGears" class="brand-logo">
        </a>

        <nav class="nav flex-column nav-gg">
          <a class="nav-link active" href="dashboard.html"><i class="bi bi-grid-1x2"></i>Dashboard</a>
          <a class="nav-link" href="order.html"><i class="bi bi-bag"></i>Order</a>
          <a class="nav-link" href="product.html"><i class="bi bi-box"></i>Products</a>
          <a class="nav-link" href="balance.html"><i class="bi bi-wallet2"></i>Balance & Withdraw</a>
          <a class="nav-link" href="analytics.html"><i class="bi bi-bar-chart"></i>Analytics & Report</a>
          <a class="nav-link" href="inbox.html"><i class="bi bi-inbox"></i>Inbox</a>
          <hr>
          <a class="nav-link" href="settings.html"><i class="bi bi-gear"></i>Settings</a>
        </nav>

        <div class="mt-4">
          <button class="btn btn-outline-danger w-100"><i class="bi bi-box-arrow-right me-1"></i> Log Out</button>
        </div>
      </aside>

      <!-- ===== MAIN ===== -->
      <main class="col-12 col-lg-10 main-wrap">

        <!-- App Bar gradient -->
        <div class="appbar px-3 px-md-4 py-3 mb-4 d-flex align-items-center justify-content-between">
          <div>
            <div class="small opacity-75 mb-1">Welcome back 👋</div>
            <h1 class="wc-title mb-0">TechnoWorld</h1>
          </div>
          <div class="d-flex gap-2">
            <span class="badge-chip d-inline-flex align-items-center gap-2"><i class="bi bi-bell"></i></span>
            <span class="badge-chip d-inline-flex align-items-center gap-2">
              <span class="rounded-circle bg-white text-primary fw-bold d-inline-flex align-items-center justify-content-center" style="width:28px;height:28px">TW</span>
              Profil
            </span>
          </div>
        </div>

        <!-- KPI Row -->
        <div class="row g-3 mb-3">
          <div class="col-12 col-md-6 col-xl-3">
            <div class="kpi">
              <span class="rounded-3 bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px"><i class="bi bi-bag"></i></span>
              <div><div class="small text-muted-gg">Orders Today</div><div class="fs-4 fw-black">12</div></div>
            </div>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <div class="kpi">
              <span class="rounded-3 bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px"><i class="bi bi-piggy-bank"></i></span>
              <div><div class="small text-muted-gg">Monthly Revenue</div><div class="fs-4 fw-black">$2,430</div></div>
            </div>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <div class="kpi">
              <span class="rounded-3 bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px"><i class="bi bi-box-seam"></i></span>
              <div><div class="small text-muted-gg">Active Products</div><div class="fs-4 fw-black">34</div></div>
            </div>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <div class="kpi">
              <span class="rounded-3 bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px"><i class="bi bi-star"></i></span>
              <div><div class="small text-muted-gg">Store Rating</div><div class="fs-4 fw-black">4.7</div></div>
            </div>
          </div>
        </div>

        <!-- Chart & Recent -->
        <div class="row g-3">
          <div class="col-12 col-xl-8">
            <section class="gg-card p-3 p-md-4">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Sales Chart</h5>
                <button class="btn btn-sm btn-outline-secondary">This Year</button>
              </div>
              <div class="text-muted-gg">Hubungkan ke backend/Chart.js untuk grafik dinamis.</div>
              <div class="mt-4" style="height:300px;background:linear-gradient(180deg,#f7faff,#fff);border:1px dashed #e1e7f8;border-radius:12px"></div>
            </section>
          </div>

          <div class="col-12 col-xl-4">
            <section class="gg-card p-3 p-md-4">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Recent Order</h5>
                <a href="#" class="small text-decoration-none">Full Detail</a>
              </div>

              <div class="table-responsive">
                <table class="table gg-table mb-0">
                  <thead>
                    <tr>
                      <th>Order ID</th>
                      <th>Product</th>
                      <th>Customer</th>
                      <th>Status</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>#10231</td>
                      <td>Logitech G Pro X</td>
                      <td>John Doe</td>
                      <!-- gunakan komponen status yang konsisten -->
                      <td><span class="badge-status completed">Completed</span></td>
                      <td>$99</td>
                    </tr>
                    <tr>
                      <td>#10230</td>
                      <td>Logitech G Pro X</td>
                      <td>John Doe</td>
                      <td><span class="badge-status completed">Completed</span></td>
                      <td>$99</td>
                    </tr>
                    <tr>
                      <td>#10229</td>
                      <td>Logitech G Pro X</td>
                      <td>John Doe</td>
                      <td><span class="badge-status completed">Completed</span></td>
                      <td>$99</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>
          </div>
        </div>

        <p class="text-center mt-4 foot small mb-0">© 2025 GigaGears. All rights reserved.</p>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
