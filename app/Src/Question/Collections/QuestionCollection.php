<?php

namespace App\Src\Question\Collections;

use App\Base\BaseCollection;
use App\Helpers\Database\Collection;
use App\Models\Category;
use App\Models\Question;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionCollection extends BaseCollection
{
    public function __construct()
    {
        $this->setModel(Question::class);
    }

    public function filters($params)
    {
        return $this;
    }
}
