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
  <link rel="stylesheet" href="{{asset('css/styles.css')}}">
</head>
<body>
  @livewireStyles
  <div class="container-fluid">
    <div class="row g-0">
      <!-- ===== SIDEBAR (KONSISTEN) ===== -->
      <aside class="col-12 col-lg-2 side d-none d-lg-block">
        <a href="dashboard.html" class="brand-link" aria-label="GigaGears">
          <img src="{{ asset('images/logo GigaGears.png') }}" alt="GigaGears" class="brand-logo">
        </a>

        <nav class="nav flex-column nav-gg">
          <a class="nav-link" href="{{ route('seller.index') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a>
          <a class="nav-link active" href="{{ route('seller.orders') }}"><i class="bi bi-bag"></i>Order</a>
          <a class="nav-link" href="{{ route('seller.products') }}"><i class="bi bi-box"></i>Products</a>
          <a class="nav-link" href="{{ route('seller.analytics') }}"><i class="bi bi-bar-chart"></i>Analytics & Report</a>
          <a class="nav-link" href="{{ route('seller.inbox') }}"><i class="bi bi-inbox"></i>Inbox</a>
          <hr>
          <a class="nav-link" href="{{ route('settings.index')}}"><i class="bi bi-gear"></i>Settings</a>
        </nav>

        <div class="mt-4">
          <a class="btn btn-outline-danger w-100" href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right me-1"></i> Log Out</a>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
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
            
            <div class="col-6 col-md-5 col-lg-10">
              <form action="{{ route('seller.recent.order') }}" method="GET" class="col-12 col-md-4 col-lg-6">
                  <select id="orderStatus" class="form-select" name="status">
                    <option value="">Aplikasi (all)</option>
                     <option value="pending" @selected(request('status')=='pending')>Pending</option>
                    <option value="processing" @selected(request('status')=='processing')>Processing</option>
                    <option value="completed" @selected(request('status')=='completed')>Completed</option>
                    <option value="cancelled" @selected(request('status')=='cancelled')>Cancelled</option>
                    <option value="refunded" @selected(request('status')=='refunded')>Refunded</option>
                  </select>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
              <button type="submit" id="orderApply" class="btn btn-primary w-100">
                <i class="bi bi-funnel me-1"></i>Apply
              </button>
              </form>
            </div>
          </div>
        </div>

        <!-- Recent Orders table -->
        <section class="gg-card p-0 orders">
          <div class="d-flex align-items-center justify-content-between px-3 px-md-4 py-3 border-bottom">
            <h5 class="mb-0">Recent Order</h5>
          </div>

          <div class="table-responsive p-3">
            <livewire:order-shipping-modal />
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
            @if($orders)
              <tbody id="ordersBody">
                @php $i = 0; @endphp
                @forelse ($orders as $oi)                
                <tr>
                  <th scope='row'>#{{$oi->order->id}}</th>
                  <td>{{ $oi->product->name }}</td>
                  <td>{{ $oi->order->user->name }}</td>
                  <td>{{ $oi->order->status }}</td>
                  <td>{{ $oi->total_qty }}</td>
                  <td>{{ $oi->price }}</td>
                  <td>{{ $oi->order_date }}</td>    
                  <td><button 
                      class="btn btn-primary btn-sm"
                      onclick="Livewire.emit('openOrderShippingModal', {{ $order->id }})">
                      Update
                    </button>
                    </td>
                </tr>
                <tr>
                  @empty
                  <td colspan="8" class="text-center text-muted py-4">Belum ada order.</td>
                  @endforelse
                </tr>
              </tbody>
              <tfoot>
              @else
                <tr id="noResultsRow" style="display:none">
                  <td colspan="8" class="text-center text-muted py-4">Tidak ada hasil yang cocok.</td>
                </tr>
              @endif
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

 <div wire:ignore.self>
  <div class="modal fade" id="orderUpdateModal" tabindex="-1" aria-labelledby="orderUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-light">
          <h5 class="m-2">Update Order <small class="text-muted">#{{ $orderId ?? '' }}</small></h5>
          <button type="button" class="btn btn-icon btn-outline-secondary" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12 col-lg-8">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-12 col-md-6">
                      <label class="form-label">Status</label>
                      <select wire:model="status" class="form-select">
                        <option value="">--Pilih status--</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="refunded">Refunded</option>
                      </select>
                      @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                      <label class="form-label">Total pembayaran</label>
                      <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <div class="form-control bg-light fw-semibold">
                          {{ number_format(optional($order)->total ?? 0, 0, ',', '.') }}
                        </div>
                      </div>
                    </div>

                    <div class="col-12"><hr class="my-2"><div class="small text-muted">Shipping Information</div></div>

                    <div class="col-12 col-md-6">
                      <label class="form-label">Jasa Kirim</label>
                      <select wire:model="courier" class="form-select">
                        <option value="">--Pilih Kurir---</option>
                        @foreach($couriers as $c)
                          <option value="{{ $c['name'] }}">{{ $c['name'] }}</option>
                        @endforeach
                      </select>
                      @error('courier') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                      <label class="form-label">No. Resi</label>
                      <input wire:model.defer="tracking_number" class="form-control" />
                      @error('tracking_number') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                      <label class="form-label">Biaya kirim</label>
                      <input wire:model.defer="shipping_cost" type="number" class="form-control" />
                      @error('shipping_cost') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                      <label class="form-label">Tanggal kirim</label>
                      <input wire:model.defer="shipping_date" type="date" class="form-control" />
                      @error('shipping_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                      <label class="form-label">Estimasi Tiba</label>
                      <input wire:model.defer="eta_text" class="form-control" />
                      @error('eta_text') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                  </div>
                </div>
              </div>
            </div>

            <div class="col-12 col-lg-4">
              <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                  <div class="small text-muted mb-2">Quick actions</div>
                  <div class="d-grid gap-2">
                    <button wire:click="ship" type="button" class="btn btn-success">
                      <i class="bi bi-check-circle me-1"></i> Mark as Shipped
                    </button>
                    <button wire:click="cancelBySeller" type="button" class="btn btn-outline-danger" id="ou-cancel-order">
                      <i class="bi bi-x-octagon me-1"></i> Cancel Order
                    </button>
                    <button wire:click="save" type="button" class="btn btn-primary">Save changes</button>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <div class="modal-footer bg-light d-flex justify-content-between">
          <div class="text-muted small">
            Perubahan ini <b>belum</b> tersimpan hingga kamu menekan <b>Save changes</b>.
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>


<script>
  document.addEventListener('DOMContentLoaded', function () {
      const modalEl = document.getElementById('orderUpdateModal');
      const bsModal = new bootstrap.Modal(modalEl);

      window.addEventListener('open-order-update-modal', () => {
          bsModal.show();
      });

      window.addEventListener('close-order-update-modal', () => {
          bsModal.hide();
      });

      // optional: show simple toast from Livewire notify
      window.addEventListener('notify', (e) => {
          const { type, message } = e.detail || e;
          // gunakan alert sementara atau implement toast
          alert(message);
      });
  });
</script>
@livewireScripts
</body>
</html>


