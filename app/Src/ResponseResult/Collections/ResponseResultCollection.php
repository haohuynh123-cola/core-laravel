<?php

namespace App\Src\ResponseResult\Collections;

use App\Base\BaseCollection;
use App\Helpers\Database\Collection;
use App\Models\Category;
use App\Models\Survey;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResponseResultCollection extends BaseCollection
{
    public function __construct()
    {
        $this->setModel(Survey::class);
    }

    public function filters($params)
    {
        return $this;
    }
}
