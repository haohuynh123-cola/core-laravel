<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'questions';

    protected $fillable = [
        'name',
        'survey_id',
        'type',
        'max_count',
        'correct_answer',
        'is_required',
        'answers_data',
        'answer_correct'
    ];

    protected $casts = [
        'answer_correct' => 'array',
    ];

    public function survey()
    {
        return $this->BelongsTo(Survey::class, 'survey_id', 'id');
    }

    public function answers()
    {
        return $this->HasMany(Answer::class);
    }

    public function questionType()
    {
        return $this->HasOne(TypeQuestion::class, 'id', 'type_id');
    }
}
