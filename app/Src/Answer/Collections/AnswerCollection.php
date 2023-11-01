<?php

namespace App\Src\Answer\Collections;

use App\Base\BaseCollection;
use App\Helpers\Database\Collection;
use App\Models\Answer;

class AnswerCollection extends BaseCollection
{
    public function __construct()
    {
        $this->setModel(Answer::class);
    }

    public function filters($params)
    {
        if (isset($params['q'])) {
            $this->where('name', $params['q']);
        }
        return $this;
    }
}
