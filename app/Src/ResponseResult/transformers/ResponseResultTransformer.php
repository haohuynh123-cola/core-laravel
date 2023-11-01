<?php

namespace App\Src\ResponseResult\Transformers;

use App\Src\Category\Transformers\CategoryTransformer;
use League\Fractal\TransformerAbstract;

class ResponseResultTransformer extends TransformerAbstract
{
    public function transform($obj)
    {
        $data = [
            'total_question' => $obj['total_question'],
            'total_correct_true' => $obj['total_correct_true'],
            'total_correct_false' => $obj['total_correct_false'],
            'percentage' => $obj['percentage']
        ];
        return $data;
    }
}
