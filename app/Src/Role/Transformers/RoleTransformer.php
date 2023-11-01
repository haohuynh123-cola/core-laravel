<?php

namespace App\Src\Role\Transformers;

use League\Fractal\TransformerAbstract;

class RoleTransformer extends TransformerAbstract
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