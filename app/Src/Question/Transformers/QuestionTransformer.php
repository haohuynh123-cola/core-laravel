<?php

namespace App\Src\Question\Transformers;

use App\Models\Question;
use App\Src\Answer\Transformers\AnswerTransformer;
use League\Fractal\TransformerAbstract;

class QuestionTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array  $defaultIncludes  = [
        // 'answers'
    ];

    public function transform($obj)
    {
        $data = [
            'id' => $obj->id,
            'title' => $obj->name,
            'description' => $obj->description,
            'type' => $obj->type,
            'max_count' => $obj->max_count,
            'answers' => $obj->answers_data ? json_decode($obj->answers_data, true) : []
        ];
        return $data;
    }

    /**
     * Include Answers
     *
     * @return \League\Fractal\Resource\Collection
     */
    // public function includeAnswers(Question $question)
    // {
    //     $answers = $question->answers;
    //     return  $answers
    //         ? $this->Collection($answers, new AnswerTransformer())
    //         : $this->null();
    // }
}
