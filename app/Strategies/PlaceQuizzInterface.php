<?php

namespace App\Strategies;

use Illuminate\Support\Collection;

interface PlaceQuizzInterface
{
    public function placeQuizz(Collection $quizz);
}
