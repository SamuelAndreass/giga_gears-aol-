<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears — Set Up Your Store</title>

  <!-- Bootstrap & Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- Google Font (Chakra Petch) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root{
    --auth-radial: rgba(91,140,255,.18);
    --auth-top:   #eef5ff;
    --auth-mid:   #ffffff;
    --auth-bot:   #eaf3ff;
  }
  body.bg-auth{
    background:
      radial-gradient(1100px 500px at 50% -120px, var(--auth-radial), transparent 60%),
      linear-gradient(180deg, var(--auth-top) 0%, var(--auth-mid) 46%, var(--auth-bot) 100%);
  }
    :root{
      --gg-primary:#5b8cff;
      --gg-muted:#6b7280;
      --gg-radius:16px;
      --gg-dark:#1f2b57;
    }

    /* === BG sama persis dengan halaman Sign Up === */
    body{
      font-family:"Chakra Petch", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      background:
        radial-gradient(1100px 520px at 72% 18%, rgba(91,140,255,.16), transparent 58%),
        linear-gradient(180deg, #eaf2ff 0%, #edf5ff 45%, #e6f0ff 100%);
      background-attachment: fixed, fixed;
      min-height:100vh;
    }

    /* Sheet memanjang (scroll di body) */
    .sheet{
      max-width: 1160px;
      background:#fff;
      border-radius:24px;
      box-shadow:0 16px 40px rgba(15,34,85,.12), 0 2px 0 rgba(15,34,85,.04) inset;
      margin:24px auto;
      padding:28px 20px;
    }
    @media (min-width: 768px){ .sheet{ padding:40px 36px; } }
    @media (min-width: 992px){ .sheet{ padding:48px; } }

    .brand-img{ display:inline-flex; align-items:center; gap:.5rem; text-decoration:none; }
    .brand-logo{ height:32px; width:auto; display:block; }
    @media (min-width:992px){ .brand-logo{ height:36px; } }

    .section-hint{ color:var(--gg-muted); }
    .legend{ font-weight:700; font-size:1rem; margin-bottom:.5rem; }
    .legend small{ color:var(--gg-muted); font-weight:600; }

    .form-control, .form-select{
      background:#f6f8fd;
      border:1px solid #e4e9f6;
      border-radius:12px;
      font-weight:500;
    }
    .form-control:focus, .form-select:focus{
      background:#fff;
      border-color:var(--gg-primary);
      box-shadow:0 0 0 .25rem rgba(91,140,255,.15);
    }
    .btn-dark-gg{ background:#1f2b57; border-color:#1f2b57; color:#fff; font-weight:800; border-radius:12px; }
    .btn-dark-gg:hover{ filter:brightness(.95); color:#fff; }

    .upload-row .btn{ border-radius:10px; }
  </style>
</head>
<body>
<body class="bg-auth">

  <div class="sheet">
    <!-- Brand -->
    <a href="#" class="brand-img" aria-label="GigaGears">
  <img
    src="../assets/gigagears-logo.png"
    alt="GigaGears"
    class="brand-logo"
    srcset="../assets/gigagears-logo.png 1x, ../assets/gigagears-logo@2x.png 2x">
</a>

    <!-- Title -->
    <div class="mb-3">
      <h1 class="mb-1" style="font-weight:800; letter-spacing:.2px;">Set Up Your Store</h1>
      <p class="section-hint mb-0">Complete your store information to start selling on GigaGears.</p>
    </div>

    <!-- Form (memanjang; scroll body) -->
    <form id="storeSetupForm" method="post" action="{{ route('become.seller') }}" enctype="multipart/form-data" class="mt-2">
      @csrf
      <div class="row g-4">
        <!-- A. Store Details -->
        <div class="col-12 col-lg-6">
          <div class="legend">A. Store Details</div>
          <div class="mb-3">
            <label class="form-label">Storename</label>
            <input type="text"  name="store_name" class="form-control @error('store_name') is-invalid @enderror" placeholder="e.g. TechnoWorld">
            @error('store_name') <div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Store Description</label>
            <input type="text" name="store_description" class="form-control @error('store_description') is-invalid @enderror" placeholder="Short description about your store">
          </div>

          <div class="row g-2 align-items-center upload-row mb-2">
            <div class="col">
              <label class="form-label mb-1">Store Logo</label>
              <input id="logoInput" name='logo' type="file" accept="image/*" class="form-control @error('logo') is-invalid @enderror">
              @error('logo') <div class="invalid-feedback">{{ $message }}</div>@enderror
              <div class="form-text">PNG/JPG up to 1MB. Square ratio recommended.</div>
            </div>
            <div class="col-auto pt-4">
              <label for="logoInput" class="btn btn-outline-primary">Upload</label>
            </div>
          </div>

          <div class="row g-2 align-items-center upload-row">
            <div class="col">
              <label class="form-label mb-1">Store Banner</label>
              <input id="bannerInput" name='banner' type="file" accept="image/*" class="form-control @error('banner') is-invalid @enderror">
              @error('banner') <div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              <div class="form-text">PNG/JPG up to 2MB. 16:9 ratio recommended.</div>
            </div>
            <div class="col-auto pt-4">
              <label for="bannerInput" class="btn btn-outline-primary">Upload</label>
            </div>
          </div>
        </div>

        <!-- B. Store Location -->
        <div class="col-12 col-lg-6">
          <div class="legend">B. Store Location <small>(If seller also has offline store)</small></div>
          <div class="mb-3">
            <label class="form-label">Province</label>
            <select name="province" class="form-select @error('province') is-invalid @enderror">
              <option selected disabled value="" {{ old ('province') ? '': 'selected'}}>Choose province</option>
              <option value="DKI Jakarta" {{ old('province') == 'DKI Jakarta' ? 'selected' : '' }}>DKI Jakarta</option>
              <option value="Jawa Barat" {{ old('province') == 'Jawa Barat' ? 'selected' : '' }}>Jawa Barat</option>
              <option value="Jawa Tengah" {{ old('province') == 'Jawa Tengah' ? 'selected' : '' }}>Jawa Tengah</option>
              <option value="Jawa Timur" {{ old('province') == 'Jawa Timur' ? 'selected' : '' }}>Jawa Timur</option>
              <option value="Banten" {{ old('province') == 'Banten' ? 'selected' : '' }}>Banten</option>
            </select>
            @error('province') <div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">City/District</label>
            <select class="form-select">
              <option selected disabled {{ old('city') ? '' : 'selected' }}>Choose city/district</option>
              <option {{ old('city') == 'Jakarta Selatan' ? 'selected' : '' }}>Jakarta Selatan</option>
              <option {{ old('city') == 'Bandung' ? 'selected' : '' }}>Bandung</option>
              <option {{ old('city') == 'Semarang' ? 'selected' : '' }}>Semarang</option>
              <option {{ old('city') == 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
              <option {{ old('city') == 'Tangerang' ? 'selected' : '' }}>Tangerang</option>
            </select>
            @error('city') <div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Full Address</label>
            <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" placeholder="Street, number, area, ZIP" value="{{ old('address') }}">
            @error('address') <div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Contact Number</label>
            <input type="tel" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" placeholder="08xxxxxxxxxx" value="{{ old('contact_number') }}">
            @error('contact_number') <div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <!-- C. Payment Info -->

        <!-- D. Additional Info -->
        <div class="col-12 col-lg-6">
          <div class="legend">D. Additional Info <small>(Optional)</small></div>
          <div class="mb-4">
            <label class="form-label">Operating Hours</label>
              <div class="d-flex gap-2">
                <div>
                    <label class="form-label small">Open</label>
                    <input type="time" name="open_time"
                        value="{{ old('open_time') }}"
                        class="form-control">
                </div>

                <div>
                    <label class="form-label small">Close</label>
                    <input type="time" name="close_time"
                        value="{{ old('close_time') }}"
                        class="form-control">
                </div>
              </div>
        
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-dark-gg btn-lg">Save & Continue</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
