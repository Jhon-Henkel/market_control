<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $purchase_id
 * @property string $name
 * @property float $quantity
 * @property string $unit
 * @property float $unit_price
 * @property float $total_price
 * @property string $created_at
 * @property string $updated_at
 *
 * @mixin Builder
 */
class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'purchase_id',
        'name',
        'quantity',
        'unit',
        'unit_price',
        'total_price',
    ];

    public $timestamps = true;

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];
}
