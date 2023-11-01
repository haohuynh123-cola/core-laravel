<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survey extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surveys';
    protected $fillable = [
        'title',
        'description',
        'category_id',
        'start_date',
        'end_start',
        'data'
    ];

    public function category()
    {
        return $this->BelongsTo(Category::class, 'category_id', 'id');
    }

    public function questions()
    {
        return $this->HasMany(Question::class);
    }
}
