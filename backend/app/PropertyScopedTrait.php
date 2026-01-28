<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;

trait PropertyScopedTrait
{
    /**
     * Boot the PropertyScoped trait for a model.
     */
    protected static function bootPropertyScopedTrait(): void
    {
        static::addGlobalScope('property_scoped', function (Builder $builder) {
            $scopedPropertyIds = config('app.scoped_property_ids');

            // Only apply scope if property_ids are set
            if (!empty($scopedPropertyIds) && is_array($scopedPropertyIds)) {
                $builder->whereIn(static::getPropertyColumn(), $scopedPropertyIds);
            }
        });
    }

    /**
     * Get the column name for property_id filtering.
     * Override this method in your model if using different column name.
     */
    public static function getPropertyColumn(): string
    {
        return 'property_id';
    }

    /**
     * Query scope to temporarily disable property filtering.
     */
    public function scopeWithoutPropertyScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('property_scoped');
    }

    /**
     * Query scope to filter by specific properties.
     */
    public function scopeForProperties(Builder $query, array $propertyIds): Builder
    {
        return $query->withoutGlobalScope('property_scoped')
            ->whereIn(static::getPropertyColumn(), $propertyIds);
    }
}
