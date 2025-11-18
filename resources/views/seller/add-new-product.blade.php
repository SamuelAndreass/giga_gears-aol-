<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears — Add New Product</title>

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
          <a class="nav-link" href="#"><i class="bi bi-grid-1x2"></i>Dashboard</a>
          <a class="nav-link" href="#"><i class="bi bi-bag"></i>Order</a>
          <a class="nav-link active" href="#"><i class="bi bi-box"></i>Products</a>
          <a class="nav-link" href="#"><i class="bi bi-wallet2"></i>Balance & Withdraw</a>
          <a class="nav-link" href="#"><i class="bi bi-bar-chart"></i>Analytics & Report</a>
          <a class="nav-link" href="#"><i class="bi bi-inbox"></i>Inbox</a>
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
            <div class="small opacity-75 mb-1">Products</div>
            <h1 class="wc-title mb-0">Add New Product</h1>
            <div class="small opacity-75">Fill in the details to list your product on GigaGears marketplace.</div>
          </div>
          <div class="d-flex gap-2">
            <span class="badge-chip d-inline-flex align-items-center gap-2">
              <i class="bi bi-bell"></i>
            </span>
            <span class="badge-chip d-inline-flex align-items-center gap-2">
              <span class="rounded-circle bg-white text-primary fw-bold d-inline-flex align-items-center justify-content-center" style="width:28px;height:28px">TW</span>
              @auth {{ Auth::user()->name }} @endauth
            </span>
          </div>
        </div>

        <!-- FORM WRAP -->
        <form id="productForm" method="post" action="{{ route('seller.add.product') }}" enctype="multipart/form-data" class="row g-3">
          <!-- A. Basic Information -->
          <section class="gg-card p-3 p-md-4">
            <h6 class="mb-3">A. Basic Information</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <input id="pName" class="form-control @error('product_name') is-invalid @enderror" type="text" name='product_name' placeholder="Product Name" required>
                @error('product_name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6">
                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                    <option value="">-- Select category --</option>
                    @foreach($categories as $cat)
                      <option value="{{ $cat->id }}"
                        {{ (int) old('category_id', optional($product ?? null)->category_id) === $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                      </option>
                    @endforeach
                </select>
                @error('category_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6">
                <input id="pBrand" class="form-control" type="text" placeholder="Brand">
              </div>
              <div class="col-md-6">
                <input id="pSku" class="form-control" type="text" placeholder="SKU / Product Code (optional)">
              </div>
              <div class="col-12">
                <textarea id="pDesc" class="form-control" rows="3" placeholder="Product Description"></textarea>
              </div>
            </div>
          </section>

          <!-- B. Pricing & Stock -->
          <section class="gg-card p-3 p-md-4">
            <h6 class="mb-3">B. Pricing & Stock</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <div class="input-group">
                  <span class="input-group-text">$</span>
                  <input id="pPrice" class="form-control" type="number" min="0" step="0.01" placeholder="Price" required>
                </div>
              <div class="col-md-6">
                <input id="pStock" class="form-control" type="number" min="0" step="1" placeholder="Stock Quantity">
              </div>
            </div>
          </section>

          <!-- C. Media Upload -->
          <section class="gg-card p-3 p-md-4">
            <h6 class="mb-3">C. Media Upload</h6>
            <div class="row g-3">
              <div class="col-md-8">
                <input id="pImages" class="form-control" type="file" accept="image/*" multiple>
                <div class="small text-muted mt-1">Upload Product Images (max 5)</div>
                <div id="imgPreview" class="d-flex gap-2 flex-wrap mt-2"></div>
              </div>
              <div class="col-md-4">
                <input id="pVideo" class="form-control" type="url" placeholder="Video URL (e.g. demo product)">
                <div class="small text-muted mt-1">Opsional: YouTube/drive link.</div>
              </div>
            </div>
          </section>

          <!-- D. Variants -->
          <section class="gg-card p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="mb-0">D. Product Variants (Optional)</h6>
              <button type="button" id="btnAddVariant" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Add Variant
              </button>
            </div>
            <div id="variantsWrap" class="vstack gap-2"></div>
            <div class="small text-muted mt-2">Contoh: Warna/Ukuran. Harga & stok bisa override.</div>
          </section>

          <!-- E. Shipping (Physical only) -->
          <section class="gg-card p-3 p-md-4">
            <h6 class="mb-3">E. Shipping (Only for Physical Products)</h6>
            <div class="row g-3">
              <div class="col-md-4">
                <input id="pWeight" class="form-control" type="number" min="0" step="1" placeholder="Weight (grams)">
              </div>
              <div class="col-md-8">
                <input id="pDims" class="form-control" type="text" placeholder="Dimensions (cm) — e.g. 20 x 12 x 8">
              </div>
            </div>
          </section>

          <!-- F. Shipping Option -->
          <section class="gg-card p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="mb-0">F. Shipping Option</h6>
              <button type="button" id="btnAddCourier" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Add Others
              </button>
            </div>
            <div id="couriersWrap" class="row g-2">
              <div class="col-md-4">
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-truck"></i></span>
                  <input class="form-control" type="text" value="JNE">
                  <span class="input-group-text"><input class="form-check-input mt-0" type="checkbox" checked></span>
                </div>
              </div>
            </div>
          </section>

          <!-- Actions -->
          <div class="d-flex flex-wrap gap-2">
            <button type="button" id="btnDraft" class="btn btn-outline-secondary">
              Save as Draft
            </button>
            <button type="submit" class="btn btn-primary">
              Publish Product
            </button>
          </div>
        </form>

        <p class="text-center mt-4 foot small mb-0">© 2025 GigaGears. All rights reserved.</p>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // image preview
      const imagesInput = document.getElementById('imagesInput');
      const previewBox = document.getElementById('previewImages');

      imagesInput?.addEventListener('change', (e) => {
        previewBox.innerHTML = '';
        const files = Array.from(e.target.files).slice(0, 8); // limit preview to first 8
        files.forEach(file => {
          const reader = new FileReader();
          reader.onload = (ev) => {
            const img = document.createElement('img');
            img.src = ev.target.result;
            img.style = "width:120px;height:120px;object-fit:cover;border-radius:6px;border:1px solid #ddd";
            previewBox.appendChild(img);
          };
          reader.readAsDataURL(file);
        });
      });

      // dynamic variants
      const variantsWrapper = document.getElementById('variantsWrapper');
      const addVariantBtn = document.getElementById('addVariantBtn');
      let variantIndex = variantsWrapper.querySelectorAll('.variant-row').length || 0;

      function makeVariantRow(i, data = {}) {
        const div = document.createElement('div');
        div.className = 'variant-row mb-2 p-2 border rounded';
        div.dataset.index = i;
        div.innerHTML = `
          <div class="d-flex gap-2 align-items-start">
            <input type="text" name="variants[${i}][name]" placeholder="Variant name (e.g. Color)"
                  class="form-control" value="${data.name ?? ''}" style="max-width:200px;">
            <input type="text" name="variants[${i}][value]" placeholder="Value (e.g. Red)"
                  class="form-control" value="${data.value ?? ''}">
            <input type="number" step="0.01" name="variants[${i}][price]" placeholder="Extra price"
                  class="form-control" value="${data.price ?? ''}" style="max-width:140px;">
            <input type="number" name="variants[${i}][stock]" placeholder="Stock"
                  class="form-control" value="${data.stock ?? ''}" style="max-width:120px;">
            <button type="button" class="btn btn-danger btn-sm remove-variant">Remove</button>
          </div>`;
        return div;
      }

      addVariantBtn?.addEventListener('click', () => {
        variantsWrapper.appendChild(makeVariantRow(variantIndex++));
      });

      // delegate remove
      variantsWrapper.addEventListener('click', (e) => {
        if (e.target.matches('.remove-variant')) {
          e.target.closest('.variant-row')?.remove();
        }
      });
    });
  </script>
</body>
</html>
