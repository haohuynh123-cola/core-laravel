<?php

namespace App\Src\Category\Transformers;

use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    public function transform($obj)
    {
        $data = [
            'id' => $obj->id,
            'name' => $obj->name,
            'slug' => $obj->slug,
        ];
        return $data;
    }
}
