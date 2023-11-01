<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'answers';
    protected $fillable = [
        'question_id',
        'text',
        'value',
        'answer',
        'is_correct'
    ];

    public function question()
    {
        return $this->BelongsTo(Question::class, 'question_id', 'id');
    }
}
