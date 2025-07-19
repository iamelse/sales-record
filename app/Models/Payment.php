<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Yogameleniawan\SearchSortEloquent\Traits\Searchable;
use Yogameleniawan\SearchSortEloquent\Traits\Sortable;

class Payment extends Model
{
    use HasFactory, Searchable, Sortable;

    protected $fillable = ['code', 'sale_id', 'amount'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    protected function formattedTotalPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->sale->total_price, 0, ',', '.')
        );
    }

    protected function formattedPaidAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->sale->paid_amount, 0, ',', '.')
        );
    }

    protected function formattedCreatedAt(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at
                ? Carbon::parse($this->created_at)->format('d M, Y H:i')
                : '[null]'
        );
    }

    protected function formattedUpdatedAt(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->updated_at
                ? Carbon::parse($this->updated_at)->format('d M, Y H:i')
                : '[null]'
        );
    }
}
