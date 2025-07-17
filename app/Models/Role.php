<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role as SpatieRoleModel;
use Yogameleniawan\SearchSortEloquent\Traits\Searchable;
use Yogameleniawan\SearchSortEloquent\Traits\Sortable;

class Role extends SpatieRoleModel
{
    use HasFactory, Sluggable, Searchable, Sortable;
    
    protected $guarded = ['id'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'onUpdate' => true
            ]
        ];
    }

    /**
     * Get the formmated user's created_at.
     * @return Attribute
     */
    protected function formattedCreatedAt(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at 
                ? Carbon::parse($this->created_at)->format('d M, Y H:i') 
                : '[null]'
        );
    }

    /**
     * Get the formmated user's updated_at.
     * @return Attribute
     */
    protected function formattedUpdatedAt(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->updated_at
                ? Carbon::parse($this->updated_at)->format('d M, Y H:i')
                : '[null]'
        );
    }
}
