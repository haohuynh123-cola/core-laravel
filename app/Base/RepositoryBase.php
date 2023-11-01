<?php

namespace App\Base;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class RepositoryBase
{
    protected $model;
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    protected $result = [
        'status' => true,
        'data' => null,
        'message' => ''
    ];

    protected $message = '';

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function result($data, $mess = null, $code = 200)
    {
        if ($this->message != '' && empty($data['data'])) {
            $this->result['status'] = false;
            $this->result['message'] = $this->message;
            $this->result['code'] = $code;
            return $this->result;
        } else {
            $this->result['status'] = true;
            $this->result['message'] = !empty($mess) ? $mess : $this->message;
        }

        if (isset($data['paging'])) {
            $this->result['paging'] = $data['paging'];
        }
        $this->result['data'] = $data['data'];

        if ($this->result['message'] == '') {
            unset($this->result['message']);
        }
        return $this->result;
    }

    public function resultFalse($message, $code = 400)
    {
        $this->result['message'] = $message;
        $this->result['status'] = false;
        $this->result['code'] = $code;

        return $this->result;
    }

    public function resultTrueNoData($message, $code = 200)
    {
        $this->result['status'] = true;
        $this->result['message'] = $message;
        $this->result['code'] = $code;

        return $this->result;
    }

    public function renderListDataRepo($params, $collection, $transformer, $sort = '', $select = [], $key_column = [])
    {
        $page = $collection->basePaging($params);

        if (!empty($select)) {
            $collection->setSelect($select);
        }
        $collection->filters($params);
        $collection->setLimit($page['limit']);
        $collection->setOffset($page['offset']);

        if ($sort) {
            $arr_sort = explode("|", $sort);
            foreach ($arr_sort as $key => $value) {
                $child_arr_sort = explode(",", $value);
                if (!in_array($child_arr_sort[0], $key_column)) {
                    return $this->resultFalse("Key sort $child_arr_sort[0] in valid");
                }
                $collection->orderBy($child_arr_sort[0], !empty($child_arr_sort[1]) ? $child_arr_sort[1] : 'desc');
            }
        }

        $paging = $collection->getPaging();
        $resource = new Collection($collection->getItems($page['is_limit']), $transformer);
        $data = (new Manager())->createData($resource)->toArray();
        $data['paging'] = $paging;

        return $data;
    }

    /*
    * Retrieve object with id
    *
    * @param int $id
    * @return \Symfony\Component\HttpFoundation\Response
    */
    // public function find($id)
    // {
    //     $model = $this->model;
    //     $transformer = $this->transformer;
    //     $object = $model::findOrFail($id);
    //     return $this->fractal->createData(new Item($object, $transformer))->toArray();
    // }

    public function createItem($data)
    {
        $model = $this->model;
        $dataCreate = $model->create($data);
        return $dataCreate;
    }
}
