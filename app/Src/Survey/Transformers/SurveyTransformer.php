<?php

namespace App\Src\Survey\Transformers;

use App\Models\Survey;
use App\Src\Category\Transformers\CategoryTransformer;
use App\Src\Question\Transformers\QuestionTransformer;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class SurveyTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array  $defaultIncludes  = [
        'questions', 'category'
    ];

    public function transform($obj)
    {
        $data = [
            'id' => data_get($obj, 'id'),
            'title' => data_get($obj, 'title'),
            'start_date' => data_get($obj, 'start_date'),
            'end_date' => data_get($obj, 'end_date'),
            'description' => data_get($obj, 'description'),
            'date' => Carbon::parse(data_get($obj, 'created_at'))->format('d/m/Y'),
        ];
        return $data;
    }

    /**
     * Include Category
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeCategory(Survey $survey)
    {
        $category = $survey->category;
        return $category
            ? $this->item($category, new CategoryTransformer())
            : $this->null();
    }

    /**
     * Include Question
     *
     * @return \League\Fractal\Resource\Collection
     */
    public function includeQuestions(Survey $survey)
    {
        $questions = $survey->questions;
        return $questions
            ? $this->Collection($questions, new QuestionTransformer())
            : $this->null();
    }
}
