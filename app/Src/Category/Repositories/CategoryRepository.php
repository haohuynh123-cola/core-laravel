<?php

namespace App\Src\Category\Repositories;

use App\Base\RepositoryBase;
use App\Models\Category;
use App\Src\Category\Collections\CategoryCollection;
use App\Src\Category\Transformers\CategoryTransformer;
use App\Src\Category\Transformers\GetListTransformer;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;


class CategoryRepository extends RepositoryBase
{
    protected $collection;
    protected $transformer;
    protected $fractal;

    public function __construct(CategoryCollection $collection, CategoryTransformer $transformer, Manager $fractal)
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
            $category = Category::find($id);
            if ($category == null) {
                $this->setMessage('Object not found');
                return $this->result([]);
            }
            $data = $this->fractal->createData(new Item($category,  $this->transformer))->toArray();
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
            $category = Category::create($data);
            $data = $this->fractal->createData(new Item($category, $this->transformer))->toArray();
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
            $category = Category::find($id);
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
            $category = Category::find($id);
            if (!$category) {
                return $this->resultFalse('Object not found');
            }
            $category->delete();
            DB::commit();
            return $this->resultTrueNoData('Category delete success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resultFalse($e->getMessage());
        }
    }

    public function getList($params)
    {
        try {
            $page = $this->collection->basePaging($params);

            $this->collection->filters($params);
            $this->collection->setLimit($page['limit']);
            $this->collection->setOffset($page['offset']);
            $this->collection->orderBy('created_at', 'desc');
            $paging = $this->collection->getPaging();
            $resource = new Collection($this->collection->getItems($page['is_limit']), new GetListTransformer);
            $data = $this->fractal->createData($resource)->toArray();
            $data['paging'] = $paging;

            return $this->result($data);
        } catch (\Exception $e) {
            $this->setMessage($e->getMessage());
            return $this->result([]);
        }
    }

    private function setData($data)
    {
        $_data = [];

        if (!empty('name')) {
            $_data['name'] = $data['name'];
        }
        return $_data;
    }
}
