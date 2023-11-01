<?php

namespace App\Src\Role\Collections;

use App\Base\BaseCollection;
use App\Helpers\Database\Collection;
use App\Models\Category;
use App\Models\Role;

class RoleCollection extends BaseCollection
{
    public function __construct()
    {
        $this->setModel(Role::class);
    }

    public function filters($params)
    {
        if (isset($params['q'])) {
            $this->where('name', $params['q']);
        }
        return $this;
    }
}