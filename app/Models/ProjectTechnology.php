<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProjectTechnology extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'category'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tech) {
            if (empty($tech->slug)) {
                $tech->slug = Str::slug($tech->name);
            }
        });
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_technology_pivot');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
