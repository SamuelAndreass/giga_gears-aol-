<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears — Settings</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- App CSS (punyamu) -->
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <div class="container-fluid">
    <div class="row g-0">

      <!-- ===== SIDEBAR (konsisten) ===== -->
      <aside class="col-12 col-lg-2 side d-none d-lg-block">
        <a href="dashboard.html" class="brand-link" aria-label="GigaGears">
          <img src="../assets/gigagears-logo.png" alt="GigaGears" class="brand-logo">
        </a>

        <nav class="nav flex-column nav-gg">
          <a class="nav-link" href="dashboard.html"><i class="bi bi-grid-1x2"></i>Dashboard</a>
          <a class="nav-link" href="order.html"><i class="bi bi-bag"></i>Order</a>
          <a class="nav-link" href="product.html"><i class="bi bi-box"></i>Products</a>
          <a class="nav-link" href="balance.html"><i class="bi bi-wallet2"></i>Balance & Withdraw</a>
          <a class="nav-link" href="analytics.html"><i class="bi bi-bar-chart"></i>Analytics & Report</a>
          <a class="nav-link" href="inbox.html"><i class="bi bi-inbox"></i>Inbox</a>
          <hr>
          <a class="nav-link active" href="settings.html"><i class="bi bi-gear"></i>Settings</a>
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
            <div class="small opacity-75 mb-1">Account & Store</div>
            <h1 class="wc-title mb-0">Settings</h1>
          </div>
          <div class="d-flex gap-2">
            <span class="badge-chip d-inline-flex align-items-center gap-2">
              <span class="rounded-circle bg-white text-primary fw-bold d-inline-flex align-items-center justify-content-center" style="width:28px;height:28px">TW</span> TechnoWorld
            </span>
          </div>
        </div>

        <!-- Content -->
        <div class="row g-3">

          <!-- ========= KIRI ========= -->
          <div class="col-12 col-xl-12">
            <!-- Profil Toko -->
            <section class="gg-card p-3 p-md-4 mb-3">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0">Profile</h5>
                <button class="btn btn-sm btn-primary" id="btnSaveProfile">
                  <i class="bi bi-save me-1"></i>Save
                </button>
              </div>

              <div class="row g-3 align-items-center">
                <div class="col-auto">
                  <div class="position-relative">
                    <img id="avatarPreview" src="https://i.pravatar.cc/96" class="rounded-circle border" alt="avatar" style="width:80px;height:80px;object-fit:cover">
                    <button class="btn btn-sm btn-light position-absolute bottom-0 end-0 rounded-circle border" id="btnAvatarPick" title="Change avatar">
                      <i class="bi bi-camera-fill"></i>
                    </button>
                    <input id="avatarInput" type="file" accept="image/*" class="d-none">
                  </div>
                </div>
                <div class="col">
                  <div class="row g-2">
                    <div class="col-12 col-md-6">
                      <label class="form-label mb-1">Store Name</label>
                      <input id="storeName" type="text" class="form-control" value="TechnoWorld">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label mb-1">Owner Name</label>
                      <input id="ownerName" type="text" class="form-control" value="John Doe">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label mb-1">Email</label>
                      <input id="email" type="email" class="form-control" value="owner@technoworld.com">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label mb-1">Phone</label>
                      <input id="phone" type="text" class="form-control" value="+62 812-3456-7890">
                    </div>
                    <div class="col-12">
                      <label class="form-label mb-1">Store Address</label>
                      <textarea id="address" rows="2" class="form-control">Jl. Mawar No. 1, Jakarta</textarea>
                    </div>
                  </div>
                </div>
              </div>
            </section>

            <!-- Preferensi Toko -->
            <section class="gg-card p-3 p-md-4 mb-3">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0">Store Preferences</h5>
                <button class="btn btn-sm btn-primary" id="btnSavePref"><i class="bi bi-save me-1"></i>Save</button>
              </div>

              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <label class="form-label mb-1">Currency</label>
                  <select id="currency" class="form-select">
                    <option value="USD">$ - USD</option>
                    <option value="IDR" selected>Rp - IDR</option>
                    <option value="EUR">€ - EUR</option>
                  </select>
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label mb-1">Timezone</label>
                  <select id="timezone" class="form-select">
                    <option>Asia/Jakarta (WIB)</option>
                    <option>Asia/Makassar (WITA)</option>
                    <option>Asia/Jayapura (WIT)</option>
                  </select>
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label mb-1">Default Shipping</label>
                  <select id="defaultShip" class="form-select">
                    <option selected>JNE</option>
                    <option>J&T</option>
                    <option>SiCepat</option>
                    <option>Pos Indonesia</option>
                  </select>
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label mb-1">Product Display</label>
                  <select id="productDisplay" class="form-select">
                    <option value="grid" selected>Grid</option>
                    <option value="list">List</option>
                  </select>
                </div>
                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="autoAccept">
                    <label class="form-check-label" for="autoAccept">
                      Auto-accept paid orders
                    </label>
                  </div>
                </div>
              </div>
            </section>

          
        <div class="row g-3 align-items-start">
          <!-- ========= KANAN ========= -->
          <div class="col-lg-8 col-md-7 col-12">
            <!-- Keamanan (tanpa 2FA) -->
            <section class="gg-card p-3 p-md-4 mb-3">
              <h5 class="mb-3">Security</h5>
              <div class="mb-2">
                <label class="form-label mb-1">Change Password</label>
                <div class="row g-2">
                  <div class="col-12">
                    <input id="pwCurrent" type="password" class="form-control" placeholder="Current password">
                  </div>
                  <div class="col-12 col-md-6">
                    <input id="pwNew" type="password" class="form-control" placeholder="New password">
                  </div>
                  <div class="col-12 col-md-6">
                    <input id="pwConfirm" type="password" class="form-control" placeholder="Confirm new password">
                  </div>
                </div>
                <div class="form-text" id="pwHint">Min. 8 karakter, kombinasi huruf & angka.</div>
                <div class="mt-2">
                  <button class="btn btn-sm btn-primary" id="btnChangePw"><i class="bi bi-shield-lock me-1"></i>Update Password</button>
                </div>
              </div>
            </section>
            </div>
          <div class="col-lg-4 col-md-5 col-12">
            <!-- Danger Zone -->
              <section class="gg-card p-3 p-md-4">
                <h5 class="mb-3 text-danger">Danger Zone</h5>
                <div class="d-flex flex-column gap-2">
                  <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalClearData">
                    <i class="bi bi-trash3 me-1"></i> Clear test data
                  </button>
                  <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteStore">
                    <i class="bi bi-exclamation-octagon me-1"></i> Delete store
                  </button>
                </div>
              </section>
          </div>
          </div>
        </div>
        <p class="text-center mt-4 foot small mb-0">© 2025 GigaGears. All rights reserved.</p>
      </main>
    </div>
  </div>

  <!-- Modals Danger -->
  <div class="modal fade" id="modalClearData" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Clear test data</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          Menghapus pesanan & produk dummy di lingkungan pengujian. Tindakan ini tidak bisa dibatalkan.
        </div>
        <div class="modal-footer">
          <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-danger" data-bs-dismiss="modal" id="btnClearOk">Clear</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalDeleteStore" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title text-danger">Delete store</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          Ketik <b>DELETE</b> untuk konfirmasi penghapusan permanen toko ini beserta semua datanya.
          <input id="confirmDelete" type="text" class="form-control mt-2" placeholder="DELETE">
        </div>
        <div class="modal-footer">
          <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-danger" id="btnDeleteStore" disabled>Delete</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast -->
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index:1080">
    <div id="saveToast" class="toast align-items-center text-bg-success border-0" role="alert">
      <div class="d-flex">
        <div class="toast-body"><i class="bi bi-check2-circle me-2"></i>Tersimpan!</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Page JS -->
  <script>
    const toast = new bootstrap.Toast(document.getElementById('saveToast'));
    const showSaved = () => toast.show();

    // Avatar preview
    document.getElementById('btnAvatarPick').addEventListener('click', ()=> {
      document.getElementById('avatarInput').click();
    });
    document.getElementById('avatarInput').addEventListener('change', (e)=> {
      const f = e.target.files?.[0];
      if(!f) return;
      const url = URL.createObjectURL(f);
      document.getElementById('avatarPreview').src = url;
    });

    // Save buttons (dummy)
    ['btnSaveProfile','btnSavePref','btnSaveNotif'].forEach(id=>{
      document.getElementById(id).addEventListener('click', showSaved);
    });

    // Password only (tanpa 2FA)
    document.getElementById('btnChangePw').addEventListener('click', ()=>{
      const cur = document.getElementById('pwCurrent').value.trim();
      const nw  = document.getElementById('pwNew').value.trim();
      const cf  = document.getElementById('pwConfirm').value.trim();
      if(!cur || !nw || !cf) return alert('Isi semua kolom password.');
      if(nw.length < 8) return alert('Password baru minimal 8 karakter.');
      if(nw !== cf) return alert('Konfirmasi password tidak cocok.');
      showSaved();
      document.getElementById('pwCurrent').value = '';
      document.getElementById('pwNew').value = '';
      document.getElementById('pwConfirm').value = '';
    });

    // Danger zone actions
    document.getElementById('btnClearOk').addEventListener('click', showSaved);

    const delInput = document.getElementById('confirmDelete');
    const delBtn   = document.getElementById('btnDeleteStore');
    delInput.addEventListener('input', ()=>{
      delBtn.disabled = (delInput.value.trim() !== 'DELETE');
    });
    delBtn.addEventListener('click', ()=>{
      showSaved();
    });
  </script>
</body>
</html>
