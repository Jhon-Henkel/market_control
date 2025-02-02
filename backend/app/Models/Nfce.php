<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $number
 * @property string $series
 * @property string $key
 * @property string $emission_date
 * @property int $purchase_id
 * @property bool $processed
 * @property string $created_at
 * @property string $updated_at
 *
 * @mixin Builder
 *
 * @method static Nfce findOrFail(int $id)
 */
class Nfce extends Model
{
    protected $table = 'nfce';

    protected $fillable = [
        'number',
        'series',
        'key',
        'processed',
        'emission_date',
    ];

    public $timestamps = true;

    protected $casts = [
        'emission_date' => 'datetime:Y-m-d H:i:s',
    ];

    public function process(): void
    {
        $this->processed = 1;
        $this->save();
    }
}
