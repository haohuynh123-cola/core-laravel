<?php

namespace App\Pipelines;

use Closure;

interface Pipe
{
    public function handle($data, Closure $next);
}
