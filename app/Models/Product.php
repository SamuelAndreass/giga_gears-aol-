<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'name', 'description', 'category_id', 'original_price', 'discount_price',
        'discount_percentage', 'images', 'colors', 'specifications', 'features',
        'stock', 'brand', 'rating', 'review_count', 'is_featured'
    ];
    protected $casts = [
        'images' => 'array', 'colors' => 'array', 'specifications' => 'array',
        'features' => 'array', 'original_price' => 'decimal:2', 'discount_price' => 'decimal:2',
        'rating' => 'decimal:1', 'review_count' => 'integer', 'is_featured' => 'boolean', 'stock' => 'integer'
    ];
    public function category() { return $this->belongsTo(Category::class); }
    public function reviews() { return $this->hasMany(ProductReview::class); }

    public function Orderitems() { return $this->hasMany(OrderItem::class); }
    public function CartItems(){ return $this->hasMany(CartItem::class);}

    public function sellerStore(){ return $this->belongsTo(SellerStore::class);}
    protected static function boot() {
        parent::boot();
        static::saving(function ($product) {
            if ($product->original_price > 0 && $product->discount_price > 0) {
                $product->discount_percentage = round((($product->original_price - $product->discount_price) / $product->original_price) * 100);
            } else { $product->discount_percentage = 0; }
        });
    }
    public function getFirstImageAttribute() {
        return !empty($this->images) ? $this->images[0] : 'https://via.placeholder.com/600x600/3B82F6/FFFFFF?text=Gigagears';
    }
    public function getIsOnSaleAttribute() { 
        return $this->original_price > $this->discount_price; 
    }

}
