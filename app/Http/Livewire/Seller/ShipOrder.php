<?php

namespace App\Http\Livewire\Seller;

use Livewire\Component;
use App\Models\Order;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShipOrder extends Component
{
    public Order $order;

    public $couriers = [];
    public $courier;
    public $tracking_number;
    public $shipping_date;
    public $eta_start;
    public $eta_end;
    public $eta_text;
    public $shipping_cost;

    protected $rules = [
        'courier' => 'required|string',
        'tracking_number' => 'nullable|string|max:120',
    ];

    public function mount(Order $order)
    {
        $this->order = $order;
        $this->authorize('ship', $order);

        $this->loadCouriers();
    }

    protected function loadCouriers()
    {
        $rows = ShippingService::active()
            ->select('courier_name')
            ->orderBy('courier_name')
            ->get();

        $this->couriers = $rows->map(fn($r) => [
            'slug' => $r->courier_slug,
            'name' => $r->courier_name,
        ])->toArray();
    }

    public function updatedCourier($value)
    {
        $this->authorize('ship', $this->order);

        $service = ShippingService::active()
            ->where('courier_slug', $value)
            ->first();

        if (!$service) {
            $this->reset(['shipping_cost','eta_start','eta_end','eta_text','tracking_number']);
            return;
        }

        // Hitung biaya
        $weight = $this->order->items->sum(fn($it) =>
            ($it->product->weight_kg ?? 0) * $it->qty
        );

        $this->shipping_cost = $service->base_rate + ($service->per_kg * $weight);

        // Hitung ETA
        $min = $service->min_delivery_days;
        $max = $service->max_delivery_days;

        $this->shipping_date = today()->toDateString();
        $this->eta_start = today()->addDays($min)->toDateString();
        $this->eta_end   = today()->addDays($max)->toDateString();

        $this->eta_text = ($min == $max)
            ? "{$min} hari (Tiba {$this->eta_start})"
            : "{$min}-{$max} hari ({$this->eta_start} — {$this->eta_end})";

        // Generate tracking preview
        $this->tracking_number = strtoupper(substr($value,0,4))
            . "-S" . Auth::id()
            . "-O" . $this->order->id
            . "-" . now()->format('YmdHis');
    }

    public function ship()
    {
        $this->authorize('ship', $this->order);
        $this->validate();

        $service = ShippingService::active()
            ->where('courier_slug', $this->courier)
            ->firstOrFail();

        DB::transaction(function () use ($service) {
            $this->order->update([
                'courier' => $this->courier,
                'shipping_date' => now(),
                'tracking_number' => $this->tracking_number ?? $this->generateTracking(),
                'estimated_arrival' => $this->eta_end,
                'shipping_cost' => $this->shipping_cost,
                'courier_service_id' => $service->id,
                'status' => 'shipped',
            ]);
        });

        session()->flash('success','Order berhasil dikirim.');
    }

    protected function generateTracking()
    {
        return strtoupper(substr($this->courier,0,4))
            . "-S" . Auth::id()
            . "-O" . $this->order->id
            . "-" . now()->format('YmdHis');
    }

    public function render()
    {
        return view('livewire.seller.ship-order');
    }
}
