<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Yogameleniawan\SearchSortEloquent\Traits\Searchable;
use Yogameleniawan\SearchSortEloquent\Traits\Sortable;

class Item extends Model
{
    use HasFactory, Searchable, Sortable;

    protected $fillable = ['code', 'name', 'image', 'price'];

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->price, 0, ',', '.')
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
