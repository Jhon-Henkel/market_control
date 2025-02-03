<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $nfce_id
 * @property float $subtotal_value
 * @property float $discount_value
 * @property float $total_value
 * @property int $total_items
 * @property string $purchase_date
 * @property string $created_at
 * @property string $updated_at
 * @property Product[] $products
 *
 * @mixin Builder
 */
class Purchase extends Model
{
    protected $table = 'purchase';

    protected $fillable = [
        'nfce_id',
        'subtotal_value',
        'discount_value',
        'total_value',
        'purchase_date',
        'total_items',
    ];

    public $timestamps = true;

    protected $casts = [
        'subtotal_value' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'total_value' => 'decimal:2',
        'purchase_date' => 'datetime:Y-m-d H:i:s',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
