<?php

namespace App\Src\Role\Repositories;

use App\Base\RepositoryBase;
use App\Models\Role;
use App\Models\Survey;
use App\Serializers\CustomDataSerializer;
use App\Src\Role\Collections\RoleCollection;
use App\Src\Role\Transformers\RoleTransformer;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Illuminate\Support\Str;

class RoleRepository extends RepositoryBase
{
    protected $collection;
    protected $transformer;
    protected $fractal;
    protected $model = Survey::class;

    public function __construct(RoleCollection $collection, RoleTransformer $transformer, Manager $fractal)
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
            $this->collection->orderBy('id', 'desc');
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
            $role = Role::find($id);
            if ($role == null) {
                $this->setMessage('Object not found');
                return $this->result([]);
            }
            $data = $this->fractal->createData(new Item($role,  $this->transformer, 'data'))->toArray();
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
            $role = Role::create($data);
            $role->slug = Str::slug($role->name);
            $role->save();
            $data = $this->fractal->createData(new Item($role, $this->transformer, 'data'))->toArray();
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
            $role = Role::find($id);
            if (!$role) {
                return $this->resultFalse('Object not found');
            }
            $_data = $this->setData($params);
            $role->fill($_data);
            $role->update($_data);
            $data = $this->fractal->createData(new Item($role, $this->transformer, 'data'))->toArray();
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
            $role = Role::find($id);
            if (!$role) {
                return $this->resultFalse('Object not found');
            }
            $role->delete();
            DB::commit();
            return $this->resultTrueNoData('Role delete success');
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
        $_data['slug'] = Str::slug($_data['name']);
        return $_data;
    }
}