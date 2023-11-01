<?php

namespace App\Strategies\PlaceSurvey;

use App\Pipelines\PlaceUpdateSurvey\UpdateQuestionProcess;
use App\Pipelines\PlaceUpdateSurvey\UpdateSurveyProcess;
use Illuminate\Routing\Pipeline;
use Illuminate\Support\Collection;
use App\Strategies\PlaceSurveyInterface;
use Illuminate\Support\Facades\Log;

class SurveyStrategyUpdate
{
    private $pipelines = [
        UpdateSurveyProcess::class,
        UpdateQuestionProcess::class
    ];

    public function process($data)
    {
        try {
            $pipeline = app(Pipeline::class);
            return $pipeline->send($data)->through($this->pipelines)->then(function ($data) {
                return $data;
            });
        } catch (\Exception $e) {
            dd($e);
            $error = $e->getMessage();
            Log::error($error);
        }
    }
}
