<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears — Inbox</title>

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
        <a class="nav-link" href="{{ route('seller.index') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a>
          <a class="nav-link" href="{{ route('seller.orders') }}"><i class="bi bi-bag"></i>Order</a>
          <a class="nav-link active" href="{{ route('seller.products') }}"><i class="bi bi-box"></i>Products</a>
          <a class="nav-link" href="{{ route('seller.analytics') }}"><i class="bi bi-bar-chart"></i>Analytics & Report</a>
          <a class="nav-link" href="{{ route('seller.inbox') }}"><i class="bi bi-inbox"></i>Inbox</a>
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
          <div class="small opacity-75 mb-1">See feedback and messages</div>
          <h1 class="wc-title mb-0">Inbox</h1>
        </div>
        <div class="d-flex gap-2">
          <span class="badge-chip d-inline-flex align-items-center gap-2"><i class="bi bi-bell"></i></span>
          <span class="badge-chip d-inline-flex align-items-center gap-2">
            <span class="rounded-circle bg-white text-primary fw-bold d-inline-flex align-items-center justify-content-center" style="width:28px;height:28px">TW</span>
            Profil
          </span>
        </div>
      </div>

      <!-- Search + Filter -->
      <section class="gg-card p-3 p-md-4 mb-3">
        <div class="row g-2 align-items-center">
          <div class="col-12 col-md">
            <div class="input-group">
              <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
              <input id="inboxSearch" type="text" class="form-control" placeholder="Search by customer, product, message…">
            </div>
          </div>
          <div class="col-12 col-md-4 col-lg-3">
            <select id="inboxFilter" class="form-select">
              <option value="all">All Review & Message</option>
              <option value="review">Only Review</option>
              <option value="message">Only Message</option>
            </select>
          </div>
        </div>
      </section>

      <!-- Table -->
      <section class="gg-card p-0 mb-3">
        <div class="d-flex align-items-center justify-content-between px-3 px-md-4 py-3 border-bottom">
          <h5 class="mb-0">Customer Feedback</h5>
          <small class="text-muted"><span id="tbl-count">0</span> items</small>
        </div>
        <div class="table-responsive p-3 overflow-hidden">
          <table class="table mb-0 align-middle">
            <thead>
              <tr>
                <th style="width:160px">Customer</th>
                <th style="width:220px">Product</th>
                <th style="width:110px">Rating</th>
                <th>Review</th>
                <th style="width:150px">Date</th>
              </tr>
            </thead>
            <tbody>
             @forelse($product_feedbacks as $pf)
              <tr>
                <td>{{ $pf->user->name }}</td>
                <td>{{ $pf->product->name }}</td>
                <td>{{ $pf->rating }}</td>
                <td> {{ $pf->comment }}</td>
                <td>{{ $pf->created_at }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center">No reviews or messages found.</td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </section>

      <!-- Message List -->
      <section class="gg-card p-3 p-md-4">
        <h5 class="mb-3">Inbox Message</h5>
        <div id="msgList" class="d-flex flex-column gap-2"></div>
        @forelse($store_feedbacks as $it)
        <div class="d-flex align-items-start gap-3 p-2 rounded-3 border">
          <div class="rounded-circle bg-secondary-subtle d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px">
              <i class="bi bi-person"></i>
          </div>
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between">
                <strong>${it.customer}</strong>
                <small class="text-muted">${it.date.split(' ')[1]}</small>
              </div>
              <div class="text-muted small">${it.message}</div>
            </div>
        </div>
         @empty
          <div class="text-center w-100">No reviews or messages found.</div>
        @endforelse
      </section>
      <p class="text-center mt-4 foot small mb-0">© 2025 GigaGears. All rights reserved.</p>
    </main>
  </div>
</div>



<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
