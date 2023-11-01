<?php

namespace App\Src\Question\Repositories;

use App\Base\RepositoryBase;
use App\Models\Question;
use App\Serializers\CustomDataSerializer;
use App\Src\Question\Collections\QuestionCollection;
use App\Src\Question\Transformers\QuestionTransformer;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class QuestionRepository extends RepositoryBase
{
    protected $collection;
    protected $transformer;
    protected $fractal;

    protected $model = Question::class;

    public function __construct(QuestionCollection $collection, QuestionTransformer $transformer, Manager $fractal)
    {
        $this->collection = $collection;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
        $this->setModel($this->model);
        $fractal->setSerializer(new CustomDataSerializer());
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
            $resource = new Collection($this->collection->getItems($page['is_limit']), $this->transformer, 'data');
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
            $dataShow = $this->find($id);
            if ($dataShow == null) {
                $this->setMessage('Object not found');
                return $this->result([]);
            }
            return $this->result($dataShow);
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
            // $partner  = new QuestionStrategy();
            // $dataCreate = $partner->process($data);
            // $data = $this->fractal->createData(new Item($dataCreate, $this->transformer))->toArray();
            DB::commit();
            // return $this->result($data);
            return $this->resultTrueNoData('Create Success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resultFalse($e->getMessage());
        }
    }

    public function update($params, $id)
    {
        DB::beginTransaction();
        try {
            $category = Question::find($id);
            if (!$category) {
                return $this->resultFalse('Object not found');
            }
            $_data = $this->setData($params);
            $category->fill($_data);
            $category->update($_data);
            $data = $this->fractal->createData(new Item($category, $this->transformer))->toArray();
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
            $category = Question::find($id);

            if (!$category) {
                return $this->resultFalse('Object not found');
            }
            $category->deleted_at = Now();
            $category->delete();

            DB::commit();
            return $this->resultTrueNoData('Question delete success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resultFalse($e->getMessage());
        }
    }

    private function setData($data)
    {
        $_data = [];
        // if (isset($data['title'])) {
        //     $_data['title'] = $data['title'];
        // }
        return $_data;
    }

    private function createQuestion($data)
    {
    }
}
