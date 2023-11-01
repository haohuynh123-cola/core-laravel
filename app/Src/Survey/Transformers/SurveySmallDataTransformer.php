<?php

namespace App\Src\Survey\Transformers;

use App\Models\Survey;
use App\Src\Category\Transformers\CategoryTransformer;
use App\Src\Question\Transformers\QuestionTransformer;
use League\Fractal\TransformerAbstract;

class SurveySmallDataTransformer extends TransformerAbstract
{
    public function transform($obj)
    {
        $data = [
            'id' => data_get($obj, 'id'),
            'title' => data_get($obj, 'title'),
            'start_date' => data_get($obj, 'start_date'),
            'end_date' => data_get($obj, 'end_date'),
            'description' => data_get($obj, 'description'),
        ];
        return $data;
    }
}
