


<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears — Store Setup</title>

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
      color:#0b2258;
    }
    .fw-black{font-weight:900!important}
    .text-muted-gg{color:var(--gg-muted)!important}

    .appbar{background:rgba(255,255,255,.75);backdrop-filter:blur(8px);
      border:1px solid rgba(91,140,255,.08);border-radius:16px;box-shadow:0 10px 26px rgba(20,30,105,.08)}
    .gg-card{background:#fff;border:1px solid rgba(91,140,255,.08);border-radius:var(--gg-radius);box-shadow:0 10px 26px rgba(24,42,117,.08)}
    .gg-soft{background:linear-gradient(180deg,#f6f9ff 0%, #ffffff 100%);border:1px solid rgba(91,140,255,.06);border-radius:var(--gg-radius)}

    .form-control,.form-select{background:#f6f8fd;border:1px solid #e8ecf4;border-radius:12px;font-weight:500}
    .form-control:focus,.form-select:focus{background:#fff;border-color:var(--gg-primary);box-shadow:0 0 0 .25rem rgba(91,140,255,.15)}

    .logo-box{
      width:96px;height:96px;border-radius:16px;background:#f7f9ff;border:1px dashed rgba(91,140,255,.35);
      display:flex;align-items:center;justify-content:center;overflow:hidden
    }
    .logo-box img{width:100%;height:100%;object-fit:cover}
    .banner-box{
      width:100%;height:140px;border-radius:16px;background:#f7f9ff;border:1px dashed rgba(91,140,255,.35);
      display:flex;align-items:center;justify-content:center;overflow:hidden
    }
    .banner-box img{width:100%;height:100%;object-fit:cover}

    .step{
      display:flex;align-items:center;gap:.6rem;background:#eef4ff;border:1px solid rgba(91,140,255,.2);
      padding:.4rem .7rem;border-radius:999px;font-weight:700;color:#0e2c73
    }
    .foot{color:#93a0c0}
  </style>
</head>
<body>
  <div class="container py-3 py-lg-4">

    <!-- APPBAR (brand minimal) -->
    <div class="appbar px-3 py-2 d-flex align-items-center justify-content-between mb-3">
      <div class="d-flex align-items-center gap-2">
        <span class="fw-black">GIGAGEARS</span>
        <span class="badge text-bg-light border">Seller Onboarding</span>
      </div>
      <a href="signin.html" class="btn btn-outline-secondary btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Sign Out</a>
    </div>

    <!-- TITLE -->
    <section class="gg-card p-4 mb-4">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
          <h1 class="fw-black mb-1">Set Up Your Store</h1>
          <p class="text-muted-gg mb-0">Lengkapi informasi toko agar siap berjualan di GigaGears.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
          <span class="step"><i class="bi bi-1-circle"></i> Details</span>
          <span class="step"><i class="bi bi-2-circle"></i> Location</span>
          <span class="step"><i class="bi bi-3-circle"></i> Payout (Opsional)</span>
          <span class="step"><i class="bi bi-4-circle"></i> Additional</span>
        </div>
      </div>
    </section>

    <!-- CONTENT GRID -->
    <form class="row g-3" id="storeSetupForm" autocomplete="off">
      <!-- LEFT COLUMN -->
      <div class="col-12 col-xl-8">

        <!-- A. STORE DETAILS -->
        <form action="POST" action="{{ route('seller.store')  }}" enctype="multipart/form-data">
        @csrf
        <section class="gg-card p-3 p-md-4 mb-3">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="mb-0">A. Store Details</h5>
            <span class="text-muted-gg small">Logo & banner opsional</span>
          </div>
          <div class="row g-3 align-items-start">
            <div class="col-auto">
              <div class="logo-box" id="logoBox"><i class="bi bi-image fs-3 text-muted"></i></div>
            </div>
            <div class="col">
              <div class="d-flex flex-wrap gap-2">
                <label class="btn btn-outline-primary mb-0">
                  <i class="bi bi-upload me-1"></i>Upload Logo
                  <input id="logo" type="file" class="d-none" accept="image/*">
                </label>
                <button class="btn btn-outline-secondary" type="button" id="btnLogoClear"><i class="bi bi-x-circle"></i></button>
              </div>
              <div class="form-text">PNG/JPG, maks 1MB, rasio 1:1 disarankan.</div>
            </div>

            <div class="col-12">
              <div class="banner-box" id="bannerBox"><i class="bi bi-image-alt fs-3 text-muted"></i></div>
              <div class="d-flex flex-wrap gap-2 mt-2">
                <label class="btn btn-outline-primary mb-0">
                  <i class="bi bi-upload me-1"></i>Upload Banner
                  <input id="banner" type="file" class="d-none" accept="image/*">
                </label>
                <button class="btn btn-outline-secondary" type="button" id="btnBannerClear"><i class="bi bi-x-circle"></i></button>
              </div>
              <div class="form-text">Rasio lebar:tinggi 5:1 disarankan.</div>
            </div>

            <div class="col-12">
              <label class="form-label">Store Name <span class="text-danger">*</span></label>
              <input name="store_name" type="text" id="store_name" class="form-control"  placeholder="Masukkan nama toko.." value="{{ old('store_name') }}"required>
                @error('store_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-12">
              <label class="form-label">Store Description</label>
              <textarea name="store_description" rows="3" class="form-control" placeholder="Deskripsi singkat toko…">{{ old('store_description') }}</textarea>
            </div>
          </div>
        </section>

        <!-- B. LOCATION -->
        <section class="gg-card p-3 p-md-4 mb-3">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="mb-0">B. Store Location (Jika ada toko offline)</h5>
            <span class="text-muted-gg small">Opsional</span>
          </div>

          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Province</label>
              <select name="province" class="form-select">
                <option value="">Select province</option>
                <option>DKI Jakarta</option>
                <option>Jawa Barat</option>
                <option>Jawa Tengah</option>
                <option>Jawa Timur</option>
              </select>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">City/District</label>
              <select name="city" class="form-select">
                <option value="">Select city/district</option>
                <option>Jakarta Selatan</option>
                <option>Bandung</option>
                <option>Semarang</option>
                <option>Surabaya</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Full Address</label>
              <input name="address" type="text" class="form-control" placeholder="Street, number, RT/RW, postal code" value="{{ old('store-address') }}">
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Contact Number</label>
              <input name="phone" type="tel" class="form-control" placeholder="08xxxxxxxxxx" inputmode="tel" value>
            </div>
          </div>
        </section>

        <!-- D. ADDITIONAL INFO -->
        <section class="gg-card p-3 p-md-4 mb-3">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="mb-0">D. Additional Info</h5>
            <span class="text-muted-gg small">Opsional</span>
          </div>

          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Business Category</label>
              <select name="biz_cat" class="form-select">
                <option value="">Select category</option>
                <option>Hardware</option>
                <option>Software</option>
                <option>Services</option>
              </select>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Operating Hours</label>
              <input name="hours" type="text" class="form-control" placeholder="Contoh: Mon–Fri 09:00–18:00">
            </div>
          </div>
        </section>
      </div>

      <!-- RIGHT COLUMN -->
      <div class="col-12 col-xl-4">
        <!-- C. PAYOUT (OPSIONAL) -->
    
        <!-- SUMMARY / ACTION -->
        <section class="gg-soft p-3 p-md-4">
          <h5 class="mb-3">Finish</h5>
          <ul class="list-unstyled small text-muted-gg mb-3">
            <li>• Lengkapi nama toko (wajib)</li>
            <li>• Lokasi & kontak opsional</li>
            <li>• Payout bisa diatur nanti</li>
          </ul>
          <div class="d-grid gap-2">
            <button class="btn btn-primary" type="submit">
              <i class="bi bi-check2-circle me-1"></i> Save & Continue
            </button>
            <button class="btn btn-outline-secondary" type="button" onclick="window.location.href='dashboard.html'">
              Skip for now
            </button>
        </form>
          </div>
          <div class="form-text mt-2">
            Ini prototipe frontend. Backend akan menyimpan data & menandai setup selesai.
          </div>
        </section>
      </div>
    </form>

    <p class="text-center mt-4 foot small">© 2025 GigaGears. All rights reserved.</p>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // ====== Preview Logo & Banner ======
    const logo = document.getElementById('logo');
    const logoBox = document.getElementById('logoBox');
    const btnLogoClear = document.getElementById('btnLogoClear');
    logo?.addEventListener('change', ()=>{
      const f = logo.files?.[0];
      logoBox.innerHTML = f ? `<img alt="Logo" src="${URL.createObjectURL(f)}">`
                            : `<i class="bi bi-image fs-3 text-muted"></i>`;
    });
    btnLogoClear?.addEventListener('click', ()=>{
      logo.value = '';
      logoBox.innerHTML = `<i class="bi bi-image fs-3 text-muted"></i>`;
    });

    const banner = document.getElementById('banner');
    const bannerBox = document.getElementById('bannerBox');
    const btnBannerClear = document.getElementById('btnBannerClear');
    banner?.addEventListener('change', ()=>{
      const f = banner.files?.[0];
      bannerBox.innerHTML = f ? `<img alt="Banner" src="${URL.createObjectURL(f)}">`
                              : `<i class="bi bi-image-alt fs-3 text-muted"></i>`;
    });
    btnBannerClear?.addEventListener('click', ()=>{
      banner.value = '';
      bannerBox.innerHTML = `<i class="bi bi-image-alt fs-3 text-muted"></i>`;
    });

    // ====== Submit demo (frontend only) ======
    document.getElementById('storeSetupForm')?.addEventListener('submit', (e)=>{
      e.preventDefault();
      // Validasi minimum: store name harus terisi
      const storeName = e.target.elements['store_name']?.value?.trim();
      if(!storeName){
        alert('Store Name wajib diisi.');
        return;
      }
      // Demo: anggap tersimpan & arahkan ke dashboard
      window.location.href = 'dashboard.html';
    });


  </script>
</body>
</html>