<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompareItem extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'compare_list_id',
        'product_id',
    ];

    /**
     * @return BelongsTo<CompareList, $this>
     */
    public function compareList(): BelongsTo
    {
        return $this->belongsTo(CompareList::class);
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
