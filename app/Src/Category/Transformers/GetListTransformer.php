<?php

namespace App\Src\Category\Transformers;

use League\Fractal\TransformerAbstract;

class GetListTransformer extends TransformerAbstract
{
    public function transform($obj)
    {
        $data = [
            'label' => $obj->name,
            'value' => $obj->id,
        ];
        return $data;
    }
}
