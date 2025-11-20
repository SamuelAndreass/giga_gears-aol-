<?php

namespace App\Http\Livewire\Seller;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Shipping;
use Carbon\Carbon;

class ShipOrder extends Component
{
    public Order $order;

    // courier slug (jne/jnt/sicepat/store)
    public $courier;

    // list services for selected courier
    public $services = [];

    // selected service id
    public $selectedServiceId;

    // preview / editable tracking number
    public $tracking_number;

    // computed fields
    public $shipping_date;
    public $eta_start; // date string
    public $eta_end;   // date string
    public $eta_text;  // "2 - 4 hari" or "01-04-2025 — 03-04-2025"
    public $shipping_cost;
    public $notes;
    public $shippers = [];
    protected $rules = [
        'shipper' => 'required|string|max:50',
        'selectedServiceId' => 'required|integer',
        'tracking_number' => 'nullable|string|max:120',
        'notes' => 'nullable|string|max:1000',
    ];

    public function mount(Order $order)
    {
        $this->order = $order;
        $this->authorizeShipOrAbort();

        // load couriers dari DB
        $this->loadCouriers();

        // init if order has existing shipping values
        $this->courier = $order->courier;
        $this->tracking_number = $order->tracking_number;
        $this->shipping_date = $order->shipping_date?->toDateString();
        if ($order->estimated_arrival) {
            // if stored as single date, fill both
            $this->eta_start = $order->estimated_arrival->toDateString();
            $this->eta_end = $order->estimated_arrival->toDateString();
            $this->eta_text = $this->eta_start;
        }

        // load services if courier already set
        if ($this->courier) {
            $this->loadServices();
        }

        // compute cost if service preselected
        if ($this->selectedServiceId) {
            $this->computeEstimatesFromService();
        }
    }

    protected function loadCouriers(){
        // ambil (distinct) display name dari tabel shipping_services
        $rows = Shipping::active()
            ->select('name')
            ->groupBy('name')
            ->orderBy('name')
            ->get();

        // transform ke array yang mudah dipakai di blade
        $this->shippers = $rows->map(fn($r) => [
            'slug' => $r->courier_slug,
            'name' => $r->courier_name ?? strtoupper($r->courier_slug),
        ])->values()->all();
    }

    public function updatedCourier($value)
    {
        $this->authorizeShipOrAbort();
        $this->selectedServiceId = null;
        $this->services = [];
        $this->eta_start = $this->eta_end = $this->eta_text = $this->shipping_cost = null;
        $this->tracking_number = null; // preview will be generated when service chosen

        if ($value) {
            $this->loadServices();
        }
    }

    protected function loadServices()
    {
        // ambil services aktif untuk courier
        $this->services = Shipping::active()
            ->where('courier_slug', $this->courier)
            ->orderBy('base_rate')
            ->get()
            ->map(function($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'service_type' => $s->service_type,
                    'base_rate' => (float)$s->base_rate,
                    'per_kg' => (float)$s->per_kg,
                    'min_delivery_days' => (int)$s->min_delivery_days,
                    'max_delivery_days' => (int)$s->max_delivery_days,
                    'coverage' => $s->coverage,
                ];
            });
    }

    public function updatedSelectedServiceId($value)
    {
        $this->authorizeShipOrAbort();
        $this->computeEstimatesFromService();
    }

    protected function computeEstimatesFromService()
    {
        $service = collect($this->services)->firstWhere('id', (int)$this->selectedServiceId);
        if (! $service) {
            $this->eta_start = $this->eta_end = $this->eta_text = $this->shipping_cost = null;
            return;
        }

        // total berat order (misal: order->items->sum weight * qty)
        $totalWeightKg = $this->order->items->sum(function($it){
            // asumsi product punya weight_kg
            return ($it->product->weight_kg ?? 0) * ($it->qty ?? 1);
        });

        // Basic shipping cost: base + per_kg * weight
        $cost = $service['base_rate'] + ($service['per_kg'] * max(0, $totalWeightKg));

        // bisa tambahkan rule rounding / min fee dsb
        $this->shipping_cost = round($cost, 0);

        // ETA range
        $min = $service['min_delivery_days'];
        $max = $service['max_delivery_days'];

        $start = Carbon::now()->addDays($min);
        $end = Carbon::now()->addDays($max);

        $this->shipping_date = Carbon::now()->toDateString();
        $this->eta_start = $start->toDateString();
        $this->eta_end = $end->toDateString();

        // human readable
        $this->eta_text = $min === $max
            ? "{$min} hari ({$this->eta_start})"
            : "{$min} - {$max} hari ({$this->eta_start} — {$this->eta_end})";

        // generate preview tracking (non-final)
        $this->tracking_number = $this->generateTrackingPreview($this->order, $this->courier);
    }

    protected function generateTrackingPreview(Order $order, string $courier)
    {
        $prefix = strtoupper(substr($courier, 0, 4));
        $sellerId = Auth::id() ?? 'S';
        return "{$prefix}-S{$sellerId}-O{$order->id}-" . now()->format('YmdHis');
    }

    public function ship()
    {
        $this->authorizeShipOrAbort();
        $this->validate();

        // find service from DB again (authoritative)
        $service = Shipping::active()->find($this->selectedServiceId);
        if (! $service) {
            $this->addError('selectedServiceId', 'Service pengiriman tidak valid.');
            return;
        }

        DB::transaction(function () use ($service) {
            // final compute server-side
            $totalWeightKg = $this->order->items->sum(function($it){
                return ($it->product->weight_kg ?? 0) * ($it->qty ?? 1);
            });

            $finalTracking = $this->tracking_number ?: $this->generateTrackingPreview($this->order, $this->courier);
            $finalCost = $service->base_rate + ($service->per_kg * max(0, $totalWeightKg));

            $etaStart = now()->addDays($service->min_delivery_days);
            $etaEnd = now()->addDays($service->max_delivery_days);

            // Simpan di order (Mode C: simpan di orders)
            $this->order->update([
                'shipping_date' => now(),
                'tracking_number' => $finalTracking,
                'tracking_source' => $this->tracking_number ? 'seller' : 'system',
                'courier' => $this->courier,
                'courier_service_id' => $service->id, // optional FK
                'shipping_cost' => $finalCost,
                'estimated_arrival' => $etaEnd, // bisa simpan end date sebagai ETA
                'status' => 'shipped',
            ]);

            // optional: log history
            if (method_exists($this->order, 'histories')) {
                $this->order->histories()->create([
                    'action' => 'shipped',
                    'meta' => json_encode([
                        'service' => $service->service_type,
                        'courier' => $this->courier,
                        'tracking' => $finalTracking,
                        'eta_min' => $etaStart->toDateString(),
                        'eta_max' => $etaEnd->toDateString(),
                        'shipping_cost' => $finalCost,
                    ]),
                    'performed_by' => Auth::id(),
                ]);
            }
        });

        session()->flash('success','Order ditandai sebagai dikirim.');
        $this->order->refresh();
        $this->emit('orderShipped', $this->order->id);
    }

    protected function authorizeShipOrAbort()
    {
        $this->authorize('ship', $this->order);
    }

    public function render()
    {
        return view('livewire.seller.ship-order', [
            'services' => $this->services,
        ]);
    }
}
