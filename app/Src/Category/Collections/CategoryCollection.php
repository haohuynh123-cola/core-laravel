<?php

namespace App\Src\Category\Collections;

use App\Base\BaseCollection;
use App\Helpers\Database\Collection;
use App\Models\Category;

class CategoryCollection extends BaseCollection
{
    public function __construct()
    {
        $this->setModel(Category::class);
    }

    public function filters($params)
    {
        if (isset($params['q'])) {
            $this->where('name', $params['q']);
        }
        return $this;
    }
}
