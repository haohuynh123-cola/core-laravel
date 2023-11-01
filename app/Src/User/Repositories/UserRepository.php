<?php

namespace App\Src\User\Repositories;

use App\Base\RepositoryBase;
use App\Models\Quizz;
use App\Models\User;
use App\Serializers\CustomDataSerializer;
use App\Src\User\Collections\UserCollection;
use App\Src\User\Transformers\UserTransformer;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;


class UserRepository extends RepositoryBase
{
    protected $collection;
    protected $transformer;
    protected $fractal;

    protected $model = User::class;

    public function __construct(UserCollection $collection, UserTransformer $transformer, Manager $fractal)
    {
        $this->collection = $collection;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
        $this->setModel($this->model);
//        $fractal->setSerializer(new CustomDataSerializer());
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
//            $dataCreate = $this->createItem($data);
            $dataCreate = Quizz::create($data);
            $data = $this->fractal->createData(new Item($dataCreate, $this->transformer))->toArray();
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
            $category = Quizz::find($id);
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
            $category = Quizz::find($id);
            if (!$category) {
                return $this->resultFalse('Object not found');
            }
            $category->deleted_at = Now();
            $category->delete();

            DB::commit();
            return $this->resultTrueNoData('Quizz delete success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resultFalse($e->getMessage());
        }
    }

    private function setData($data)
    {
        $_data = [];

        if (isset($data['name'])) {
            $_data['name'] = $data['name'];
        }
        if (isset($data['password'])) {
            $_data['password'] = $data['password'];
        }
        if(isset($data['brith_date'])){
            $_data['brith_date'] = $data['brith_date'];
        }


        return $_data;
    }
}
