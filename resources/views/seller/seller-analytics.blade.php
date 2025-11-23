<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears — Analytics & Report</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- Font -->
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- App CSS -->
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="container-fluid">
  <div class="row g-0">
    <!-- ===== SIDEBAR ===== -->
    <aside class="col-12 col-lg-2 side d-none d-lg-block">
      <a href="dashboard.html" class="brand-link" aria-label="GigaGears">
        <img src="../assets/gigagears-logo.png" alt="GigaGears" class="brand-logo">
      </a>

      <nav class="nav flex-column nav-gg">
        <a class="nav-link" href="dashboard.html"><i class="bi bi-grid-1x2"></i>Dashboard</a>
        <a class="nav-link" href="order.html"><i class="bi bi-bag"></i>Order</a>
        <a class="nav-link" href="product.html"><i class="bi bi-box"></i>Products</a>
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

    <!-- ===== MAIN ===== -->
    <main class="col-12 col-lg-10 main-wrap">
      <!-- App Bar -->
      <div class="appbar px-3 px-md-4 py-3 mb-4 d-flex align-items-center justify-content-between">
        <div>
          <div class="small opacity-75 mb-1">Track your store performance</div>
          <h1 class="wc-title mb-0">Reports & Analytics</h1>
        </div>
        <div class="d-flex gap-2">
          <span class="badge-chip d-inline-flex align-items-center gap-2"><i class="bi bi-bell"></i></span>
          <span class="badge-chip d-inline-flex align-items-center gap-2">
            <span class="rounded-circle bg-white text-primary fw-bold d-inline-flex align-items-center justify-content-center" style="width:28px;height:28px">TW</span>
            Admin
          </span>
        </div>
      </div>

      <!-- KPI Cards -->
      <section class="row g-3 mb-3">
        <div class="col-12 col-md-6 col-xl-3">
          <div class="gg-card p-3 text-center">
            <div class="small text-muted-gg mb-1">Total Revenue</div>
            <div id="kpiRevenue" class="fs-1 fw-black">{{ $total_revenue_this_month }}</div>
          </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
          <div class="gg-card p-3 text-center">
            <div class="small text-muted-gg mb-1">Total Orders</div>
            <div id="kpiOrders" class="fs-1 fw-black">{{ $total_order }}</div>
          </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
          <div class="gg-card p-3 text-center">
            <div class="small text-muted-gg mb-1">Products Sold</div>
            <div id="kpiSold" class="fs-1 fw-black"> {{ $product_sold }} </div>
          </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
          <div class="gg-card p-3 text-center">
            <div class="small text-muted-gg mb-1">New Customers</div>
            <div id="kpiCustomers" class="fs-1 fw-black">{{ $total_customer }}</div>
          </div>
        </div>
      </section>

      <!-- Orders Over Time -->
      <section class="gg-card p-3 p-md-4 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="col-lg-8">
            <h5 class="mb-0">Orders Over Time</h5>
          </div>
          <div class="col-lg-4 d-flex justify-content-end align-items-center gap-2">
            <select wire:model.debounce.250ms="month" class="form-select form-select-sm">
              @foreach($months as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
              @endforeach
                </select>
          </div>
        </div>
        <canvas id="ordersChart" height="120"></canvas>
      </section>

      <!-- Bottom row: Best-Selling & Top Customer -->
      <section class="row g-3 mb-3">
        <div class="col-12 col-xl-8">
          <div class="gg-card p-3 p-md-4 h-100">
            <h5 class="mb-3">Best-Selling Products</h5>
            <div class="row g-3">
              <div class="col-12 col-md-5">
                <canvas id="bestPie" height="220"></canvas>
              </div>
              <div class="col-12 col-md-7">
                <div class="table-responsive">
                  <table class="table mb-0 align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Product</th>
                        <th class="text-end" style="width:120px">Units</th>
                      </tr>
                    </thead>
                    <tbody id="bestList">
                      @forelse($best_selliing_prod as $prod)
                      <td> {{ $prod->product->name }} </td>
                      <td> {{ $prod->sold_items }} </td>
                      @empty`
                      <tr>
                          <td colspan="2" class="text-center text-muted">Belum ada penjualan.</td>
                      </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-xl-4">
          <div class="gg-card p-3 p-md-4 h-100">
            <h5 class="mb-3">Top Customer</h5>
            <ul id="topCustomer" class="list-unstyled m-0">
              <li class="d-flex align-items-center justify-content-between border rounded-3 p-2 mb-2">
                <div class="d-flex align-items-center gap-2">
                  @foreach($topCustomers as $i => $cust)
                  <span class="badge bg-primary-subtle text-primary">{{ $i+1 }}</span>
                  <div>
                    <div class="fw-semibold">{{ $cust->user->name }}</div>
                    <small class="text-muted">Rp. {{ number_format($cust->total_spent, 2) }}</small>
                    </div>
                  </div>
                <div class="text-end">
                <div class="fw-semibold">{{ $cust->total_orders }} Orders</div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </section>
      <p class="text-center mt-4 foot small mb-0">© 2025 GigaGears. All rights reserved.</p>
    </main>
  </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const labels = @json($topProducts->pluck('product.name'));
    const data = @json($topProducts->pluck('total_sold'));

    const ctx = document.getElementById('topProductsChart').getContext('2d');
    
    const colors = [
      '#5DADE2', '#F1948A', '#F8C471', 
      '#7FB3D5', '#A569BD', '#48C9B0'
    ];

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors.slice(0, labels.length),
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 6
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 20,
                    }
                }
            },
            cutout: "55%", // ini bikin lubang tengah (donut)
        }
    });

});
</script>

</body>
</html>
