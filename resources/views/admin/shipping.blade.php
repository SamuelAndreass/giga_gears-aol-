<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears Admin — Shipping</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="asset('css/admin.css')}}">

  <style>
    /* --- Layout Fix (Sidebar & Mobile) --- */
    .admin-side {
        width: 280px; height: 100vh; background: #fff; border-right: 1px solid #E6ECFB;
        flex-shrink: 0; transition: transform 0.3s ease-in-out;
        display: flex; flex-direction: column; position: sticky; top: 0;
    }
    .nav-admin { flex: 1; overflow-y: auto; }

    @media (max-width: 991.98px) {
        .admin-side { position: fixed; top: 0; left: 0; bottom: 0; z-index: 1050; transform: translateX(-100%); box-shadow: none; }
        .admin-side.show { transform: translateX(0); box-shadow: 4px 0 24px rgba(0,0,0,0.15); }
    }
    .sidebar-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); 
        z-index: 1040; display: none; opacity: 0; transition: opacity 0.3s;
    }
    .sidebar-overlay.show { display: block; opacity: 1; }

    /* --- Style Khusus Shipping --- */
    .ship-card {
        background: #fff; border: 1px solid var(--gg-border); border-radius: 12px;
        padding: 24px; margin-bottom: 16px; transition: transform 0.2s, box-shadow 0.2s;
    }
    .ship-card:hover { transform: translateY(-2px); box-shadow: var(--gg-shadow); border-color: #CFE0FF; }
    .ship-card.inactive { opacity: 0.6; background: #F8FAFC; }

    .ship-label { font-size: 0.75rem; color: #64748B; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 4px; }
    .ship-value { font-size: 0.95rem; font-weight: 700; color: var(--gg-ink); }

    .btn-outline-orange { color: #F59E0B; border: 1px solid #F59E0B; background: transparent; }
    .btn-outline-orange:hover { background: #FFF7E6; color: #D97706; border-color: #D97706; }
    .btn-outline-danger { color: #DC3545; border-color: #DC3545; }
    .btn-outline-danger:hover { background: #FFF5F5; color: #B02A37; }
  </style>
</head>
<body>
  
  <div class="container-fluid p-0 d-flex" style="min-height: 100vh; overflow-x: hidden;">
    
    <aside class="admin-side" id="adminSidebar">
        <a href="dashboard.html" class="brand-link" aria-label="GigaGears">
          <img src="{{asset('assets/logo GigaGears.png')}}" alt="GigaGears" class="brand-logo">
        </a>

        <nav class="nav flex-column nav-admin">
          <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a>
          <a class="nav-link" href="{{ route('admin.customers.index') }}"><i class="bi bi-people"></i>Data Customer</a>
          <a class="nav-link" href="{{ route('admin.sellers.index') }}"><i class="bi bi-person-badge"></i>Data Seller</a>
          <a class="nav-link" href="{{ route('admin.transactions.index') }}"><i class="bi bi-receipt"></i>Data Transaction</a>
          <a class="nav-link" href="{{ route('admin.products.index') }}"><i class="bi bi-box"></i>Products</a>
          <a class="nav-link active" href="{{ route('admin.shipping.index') }}"><i class="bi bi-truck"></i>Shipping Settings</a>
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
                <div class="small opacity-75 mb-1">Manage available couriers and their delivery services</div>
                <h1 class="h3 mb-0">Shipping Management</h1>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-light btn-sm"><i class="bi bi-bell"></i></button>
            <span class="badge-chip d-inline-flex align-items-center gap-2">
              <img src="https://ui-avatars.com/api/?name=Admin&background=random" class="rounded-circle" width="24" height="24">
              Admin
            </span>
          </div>
        </div>

        <div class="row g-0 mb-2">
              <div class="col-md-3 ms-auto">
                  <button class="btn btn-primary justify-content-end w-100" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Shipping
                  </button>
              </div>
        </div>


        <div id="shippingListContainer">
          @foreach($shippings as $item)
           <div class="ship-card ${item.status === 'Inactive' ? 'inactive' : ''}">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h4 class="h5 fw-bold mb-0">{{ $item->name }}</h4>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-orange px-3"onclick="openEditModal(@json([
                            'id' => $item->id,
                            'name' => $item->name,
                            'service_type' => $item->service_type,
                            'custom_service' => $item->custom_service,
                            'base_rate' => (float)$item->base_rate,
                            'per_kg' => (float)$item->per_kg,
                            'min_delivery_days' => (int)$item->min_delivery_days,
                            'max_delivery_days' => (int)$item->max_delivery_days,
                            'coverage' => $item->coverage,
                            'status' => $item->status,
                                ]) )"
                                data-bs-toggle="modal" data-bs-target="#editModal">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </button>
                        @if($item->status === 'Active')
                            <button class="btn btn-sm btn-outline-danger px-3" onclick="toggleStatus({{ $item->id }})">
                                <i class="bi bi-toggle-on me-1"></i> Deactive
                            </button>
                        @else
                            <button class="btn btn-sm btn-success px-3" onclick="toggleStatus({{ $item->id }})">
                                <i class="bi bi-toggle-off me-1"></i> Activate
                            </button>
                        @endif
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-6 col-md-2"><div class="ship-label">Courier</div>
                      <div class="ship-value">{{ $item->name }}</div>
                    </div>
                    <div class="col-6 col-md-2">
                      <div class="ship-label">Service</div>
                        <div class="ship-value">{{ $item->service_type === 'custom' ? $item->custom_service : $item->service_type }}</div>
                      </div>
                    <div class="col-6 col-md-3">
                      <div class="ship-label">Base Rate</div>
                        <div class="ship-value">Rp{{ number_format($item->base_rate, 0, ',', '.') }} 
                          <span class="text-muted small fw-normal">({{ 'Rp'.number_format($item->per_kg,0,',','.') }} /kg)</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                      <div class="ship-label">Estimated</div>
                        <div class="ship-value">
                            @if($item->min_delivery_days && $item->max_delivery_days)
                                @if($item->min_delivery_days === $item->max_delivery_days)
                                    {{ $item->min_delivery_days }} day{{ $item->min_delivery_days > 1 ? 's' : '' }}
                                @else
                                    {{ $item->min_delivery_days }}–{{ $item->max_delivery_days }} days
                                @endif
                            @else
                                -
                            @endif
                        </div>
                      </div>
                    <div class="col-6 col-md-2">
                      <div class="ship-label">Coverage</div>
                        <div class="ship-value">{{ $item->coverage }}</div>
                    </div>
                    <div class="col-6 col-md-1 text-md-end"><div class="ship-label">Status</div>
                      <div class="ship-value ${item.status === 'Active' ? 'text-success' : 'text-danger'}">{{ $item->status }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <nav><ul class="pagination pagination-sm mb-0" id="pager">
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
            </ul></nav>
        </div>

        <p class="text-center mt-5 foot small mb-0">© 2025 GigaGears. All rights reserved.</p>
    </main>
  </div>


  <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Add Shipping</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="addShippingForm">
          <div class="modal-body">
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Courier Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. JNE Express" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Service Type</label>
                <select name="service_type" id="" class="form-select" onchange="toggleCustomServiceAdd(this)">
                  <option value="" selected disabled>Select service</option>
                  <option value="REG">REG (Regular)</option>
                  <option value="ECO">Economy</option>
                  <option value="SDS">Same Day Service</option>
                  <option value="Cargo">Cargo</option>
                  <option value="custom">Other (Custom)</option>
                </select>
                <input type="text" name="custom_service" id="customAdd" class="form-control mt-2 d-none" placeholder="Enter custom service...">
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Base Rate</label>
                <div class="input-group  gap-1">
                  <span class="input-group-text bg-light">Rp.</span>
                  <input type="number" step="0.01" name="base_rate" class="form-control" required>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Rate per Kg</label>
                <div class="input-group gap-1">
                  <span class="input-group-text bg-light">Rp.</span>
                  <input type="number" step="0.01" name="per_kg" class="form-control" required>
                </div>
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Min Delivery Days</label>
                <input type="number" name="min_delivery_days" class="form-control" placeholder="e.g. 2" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Max Delivery Days</label>
                <input type="number" name="max_delivery_days" class="form-control" placeholder="e.g. 5" required>
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Coverage</label>
                <select name="coverage" class="form-select" disabled>
                  <option selected value="Domestic (Indonesia)">Domestic (Indonesia)</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary px-4">Save Shipping</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Script to handle add form submission via AJAX -->
  <script>
    document.getElementById('addShippingForm').addEventListener('submit', function(e){
        e.preventDefault();

        let formData = new FormData(this);

        fetch("{{ route('admin.shipping.store') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                alert(data.message);

                // Tutup modal
                let modal = bootstrap.Modal.getInstance(document.getElementById('addModal'));
                modal.hide();

                // Reset form
                document.getElementById('addShippingForm').reset();

                // Reload page atau update tabel secara AJAX
                location.reload();
            }
        })
        .catch(err => console.error(err));
    });
  </script>


  <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Edit Shipping</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="editShippingForm">
          <div class="modal-body">
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Courier Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. JNE Express" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Service Type</label>
                <select name="service_type" class="form-select" onchange="toggleCustomServiceUpdate(this)">
                  <option value="" selected disabled>Select service</option>
                  <option value="REG">REG (Regular)</option>
                  <option value="ECO">Economy</option>
                  <option value="SDS">Same Day Service</option>
                  <option value="Cargo">Cargo</option>
                  <option value="custom">Other (Custom)</option>
                </select>
                <input type="text" name="custom_service" id="customEdit" class="form-control mt-2 d-none" placeholder="Enter custom service...">
              </div>
            </div>
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Base Rate</label>
                <div class="input-group  gap-1">
                  <span class="input-group-text bg-light">Rp.</span>
                  <input type="number" step="0.01" name="base_rate" class="form-control" required>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Rate per Kg</label>
                <div class="input-group gap-1">
                  <span class="input-group-text bg-light">Rp.</span>
                  <input type="number" step="0.01" name="per_kg" class="form-control" required>
                </div>
              </div>
            </div>
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Min Delivery Days</label>
                <input type="number" name="min_delivery_days1" class="form-control" placeholder="e.g. 2" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Max Delivery Days</label>
                <input type="number" name="max_delivery_days1" class="form-control" placeholder="e.g. 5" required>
              </div>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!--- Script to handle edit form submission via AJAX -->
  <script>
    document.getElementById('editShippingForm').addEventListener('submit', function(e){
        e.preventDefault();

        const id = this.getAttribute('data-id');
        const formData = new FormData(this);

        // Karena name berbeda dari DB, ubah key agar sesuai:
        formData.set('min_delivery_days', formData.get('min_delivery_days1'));
        formData.set('max_delivery_days', formData.get('max_delivery_days1'));
        formData.delete('min_delivery_days1');
        formData.delete('max_delivery_days1');

        fetch(`/admin/shipping/${id}`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "X-HTTP-Method-Override": "PUT"
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);

                let modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                modal.hide();

                this.reset();
                location.reload();
            }
        })
        .catch(err => console.error(err));
    });
  </script>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>

    function toggleCustomServiceAdd(select) {
      const input = document.getElementById('customAdd');
      if(select.value === 'custom') {
          input.classList.remove('d-none');
          input.required = true;
      } else {
          input.classList.add('d-none');
          input.required = false;
          input.value = '';
      }
    }

    function toggleCustomServiceUpdate(select) {
      const customInput = document.getElementById('customEdit');
      if (select.value === 'custom') {
          customInput.classList.remove('d-none');
          customInput.required = true;
      } else {
          customInput.classList.add('d-none');
          customInput.required = false;
          customInput.value = "";
      }
    }

    function toggleCustomServiceUpdate(select) {
      const input = document.getElementById('customEdit');
      input.classList.toggle('d-none', select.value !== 'custom');
    }

    const btnToggle = document.getElementById('btnToggle');
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function toggleSidebar() {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    }

    if(btnToggle) btnToggle.addEventListener('click', toggleSidebar);
    if(overlay) overlay.addEventListener('click', toggleSidebar);



    function openEditModal(data) {

        // Ambil elemen form
        const form = document.getElementById('editShippingForm');

        // Set action URL
        form.setAttribute('data-id', data.id);

        // Isi field
        form.name.value = data.name;
        form.service_type.value = data.service_type;

        if (data.service_type === 'custom') {
            document.getElementById('customEdit').classList.remove('d-none');
            form.custom_service.value = data.custom_service;
        } else {
            document.getElementById('customEdit').classList.add('d-none');
            form.custom_service.value = '';
        }

        form.base_rate.value = data.base_rate;
        form.per_kg.value = data.per_kg;
        form.min_delivery_days1.value = data.min_delivery_days;
        form.max_delivery_days1.value = data.max_delivery_days;

        const modal = new bootstrap.Modal(document.getElementById('editModal'));
        modal.show();
    }


  </script>


</body>
</html>