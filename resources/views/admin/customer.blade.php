<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears Admin — Customer Data</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="css/admin.css">

  <style>
    /* 1. Fix Lebar & Layout Sidebar */
    .admin-side {
        width: 280px; 
        height: 100vh; 
        background: #fff;
        border-right: 1px solid #E6ECFB;
        flex-shrink: 0;
        transition: transform 0.3s ease-in-out;
        
        /* Flexbox untuk susunan vertikal rapi */
        display: flex;           
        flex-direction: column;  
        position: sticky; top: 0;
    }

    /* Agar Menu bisa di-scroll jika terlalu panjang di HP */
    .nav-scroll-wrap {
        flex: 1;
        overflow-y: auto;
    }

    /* 2. Logika Tampilan Mobile (HP) */
    @media (max-width: 991.98px) {
        .admin-side {
            position: fixed; 
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
          <img src="{{assets('assets/logo GigaGears.png')}}" alt="GigaGears" class="brand-logo">
        </a>

        <div class="nav-scroll-wrap">
            <nav class="nav flex-column nav-admin">
              <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a>
              <a class="nav-link" href="{{ route('admin.customers.index') }}"><i class="bi bi-people"></i>Data Customer</a>
              <a class="nav-link" href="{{ route('admin.sellers.index') }}"><i class="bi bi-person-badge"></i>Data Seller</a>
              <a class="nav-link" href="{{ route('admin.transactions.index') }}"><i class="bi bi-receipt"></i>Data Transaction</a>
              <a class="nav-link" href="{{ route('admin.products.index') }}"><i class="bi bi-box"></i>Products</a>
              <a class="nav-link active" href="{{ route('admin.shipping.index') }}"><i class="bi bi-truck"></i>Shipping Settings</a>
            </nav>
        </div>

        <div class="mt-auto pb-4 px-3 pt-3 border-top">
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
                <div class="small opacity-75 mb-1">View and manage all registered customers</div>
                <h1 class="h3 mb-0">Customer Data</h1>
            </div>
          </div>

          <div class="d-flex align-items-center gap-2">
            <span class="badge-chip d-inline-flex align-items-center gap-2">
              <img src="https://ui-avatars.com/api/?name=Admin&background=random" class="rounded-circle" width="24" height="24" alt="A">
              Admin
            </span>
          </div>
        </div>

      <form action="{{ route('admin.customers.index') }}" method="GET">
        <div class="gg-card p-3 p-md-4 mb-3">
          <div class="row g-2 align-items-center">
            <div class="col-12 col-md-6">
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by ID, Name, Email...">
                  <button class="btn btn-primary px-4">Search</button>
              </div>
              
            </div>
          </div>
        </div>
        
      </form>

        <section class="gg-card p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th style="width:110px">Cust ID</th>
                  <th>Full Name</th>
                  <th>Email</th>
                  <th>Phone Number</th>
                  <th style="width:150px">Registered</th>
                  <th style="width:100px">Status</th>
                  <th style="width:160px" class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody id="tbodyCustomers">
                @forelse($customers as $c)
                  <tr>
                      <td class="fw-semibold text-muted">
                          CUST-{{ str_pad($c->id, 4, '0', STR_PAD_LEFT) }}
                      </td>
                      <td class="fw-bold">{{ $c->name }}</td>
                      <td>{{ $c->email }}</td>
                      <td>{{ $c->phone ?? '-' }}</td>
                      <td>{{ $c->created_at->format('d M Y') }}</td>
                      <td>
                          <span class="badge-status {{ strtolower($c->status) }}">
                              {{ $c->status }}
                          </span>
                      </td>
                      <td class="text-end text-nowrap">
                          {{-- EDIT --}}
                          <button class="btn p-0 border-0 text-secondary ms-2"
                                  onclick="openCustomerEdit({{ $c->id }})">
                              <i class="bi bi-pencil-square fs-5"></i>
                          </button>
                          {{-- ACTIVATE / SUSPEND --}}
                          @if(strtolower($c->status) === 'active')
                              <form action="{{ route('admin.customers.update-status', $c->id) }}" 
                                    method="POST" class="d-inline">
                                  @csrf
                                  @method('PATCH')
                                  <input type="hidden" name="status" value="Suspended">
                                  <button class="btn p-0 border-0 text-danger ms-2">
                                      <i class="bi bi-dash-circle-fill fs-5"></i>
                                  </button>
                              </form>
                          @elseif(strtolower($c->status) === 'banned')
                              <form action="{{ route('admin.customers.update-status', $c->id) }}" 
                                    method="POST" class="d-inline">
                                  @csrf
                                  @method('PATCH')
                                  <input type="hidden" name="status" value="Active">
                                  <button class="btn p-0 border-0 text-success ms-2">
                                      <i class="bi bi-check-circle-fill fs-5"></i>
                                  </button>
                              </form>
                          @endif
                      </td>
                  </tr>
                  @empty
                  <tr>
                      <td colspan="7" class="text-center text-muted py-4">
                          No customers found.
                      </td>
                  </tr>
                  @endforelse
              </tbody>
            </table>
          </div>

          <div class="d-flex justify-content-between align-items-center px-3 px-md-4 py-3 border-top">
            <div class="small text-muted"><span id="countInfo">0</span> results</div>
            <nav><ul class="pagination pagination-sm mb-0" id="pager"></ul></nav>
          </div>
        </section>

        <p class="text-center mt-4 foot small mb-0">© 2025 GigaGears. All rights reserved.</p>
    </main>
  </div>

  <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Customer</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editCustomerForm" method="POST">
        @csrf
        @method('PATCH')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small text-muted">Full Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label small text-muted">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label small text-muted">Phone Number</label>
            <input type="text" name="phone" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label small text-muted">Address</label>
            <textarea name="address" rows="2" class="form-control"></textarea>
          </div>

        </div>

        <div class="modal-footer bg-light">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary">Save Changes</button>
        </div>

      </form>
      </div>
    </div>
  </div>

 

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
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

    function openCustomerEdit(id) {
      fetch(`/admin/customers/${id}/json`)
          .then(res => res.json())
          .then(c => {

              const form = document.getElementById('editCustomerForm');
              form.action = `/admin/customers/${id}/edit`;

              form.name.value = c.name;
              form.email.value = c.email;
              form.phone.value = c.phone;
              form.address.value = c.address;

              new bootstrap.Modal(document.getElementById('editModal')).show();
        });
    }
  </script>


</body>
</html>