<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears Admin — Shipping</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{asset('css/admin.css')}}">

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
   @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert" style="z-index: 1100;">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
  <div class="container-fluid p-0 d-flex" style="min-height: 100vh; overflow-x: hidden;">
    
    <aside class="admin-side" id="adminSidebar">
        <a href="dashboard.html" class="brand-link" aria-label="GigaGears">
          <img src="{{asset('images/logo GigaGears.png')}}" alt="GigaGears" class="brand-logo">
        </a>

        <nav class="nav flex-column nav-admin">
          <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a>
          <a class="nav-link" href="{{ route('admin.customers.index') }}"><i class="bi bi-people"></i>Data Customer</a>
          <a class="nav-link" href="{{ route('admin.sellers.index') }}"><i class="bi bi-person-badge"></i>Data Seller</a>
          <a class="nav-link" href="{{ route('admin.transactions.index') }}"><i class="bi bi-receipt"></i>Data Transaction</a>
          <a class="nav-link" href="{{ route('admin.products.index') }}"><i class="bi bi-box"></i>Products</a>
          <a class="nav-link active" href="{{ route('admin.shipping.index') }}"><i class="bi bi-truck"></i>Shipping Settings</a>
        </nav>

        <div class="mt-auto pb-4 px-3 pt-3 border-top">
          <a class="btn btn-logout w-100" href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right me-1"></i> Log Out</a>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
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
          @forelse($shippings as $item)
           <div class="ship-card ${item.status === 'Inactive' ? 'inactive' : ''}">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h4 class="h5 fw-bold mb-0">{{ $item->name }}</h4>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-orange px-3"
                          data-item='{{ base64_encode(json_encode([
                              "id" => $item->id,
                              "name" => $item->name,
                              "service_type" => $item->service_type,
                              "custom_service" => $item->custom_service,
                              "base_rate" => (float) $item->base_rate,
                              "per_kg" => (float) $item->per_kg,
                              "min_delivery_days" => (int) $item->min_delivery_days,
                              "max_delivery_days" => (int) $item->max_delivery_days,
                              "coverage" => $item->coverage,
                              "status" => $item->status,
                          ])) }}'
                          onclick="openEditModalFromButton(this)"
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
          @empty
            <div class="alert alert-info">
                No shipping methods found. Please add a shipping method.
            </div>
          @endforelse
        </div>

        <div class="d-flex justify-content-end mt-4">
            {{ $shippings->links() }}
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
                <select name="coverage" class="form-select" readonly>
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
      document.getElementById('addShippingForm').addEventListener('submit', async function(e){
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);

        try {
          const res = await fetch("{{ route('admin.shipping.store') }}", {
            method: "POST",
            headers: {
              "X-CSRF-TOKEN": "{{ csrf_token() }}",
              'Accept': 'application/json' // minta JSON back
              // NOTE: jangan set 'Content-Type' saat menggunakan FormData
            },
            credentials: 'same-origin', // kirim cookie session (important)
            body: formData
          });

          // jika server meng-redirect, tangkap dulu (debug)
          if (res.redirected || res.status === 302) {
            const loc = res.headers.get('Location') || '(no Location header)';
            const text = await res.text();
            throw new Error(`Unexpected redirect (status ${res.status}) to ${loc}. Response preview:\n\n${text.slice(0,400)}`);
          }

          const ct = res.headers.get('content-type') || '';
          if (!ct.includes('application/json')) {
            const text = await res.text();
            throw new Error('Expected JSON but server returned HTML/text: ' + text.slice(0,400));
          }

          const data = await res.json();

          if (data.success) {
            alert(data.message);
            const modal = bootstrap.Modal.getInstance(document.getElementById('addModal'));
            if (modal) modal.hide();
            form.reset();
            location.reload();
          } else if (data.errors) {
            // contoh menampilkan error validasi
            console.error('Validation errors:', data.errors);
            alert('Validation error: ' + JSON.stringify(data.errors));
          } else {
            console.log('Response:', data);
          }

        } catch (err) {
          console.error('Request failed:', err);
          alert('Request failed: ' + err.message);
        }
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

    /**
     * Robust JS for Edit Shipping Modal
     * - Decodes base64 data-item JSON on button and fills the form
     * - Handles "custom" service_type visibility
     * - Submits form via fetch with FormData and X-HTTP-Method-Override: PUT
     * - Expects JSON responses (422 for validation, success true on OK)
     */

    /* ===== Helpers ===== */
    function safeJsonParse(str) {
      try { return JSON.parse(str); } catch (e) { return null; }
    }

    function decodeDataItem(b64) {
      try {
        // Some browsers require padding; atob usually works as base64 from server
        const json = atob(b64);
        return safeJsonParse(json);
      } catch (err) {
        console.error('Failed to decode data-item base64:', err);
        return null;
      }
    }

    /* ===== Open modal from button =====
      Buttons have data-item="<base64(json)>"
      e.g. onclick="openEditModalFromButton(this)"
    */
    function openEditModalFromButton(btn) {
      if (!btn) return console.error('openEditModalFromButton: button is null');
      const b64 = btn.getAttribute('data-item') || btn.dataset.item;
      if (!b64) return console.error('openEditModalFromButton: no data-item attribute found');

      const data = decodeDataItem(b64);
      if (!data) return console.error('openEditModalFromButton: decoded data invalid');

      openEditModal(data);
    }

    /* ===== Toggle custom service input when select changes ===== */
    function toggleCustomServiceUpdate(selectEl) {
      const customInput = document.getElementById('customEdit');
      if (!selectEl) return;
      if (selectEl.value === 'custom') {
        customInput.classList.remove('d-none');
        customInput.required = true;
      } else {
        customInput.classList.add('d-none');
        customInput.required = false;
        customInput.value = ''; // clear stale value
      }
    }

    /* ===== Fill the edit form and show modal ===== */
    function openEditModal(data) {
      if (!data) return console.error('openEditModal: data required');

      const form = document.getElementById('editShippingForm');
      if (!form) return console.error('openEditModal: editShippingForm not found');

      // Attach data-id for submit to use
      form.setAttribute('data-id', data.id);

      // Helper to set form element safely
      const setField = (name, value) => {
        // prefer form.elements[name] (works for inputs/selects/textareas)
        let el = form.elements[name];
        if (!el) el = form.querySelector(`[name="${name}"]`);
        if (el) {
          // For select, ensure we pick existing option if possible
          if (el.tagName === 'SELECT') {
            const opt = Array.from(el.options).find(o => o.value === String(value));
            if (opt) el.value = value;
            else {
              // if value not found, and it's "custom", set to custom and populate custom input
              if (String(value) === 'custom') el.value = 'custom';
              else {
                // keep select as-is (or choose first non-disabled option)
                // but still set a hidden fallback if available
              }
            }
          } else {
            el.value = (value !== undefined && value !== null) ? value : '';
          }
        } else {
          // fallback: hidden input (useful when original select is disabled in markup)
          const hidden = form.querySelector(`input[type="hidden"][name="${name}"]`);
          if (hidden) hidden.value = (value !== undefined && value !== null) ? value : '';
        }
      };

      // Set values (names must match form element names)
      setField('name', data.name);
      setField('service_type', data.service_type);
      // handle custom service visibility and value
      const customInput = document.getElementById('customEdit');
      if (String(data.service_type) === 'custom') {
        if (customInput) {
          customInput.classList.remove('d-none');
          customInput.required = true;
          setField('custom_service', data.custom_service ?? '');
        }
      } else {
        if (customInput) {
          customInput.classList.add('d-none');
          customInput.required = false;
          setField('custom_service', ''); // clear
        }
      }

      setField('base_rate', data.base_rate);
      setField('per_kg', data.per_kg);
      setField('min_delivery_days1', data.min_delivery_days);
      setField('max_delivery_days1', data.max_delivery_days);

      // coverage — if you have hidden input named coverage, populate it
      const covHidden = form.querySelector('input[type="hidden"][name="coverage"]');
      if (covHidden && data.coverage !== undefined) covHidden.value = data.coverage;

      // show modal (safe: get existing Modal instance or create one)
      const modalEl = document.getElementById('editModal');
      const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
      modal.show();
    }

    /* ===== Submit handler =====
      Uses FormData, sets min/max keys, sends PUT via method override
    */
    (function attachEditFormSubmit() {
      const form = document.getElementById('editShippingForm');
      if (!form) return console.warn('attachEditFormSubmit: form not found');

      form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = this.getAttribute('data-id');
        if (!id) return alert('Missing shipping ID. Try re-opening the edit modal.');

        try {
          const formData = new FormData(this);

          // normalize field names to your API expectation
          if (formData.has('min_delivery_days1')) {
            formData.set('min_delivery_days', formData.get('min_delivery_days1'));
            formData.delete('min_delivery_days1');
          }
          if (formData.has('max_delivery_days1')) {
            formData.set('max_delivery_days', formData.get('max_delivery_days1'));
            formData.delete('max_delivery_days1');
          }

          // If coverage is disabled/select not sent, but you have hidden coverage input, ensure it's present
          // (usually handled by hidden input in markup)
          // e.g. form contains: <input type="hidden" name="coverage" value="Domestic (Indonesia)">
          // If not, you can set manually:
          // formData.set('coverage', 'Domestic (Indonesia)');

          // Add method override for Laravel (PUT)
          formData.set('_method', 'PUT');

          const res = await fetch(`/admin/shipping/${id}`, {
            method: 'POST', // send POST with _method=PUT
            headers: {
              'X-CSRF-TOKEN': "{{ csrf_token() }}", // blade will render token
              'Accept': 'application/json'
              // DO NOT set Content-Type when sending FormData
            },
            credentials: 'same-origin', // include cookies/session
            body: formData
          });

          // Handle redirect (if server incorrectly redirects)
          if (res.redirected || res.status === 302) {
            const location = res.headers.get('Location') || '(no Location header)';
            const text = await res.text();
            throw new Error(`Server redirected to ${location}. Response preview:\n${text.slice(0,300)}`);
          }

          // If Laravel validation fails it typically returns 422 with JSON
          if (res.status === 422) {
            const err = await res.json();
            console.warn('Validation errors:', err);
            const messages = (err.errors) ? Object.values(err.errors).flat().join('\n') : JSON.stringify(err);
            return alert('Validation failed:\n' + messages);
          }

          // Expect JSON success
          const contentType = res.headers.get('content-type') || '';
          if (!contentType.includes('application/json')) {
            const text = await res.text();
            throw new Error('Expected JSON response but got: ' + text.slice(0,400));
          }

          const data = await res.json();

          if (data.success) {
            alert(data.message || 'Saved');
            // hide modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
            if (modal) modal.hide();
            this.reset();
            // refresh or update UI
            location.reload();
          } else {
            // handle structured error from server
            console.warn('Server returned non-success:', data);
            const msg = data.message || JSON.stringify(data);
            alert('Error: ' + msg);
          }
        } catch (err) {
          console.error('Failed to submit edit form:', err);
          alert('Request failed: ' + (err.message || err));
        }
      }, { passive: false });
    })();

    /* ===== Optional: Initialize toggles on page load for any prefilled selects ===== */
    document.addEventListener('DOMContentLoaded', () => {
      // ensure custom input visibility matches initial select value
      const select = document.querySelector('#editShippingForm [name="service_type"]');
      if (select) toggleCustomServiceUpdate(select);
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



  </script>


</body>
</html>