<?php

namespace App\Src\User\Collections;

use App\Base\BaseCollection;
use App\Helpers\Database\Collection;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCollection extends BaseCollection
{
    public function __construct()
    {
        $this->setModel(User::class);
    }

    public function filters($params)
    {
        return $this;
    }


}
