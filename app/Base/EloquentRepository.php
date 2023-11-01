<?php

namespace App\Base;

use App\Components\PagingComponent;

class EloquentRepository
{
    protected $query;
    protected $limit = 10;
    protected $offset = 0;
    protected $sorts = [];

    public function setModel($model)
    {
        $this->query = $model::query();
    }

    public function setSelect(array $field)
    {
        if (count($field) != 0) {
            $this->query->select($field);
        }
    }

    public function getTotal()
    {
        $total = $this->query->count();
        return $total;
    }

    public function orderBy($attribute, $direction)
    {
        $this->sorts[$attribute] = $direction;
        return $this;
    }

    public function setLimit($limit)
    {
        if (intval($limit) > 0) {
            $this->limit = $limit;
        }
    }

    public function setOffset($offset)
    {
        if (intval($offset) > 0) {
            $this->offset = $offset;
        }
    }

    public function getItems($is_limit = false)
    {
        if (count($this->sorts)) {
            foreach ($this->sorts as $field => $direction) {
                $this->query->orderBy($field, $direction);
            }
        }

        if ($is_limit == true) {
            return $this->query->limit($this->limit)->offset($this->offset)->get();
        }
        $current_offset = !empty($this->offset) ? $this->offset : 0;
        return $this->query->limit(config('app.max_request', 1000))->offset($current_offset)->get();
    }

    public function getPaging()
    {
        $limit = intval($this->limit) > 0 ? intval($this->limit) : 10;
        $offset = intval($this->offset);
        $paging = new PagingComponent($this->getTotal(), $offset, $limit);

        return $paging->render();
    }
}
