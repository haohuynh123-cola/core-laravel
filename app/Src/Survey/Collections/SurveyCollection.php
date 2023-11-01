<?php

namespace App\Src\Survey\Collections;

use App\Base\BaseCollection;
use App\Helpers\Database\Collection;
use App\Models\Category;
use App\Models\Survey;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyCollection extends BaseCollection
{
    public function __construct()
    {
        $this->setModel(Survey::class);
    }

    public function filters($params)
    {
        if (isset($params['qs'])) {
            $this->query->where('title', 'like', '%' . $params['qs'] . '%');
        }
        return $this;
    }
}
