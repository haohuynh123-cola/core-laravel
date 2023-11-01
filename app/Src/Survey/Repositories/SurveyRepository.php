<?php

namespace App\Src\Survey\Repositories;

use App\Base\RepositoryBase;
use App\Models\Survey;
use App\Serializers\CustomDataSerializer;
use App\Src\Survey\Collections\SurveyCollection;
use App\Src\Survey\Transformers\SurveySmallDataTransformer;
use App\Src\Survey\Transformers\SurveyTransformer;
use App\Strategies\PlaceSurvey\SurveyStrategy;
use App\Strategies\PlaceSurvey\SurveyStrategyUpdate;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class SurveyRepository extends RepositoryBase
{
    protected $collection;
    protected $transformer;
    protected $fractal;

    protected $model = Survey::class;

    public function __construct(SurveyCollection $collection, SurveyTransformer $transformer, Manager $fractal)
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
            $survey = Survey::find($id);
            if ($survey == null) {
                $this->setMessage('Object not found');
                return $this->result([]);
            }
            $data = $this->fractal->createData(new Item($survey,  $this->transformer, 'data'))->toArray();
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
            $partner  = new SurveyStrategy();
            $dataCreate = $partner->process($params);
            DB::commit();
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
            $category = Survey::find($id);
            if (!$category) {
                return $this->resultFalse('Object not found');
            }
            $params['id'] = $id;
            $partner  = new SurveyStrategyUpdate();
            $updateProcess = $partner->process($params);
            DB::commit();
            return $this->resultTrueNoData('Update Success');
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return $this->resultFalse($e->getMessage());
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $survey = Survey::find($id);
            if (!$survey) {
                return $this->resultFalse('Object not found');
            }
            $survey->delete();
            DB::commit();
            return $this->resultTrueNoData('Survey delete success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resultFalse($e->getMessage());
        }
    }

    public function getSurveyData($id)
    {
        try {
            $survey = Survey::find($id);
            if ($survey == null) {
                $this->setMessage('Object not found');
                return $this->result([]);
            }
            $data = $this->fractal->createData(new Item($survey, new SurveySmallDataTransformer, 'data'))->toArray();
            return $this->result($data);
        } catch (\Exception $e) {
            $this->setMessage($e->getMessage());
            return $this->result([]);
        }
    }

    private function setData($data)
    {
        $_data = [];
        if (isset($data['title'])) {
            $_data['title'] = $data['title'];
        }
        if (isset($data['category_id'])) {
            $_data['category_id'] = $data['category_id'];
        }
        if (isset($data['start_date'])) {
            $_data['start_date'] = $data['start_date'];
        }
        if (isset($data['end_date'])) {
            $_data['end_date'] = $data['end_date'];
        }
        if (isset($data['questions'])) {
            $_data['questions'] = $data['questions'];
        }
        if (isset($data['description'])) {
            $_data['description'] = $data['description'];
        }
        return $_data;
    }
}
