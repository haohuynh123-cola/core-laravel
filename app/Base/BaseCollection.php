<?php

namespace App\Base;

use App\Components\PagingComponent;
use Illuminate\Support\Facades\DB;

class BaseCollection
{
    protected $query;
    protected $limit;
    protected $offset;
    protected $sorts = [];
    protected $is_group_by = false;

    public function setModel($model)
    {
        $this->query = $model::query();
    }

    public function setSelect(array $field)
    {
        if (count($field) != 0) {
            $this->query->select($field);
        }
        return $this;
    }

    public function setGroupBy($field)
    {
        $this->query->groupBy($field);
        $this->is_group_by = true;
        return $this;
    }

    public function getTotal()
    {
        if ($this->is_group_by == true) {
            $total = count($this->query->get());
        } else {
            $total = $this->query->count();
        }
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
        return $this->query->limit(PHP_INT_MAX)->offset($current_offset)->get();
    }

    public function getPaging()
    {
        $limit = intval($this->limit) > 0 ? intval($this->limit) : $this->getTotal();
        $offset = intval($this->offset);
        $paging = new PagingComponent($this->getTotal(), $offset, $limit);

        return $paging->render();
    }

    public function basePaging($data)
    {
        $result = [];
        $result['is_limit'] = true;

        $result['limit'] = isset($data['limit']) ? (int) $data['limit'] : 10;
        $result['page'] = isset($data['page']) ? (int) $data['page'] : 1;
        $result['offset'] = isset($data['offset']) ? (int) $data['offset'] : 0;

        if (!empty($data['page']) && $data['page'] > 1) {
            $result['offset'] = ($result['page'] - 1) * $result['limit'];
        }

        if ($result['limit'] == config('app.no_limit', -1)) {
            $result['is_limit'] = false;
        }

        return $result;
    }
}
