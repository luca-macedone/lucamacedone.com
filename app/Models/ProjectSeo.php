<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectSeo extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image'
    ];

    protected $casts = [
        'meta_keywords' => 'array'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getOgImageUrlAttribute()
    {
        return $this->og_image ? asset('storage/' . $this->og_image) : null;
    }
}
