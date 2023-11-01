<?php

namespace App\Src\Answer\Repositories;

use App\Base\RepositoryBase;
use App\Models\Answer;
use App\Src\Answer\Collections\AnswerCollection;
use App\Src\Answer\Transformers\AnswerTransformer;
use App\Src\Answer\Transformers\GetListTransformer;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;


class AnswerRepository extends RepositoryBase
{
    protected $collection;
    protected $transformer;
    protected $fractal;

    public function __construct(AnswerCollection $collection, AnswerTransformer $transformer, Manager $fractal)
    {
        $this->collection = $collection;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }

    public function index($params)
    {
        try {
            $page = $this->collection->basePaging($params);

            $this->collection->filters($params);
            $this->collection->setLimit($page['limit']);
            $this->collection->setOffset($page['offset']);
            $this->collection->orderBy('created_at', 'desc');
            $paging = $this->collection->getPaging();
            $resource = new Collection($this->collection->getItems($page['is_limit']), $this->transformer);
            $data = $this->fractal->createData($resource)->toArray();
            $data['paging'] = $paging;

            return $this->result($data);
        } catch (\Exception $e) {
            $this->setMessage($e->getMessage());
            return $this->result([]);
        }
    }

    public function show($id)
    {
        try {
            $Answer = Answer::find($id);
            if ($Answer == null) {
                $this->setMessage('Object not found');
                return $this->result([]);
            }

            $data = $this->fractal->createData(new Item($Answer,  $this->transformer))->toArray();
            return $this->result($data);
        } catch (\Exception $e) {
            $this->setMessage($e->getMessage());
            return $this->result([]);
        }
    }

    public function create($params)
    {
        DB::beginTransaction();
        try {
            $data = $this->setData($params);
            $Answer = Answer::create($data);
            $data = $this->fractal->createData(new Item($Answer, $this->transformer))->toArray();
            DB::commit();
            return $this->result($data);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resultFalse($e->getMessage());
        }
    }

    public function update($params, $id)
    {
        DB::beginTransaction();
        try {
            $Answer = Answer::find($id);
            if (!$Answer) {
                return $this->resultFalse('Object not found');
            }
            $_data = $this->setData($params);
            $Answer->fill($_data);
            $Answer->update($_data);
            $data = $this->fractal->createData(new Item($Answer, $this->transformer))->toArray();
            DB::commit();
            return $this->result($data);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resultFalse($e->getMessage());
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $Answer = Answer::find($id);
            if (!$Answer) {
                return $this->resultFalse('Object not found');
            }
            $Answer->delete();
            DB::commit();
            return $this->resultTrueNoData('Answer delete success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resultFalse($e->getMessage());
        }
    }

    // public function getList($params)
    // {
    //     try {
    //         $page = $this->collection->basePaging($params);

    //         $this->collection->filters($params);
    //         $this->collection->setLimit($page['limit']);
    //         $this->collection->setOffset($page['offset']);
    //         $this->collection->orderBy('created_at', 'desc');
    //         $paging = $this->collection->getPaging();
    //         $resource = new Collection($this->collection->getItems($page['is_limit']), new GetListTransformer);
    //         $data = $this->fractal->createData($resource)->toArray();
    //         $data['paging'] = $paging;

    //         return $this->result($data);
    //     } catch (\Exception $e) {
    //         $this->setMessage($e->getMessage());
    //         return $this->result([]);
    //     }
    // }

    private function setData($data)
    {
        $_data = [];

        if (!empty('name')) {
            $_data['name'] = $data['name'];
        }
        return $_data;
    }
}
