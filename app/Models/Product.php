<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'external_id', 'sku', 'ean', 'title', 'brand',
        'image_url', 'description', 'stock_quantity', 'status',
        'cost_price', 'sale_price', 'promotional_price', 'profit_margin',
        'weight', 'height', 'width', 'length',
        'category_id', 'listing_type_id', 'permalink', 'json_data'
    ];

    protected $with = ['medias', 'channel_settings'];

    protected $casts = [
        'json_data' => 'array',
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'promotional_price' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('company_id', Auth::user()->company_id);
            }
        });

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->company_id = Auth::user()->company_id;
            }
        });
    }

    // Relacionamentos existentes...
    public function medias() { return $this->hasMany(ProductMedia::class)->orderBy('position'); }
    public function channel_settings() { return $this->hasMany(ProductChannelSetting::class); }
    public function company() { return $this->belongsTo(Company::class); }
    
    // NOVO: Relacionamento essencial para o cálculo de vendas
    public function orderItems() 
    { 
        return $this->hasMany(OrderItem::class); 
    }
}