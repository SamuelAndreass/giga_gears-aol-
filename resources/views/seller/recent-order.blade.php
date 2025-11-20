<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears — Orders</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- Google Font -->
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
          <a class="nav-link" href="dashboard.html"><i class="bi bi-grid-1x2"></i>Dashboard</a>
          <a class="nav-link active" href="order.html"><i class="bi bi-bag"></i>Order</a>
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
            <div class="small opacity-75 mb-1">Orders</div>
            <h1 class="wc-title mb-0">Your Order</h1>
          </div>
          <div class="d-flex gap-2">
            <span class="badge-chip d-inline-flex align-items-center gap-2"><span class="rounded-circle bg-white text-primary fw-bold d-inline-flex align-items-center justify-content-center" style="width:28px;height:28px">TW</span> TechnoWorld</span>
          </div>
        </div>

        <!-- Filter row -->
        <div class="gg-card p-3 p-md-4 mb-3">
          <div class="row g-2 align-items-center">
            <div class="col-12 col-lg">
              <div class="input-group">
                <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                <input id="orderSearch" type="text" class="form-control" placeholder="Search by order ID, buyer, product…">
              </div>
            </div>

            <div class="col-12 col-md-5 col-lg-3">
              <select id="orderStatus" class="form-select">
                <option value="all">Order Status (all)</option>
                <option value="Pending">Pending</option>
                <option value="Processing">Processing</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
                <option value="Refunded">Refunded</option>
              </select>
            </div>

            <div class="col-6 col-md-3 col-lg-1">
              <button id="orderReset" class="btn btn-outline-secondary w-100" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
              </button>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
              <button id="orderApply" class="btn btn-primary w-100">
                <i class="bi bi-funnel me-1"></i>Apply
              </button>
            </div>
          </div>
        </div>

        <!-- Recent Orders table -->
        <section class="gg-card p-0 orders">
          <div class="d-flex align-items-center justify-content-between px-3 px-md-4 py-3 border-bottom">
            <h5 class="mb-0">Recent Order</h5>
          </div>

          <div class="table-responsive">
            <table id="ordersTable" class="table mb-0">
              <thead>
                <tr>
                  <th style="width:110px">Order ID</th>
                  <th>Product</th>
                  <th style="width:160px">Customer</th>
                  <th style="width:120px">Status</th>
                  <th class="col-qty">QTY</th>
                  <th style="width:90px">Total</th>
                  <th style="width:140px">Date</th>
                  <th style="width:120px">Actions</th>
                </tr>
              </thead>
              <tbody id="ordersBody">
                @php $i = 0; @endphp
                @forelse ($orders as $order)
                <tr>
                  <th scope='row'>#{{ $order->id}}</th>
                  <td>{{ $order->product->name }}</td>
                  <td>{{ $order->user->name }}</td>
                  <td>{{ $order->status }}</td>
                  <td>{{ $order->item->qty }}</td>
                  <td>{{ $order->item->price }}</td>
                  <td>{{ $order->order_date }}</td>    
                  <td><button class="btn btn-primary btn-sm btn-update me-2" data-bs-toggle="modal" data-bs-target="#orderUpdateModal">Update</button></td>
                  @php $i++ @endphp
                </tr>
                <tr>
                  @empty
                  <td colspan="8" class="text-center text-muted py-4">Tidak ada order.</td>
                  @endforelse
                </tr>
              </tbody>
              <tfoot>
                <tr id="noResultsRow" style="display:none">
                  <td colspan="8" class="text-center text-muted py-4">Tidak ada hasil yang cocok.</td>
                </tr>
              </tfoot>
            </table>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
          </div>
        </section>

        <p class="text-center mt-4 foot small mb-0">© 2025 GigaGears. All rights reserved.</p>
      </main>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- ===================== UPDATE ORDER MODAL ===================== -->
  <div class="modal fade" id="orderUpdateModal" tabindex="-1" aria-labelledby="orderUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-light">
          <div>
            <h5 class="m-2">Update Order</h5>
          </div>
          <div class="d-flex align-items-center gap-4">
            <button type="button" class="btn btn-icon btn-outline-secondary" data-bs-dismiss="modal" aria-label="Close">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <!-- KIRI: Form utama -->
            <div class="col-12 col-lg-8">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-12 col-md-6">
                      <label class="form-label">Status</label>
                      <div class="form-control bg-light border-0">Status</div>
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label">Total</label>
                      <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <div id="ou-total" class="form-control bg-light fw-semibold"> {{$orders->total_amount}}</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <hr class="my-2">
                      <div class="small text-muted">Shipping Information</div>
                    </div>

                    <div class="col-12 col-md-6">
                      <label class="form-label">Jasa Kirim</label>
                      <div class="form-control bg-light border-0">JNE / J&T / SiCepat …</div>
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label">No. Resi</label>
                      <div class="form-control bg-light border-0">No. pelacakan / airway bill</div>
                    </div>

                    <div class="col-12 col-md-6">
                      <label class="form-label">Tanggal Kirim</label>
                      <div class="form-control bg-light border-0">01-04-2024</div>
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label">Estimasi Tiba</label>
                      <div class="form-control bg-light border-0">01-04-2024</div>
                    </div>
                  </div><!-- /row -->
                </div>
              </div>
            </div>

            <!-- KANAN: Aksi cepat + timeline -->
            <div class="col-12 col-lg-4">
              <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                  <div class="small text-muted mb-2">Quick actions</div>
                  <div class="d-grid gap-2">
                    <button class="btn btn-success" id="ou-mark-completed"><i class="bi bi-check-circle me-1"></i> Mark as Shipped</button>
                    <button class="btn btn-outline-danger" id="ou-cancel-order"><i class="bi bi-x-octagon me-1"></i> Cancel</button>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /row -->
        </div>

        <div class="modal-footer bg-light d-flex justify-content-between">
          <div class="text-muted small">
            Perubahan ini <b>belum</b> tersimpan hingga kamu menekan <b>Save changes</b>.
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            <button class="btn btn-primary" id="ou-save">Save changes</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- =================== SCRIPT: UPDATE MODAL ===================== -->
  <script>
  (() => {
    const modalEl = document.getElementById('orderUpdateModal');
    const statusBadge = document.getElementById('ou-status-badge');

    function badgeClassByStatus(s){
      const m = {
        Pending: 'bg-warning text-dark',
        Processing: 'bg-info text-dark',
        Completed: 'bg-success',
        Cancelled: 'bg-danger',
        Refunded: 'bg-secondary'
      };
      return m[s] || 'bg-secondary';
    }

    document.addEventListener('click', (e) => {
      const btn = e.target.closest('.btn-update');
      if(!btn) return;

      const orderId   = btn.dataset.orderId || '—';
      const customer  = btn.dataset.customer || '—';
      const status    = btn.dataset.status || 'Pending';
      const carrier   = btn.dataset.carrier || '';
      const tracking  = btn.dataset.tracking || '';
      const shipDate  = btn.dataset.shipDate || '';
      const etaDate   = btn.dataset.etaDate || '';
      const total     = btn.dataset.total || '';

      document.getElementById('ou-id').textContent = orderId;
      document.getElementById('ou-customer').textContent = customer;
      document.getElementById('ou-status').value = status;
      document.getElementById('ou-carrier').value = carrier;
      document.getElementById('ou-tracking').value = tracking;
      document.getElementById('ou-shipdate').value = shipDate;
      document.getElementById('ou-eta').value = etaDate;
      document.getElementById('ou-total').value = total;

      statusBadge.className = 'badge rounded-pill ' + badgeClassByStatus(status);
      statusBadge.textContent = status;

      document.getElementById('ou-customer-note').value = '';
      document.getElementById('ou-internal-note').value = '';
      document.getElementById('ou-notify').checked = true;
    });

    document.getElementById('ou-status').addEventListener('change', (e)=>{
      const val = e.target.value;
      statusBadge.className = 'badge rounded-pill ' + badgeClassByStatus(val);
      statusBadge.textContent = val;
    });

    document.getElementById('ou-save').addEventListener('click', ()=>{
      alert('✅ Perubahan tersimpan (simulasi). Kirim ke API untuk menyimpan ke server.');
      const modal = bootstrap.Modal.getInstance(modalEl);
      modal?.hide();
    });

    document.getElementById('ou-mark-completed').addEventListener('click', ()=>{
      document.getElementById('ou-status').value = 'Completed';
      document.getElementById('ou-status').dispatchEvent(new Event('change'));
    });

    document.getElementById('ou-cancel-order').addEventListener('click', ()=>{
      if(confirm('Yakin membatalkan pesanan ini?')) {
        document.getElementById('ou-status').value = 'Cancelled';
        document.getElementById('ou-status').dispatchEvent(new Event('change'));
      }
    });

    document.getElementById('ou-print').addEventListener('click', ()=>{
      alert('🖨️ Cetak invoice (simulasi). Di produksi, arahkan ke halaman/route cetak.');
    });
  })();
  </script>

  <!-- ===================== SCRIPT: RENDER TABEL + VIEW MODAL ===================== -->
  <script>
  // Contoh data (dummy). Ganti dengan data backend.
  const orders = [
    {
      id:"#10231",
      product:"Logitech G Pro X",
      customer:"John Doe",
      email:"john@example.com",
      phone:"+62 812-3456-7890",
      address:"Jl. Mawar No. 1, Jakarta",
      status:"Pending",
      qty:1,
      total:99,
      date:"2025-09-25 10:20",
      carrier:"JNE",
      tracking:"SOC-123456789",
      shipDate:"2025-09-25",
      etaDate:"2025-09-30",
      items:[{name:"Logitech G Pro X", qty:1, price:99}]
    },
    {
      id:"#10230",
      product:"Adobe Creative Cloud",
      customer:"Amelia W",
      email:"amelia@example.com",
      phone:"+62 811-0000-2222",
      address:"Jl. Kenanga No. 2, Bandung",
      status:"Completed",
      qty:1,
      total:99,
      date:"2025-09-24 15:10",
      carrier:"JNE",
      tracking:"SOC-987654321",
      shipDate:"2025-09-22",
      etaDate:"2025-09-26",
      items:[{name:"Adobe Creative Cloud License", qty:1, price:99}]
    }
  ];

  // Render baris tabel
  const rows = orders.map(o => `
    <tr>
      <td>${o.id}</td>
      <td>${o.product}</td>
      <td>${o.customer}</td>
      <td><span class="badge-status ${o.status.toLowerCase()}">${o.status}</span></td>
      <td>${o.qty}</td>
      <td>$${o.total}</td>
      <td>
        <span class="subdate">${o.date.split(' ')[0]}</span>
        <span class="subdate">${o.date.split(' ')[1]}</span>
      </td>
      <td class="text-end">
        <button
          class="btn btn-primary btn-sm btn-update me-2"
          data-bs-toggle="modal" data-bs-target="#orderUpdateModal"
          data-order-id="${o.id}"
          data-customer="${o.customer}"
          data-status="${o.status}"
          data-carrier="${o.carrier||''}"
          data-tracking="${o.tracking||''}"
          data-ship-date="${o.shipDate||''}"
          data-eta-date="${o.etaDate||''}"
          data-total="${o.total}"
        >Update</button>

        <button
          class="btn btn-outline-secondary btn-sm btn-view"
          data-bs-toggle="modal" data-bs-target="#orderViewModal"
          data-order-id="${o.id}"
          data-customer="${o.customer}"
          data-email="${o.email||''}"
          data-phone="${o.phone||''}"
          data-address="${o.address||''}"
          data-status="${o.status}"
          data-total="${o.total}"
          data-items='${JSON.stringify(o.items||[])}'
        ><i class="bi bi-eye"></i></button>
      </td>
    </tr>
  `).join('');

  document.getElementById('ordersBody').innerHTML = rows;

  // Handler tombol 'Lihat' (mata)
  document.addEventListener('click', (e)=>{
    const btn = e.target.closest('.btn-view');
    if(!btn) return;

    const id = btn.dataset.orderId || '—';
    const customer = btn.dataset.customer || '—';
    const email = btn.dataset.email || '—';
    const phone = btn.dataset.phone || '—';
    const addres  s = btn.dataset.address || '—';
    const status = btn.dataset.status || '—';
    const total = parseFloat(btn.dataset.total || '0');
    let items = [];
    try { items = JSON.parse(btn.dataset.items || '[]'); } catch(_) {}

    // Header
    document.getElementById('vo-id').textContent = id;
    const voStatus = document.getElementById('vo-status');
    voStatus.textContent = status;
    voStatus.className = 'badge align-middle ' + (
      status === 'Pending'   ? 'bg-warning text-dark' :
      status === 'Completed' ? 'bg-success' :
      status === 'Cancelled' ? 'bg-danger' :
      status === 'Processing'? 'bg-info text-dark' : 'bg-secondary'
    );

    // Customer
    document.getElementById('vo-customer').textContent = customer;
    document.getElementById('vo-email').textContent = email;
    document.getElementById('vo-phone').textContent = phone;
    document.getElementById('vo-address').textContent = address;

    // Items
    const tbody = document.querySelector('#orderViewModal #vo-items tbody');
    tbody.innerHTML = items.map(it => {
      const sub = (Number(it.price||0) * Number(it.qty||0)).toFixed(2);
      return `
        <tr>
          <td>${it.name||'-'}</td>
          <td class="text-center">${it.qty||0}</td>
          <td class="text-end">$${Number(it.price||0).toFixed(2)}</td>
          <td class="text-end">$${sub}</td>
        </tr>
      `;
    }).join('');

    document.getElementById('vo-total').textContent = total.toFixed(2);

    // Timeline contoh
    const tl = document.getElementById('vo-timeline');
    tl.innerHTML = `
      <li>Order created</li>
      <li>Status: ${status}</li>
    `;
  });
  </script>

  <!-- ===================== SCRIPT: FILTER SEARCH + STATUS ===================== -->
  <script>
  (() => {
    const $ = (sel, ctx=document) => ctx.querySelector(sel);
    const $$ = (sel, ctx=document) => Array.from(ctx.querySelectorAll(sel));

    const searchInput = $('#orderSearch');
    const statusSelect = $('#orderStatus');
    const applyBtn     = $('#orderApply');
    const resetBtn     = $('#orderReset');

    const noRow = $('#noResultsRow');

    const norm = s => (s ?? '').toString().toLowerCase().trim();

    function matchRow(tr, q, status) {
      const text = norm(tr.textContent);
      const badge = tr.querySelector('.badge-status');
      const rowStatus = badge ? badge.textContent.trim() : (tr.children[3]?.textContent.trim() || '');
      const okSearch = !q || text.includes(q);
      const okStatus = (status === 'all') || (rowStatus === status);
      return okSearch && okStatus;
    }

    function filterOrders() {
      const q = norm(searchInput.value);
      const st = statusSelect.value;

      let visible = 0;
      $$('#ordersBody tr').forEach(tr => {
        const show = matchRow(tr, q, st);
        tr.style.display = show ? '' : 'none';
        if (show) visible++;
      });

      if (noRow) noRow.style.display = visible === 0 ? '' : 'none';
    }

    let t = null;
    searchInput?.addEventListener('input', () => {
      clearTimeout(t);
      t = setTimeout(filterOrders, 150);
    });

    statusSelect?.addEventListener('change', filterOrders);
    applyBtn?.addEventListener('click', filterOrders);

    resetBtn?.addEventListener('click', () => {
      searchInput.value = '';
      statusSelect.value = 'all';
      filterOrders();
      searchInput.focus();
    });

    // jalankan awal
    filterOrders();
  })();
  </script>
</body>
</html>


