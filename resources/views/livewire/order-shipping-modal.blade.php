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