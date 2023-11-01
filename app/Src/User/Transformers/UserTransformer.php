<?php

namespace App\Src\User\Transformers;

use App\Models\Category;
use App\Models\Quizz;
use App\Src\Category\Transformers\CategoryTransformer;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform($obj)
    {
        $data = [
            'id' => $obj->id,
            'name' => $obj->name,
            'birthday' => $obj->birthday
        ];
        return $data;
    }

}
