<?php

namespace App\Src\ResponseResult\Repositories;

use App\Base\RepositoryBase;
use App\Models\History;
use App\Models\Question;
use App\Models\ResultSurvey;
use App\Models\Survey;
use App\Serializers\CustomDataSerializer;
use App\Src\ResponseResult\Collections\ResponseResultCollection;
use App\Src\ResponseResult\Transformers\ResponseResultTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class ResponseResultRepository extends RepositoryBase
{
    protected $collection;
    protected $transformer;
    protected $fractal;
    protected $model = ResultSurvey::class;

    public function __construct(ResponseResultCollection $collection, ResponseResultTransformer $transformer, Manager $fractal)
    {
        $this->collection = $collection;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
        $this->setModel($this->model);
        $fractal->setSerializer(new CustomDataSerializer());
    }

    public function postResultSurvey($params)
    {
        DB::beginTransaction();
        try {
            if (isset($params['survey_id'])) {
                $survey = Survey::where('id', $params['survey_id'])->first();
                if ($survey) {
                    $questionResult = $this->checkQuestion($survey, $params);
                    $this->saveResultSurvey($questionResult, $survey); //save result survey
                    $calc = $this->calculationSurvey($questionResult, $survey);
                    $data['data'] = $this->fractal->createData(new Item($calc,  $this->transformer))->toArray();
                }
            }
            DB::commit();
            return $this->result($data);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resultFalse($e->getMessage());
        }
    }

    /**
     * Check Result Response
     * Params: $survey, $params
     * Return Array $resultCorrect
     */
    private function checkQuestion($survey, $params)
    {
        $allListQuestions = Question::where('survey_id', $params['survey_id'])->get();
        $correctAnswers = [];
        $resultCorrect = [];
        $surveyResultResponse = [];

        foreach ($allListQuestions as $question) {
            $correctAnswers[$question->id] = $question->answer_correct;
            if (array_key_exists($question->id, $params['questions'])) {
                $surveyResultResponse[$question->id] = [
                    'selected' => $params['questions'][$question->id],
                    'type' => $question->type
                ];
            }
        }
        foreach ($surveyResultResponse as $key => $value) {
            switch ($value['type']) {
                case 'radio':
                    $resultCorrect[$key] = [
                        'answer_choose' => $value['selected'],
                        'correct' => in_array($value['selected'], $correctAnswers[$key]),
                        'answer_correct' => $correctAnswers[$key]
                    ];
                    break;
                case 'checkbox':
                    $array1 = $correctAnswers[$key];
                    $array2 = $value['selected'];
                    $difference = array_diff($array1, $array2);
                    $resultCorrect[$key] = [
                        'answer_choose' => $value['selected'],
                        'correct' => empty($difference) ? true : false,
                        'answer_correct' => $correctAnswers[$key]
                    ];
                    break;
                case 'text':
                    $resultCorrect[$key] = [
                        'answer_choose' => $value['selected'],
                        'correct' => true
                    ];
                    break;
                case 'rating':
                    $resultCorrect[$key] = [
                        'answer_choose' => $value['selected'],
                        'correct' => true
                    ];
                    break;
            }
        }
        return $resultCorrect;
    }

    private function saveResultSurvey($questionResult, $survey)
    {
        foreach ($questionResult as $key => $value) {
            ResultSurvey::create([
                'survey_id' => $survey->id,
                'question_id' => $key,
                'answer' => is_array($value['answer_choose']) ? json_encode($value['answer_choose']) : $value['answer_choose'],
                'correct' => $value['correct']
            ]);
        }
        Log::info('#Save Result Success');
    }

    private function calculationSurvey($questionResult, $survey)
    {
        $data = [];
        $questionCount = Question::where('survey_id', $survey->id)->get()->count();
        $countCorrectTrue = 0;

        foreach ($questionResult as $item) {
            if ($item["correct"]) {
                $countCorrectTrue++;
            }
        }

        $data = [
            'total_question' => $questionCount,
            'total_correct_true' => $countCorrectTrue,
            'total_correct_false' => $questionCount - $countCorrectTrue,
            'percentage' => round(($countCorrectTrue / $questionCount) * 100, 2),
        ];
        History::create([
            'user_id' => 1,
            'survey_id' => $survey->id,
            'total_wrong' => $questionCount - $countCorrectTrue,
            'compile_at' => Carbon::now()
        ]);
        return $data;
    }
}