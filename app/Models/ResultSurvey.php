<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultSurvey extends Model
{
    use HasFactory;

    protected $table = 'result_survey';

    protected $fillable = [
        'survey_id',
        'question_id',
        'answer',
        'user_id',
        'correct'
    ];
}
