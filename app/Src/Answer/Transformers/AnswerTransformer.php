<?php

namespace App\Src\Answer\Transformers;

use League\Fractal\TransformerAbstract;

class AnswerTransformer extends TransformerAbstract
{
    public function transform($obj)
    {
        $data = [
            'id' => $obj->id,
            'answer' => $obj->answer,
            'is_correct' => $obj->is_correct
        ];
        return $data;
    }
}
