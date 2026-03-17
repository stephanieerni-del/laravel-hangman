<?php

namespace App\Interfaces;

use App\Classes\RandomWord;
use Illuminate\Support\Collection;

interface ChallengeGenerator
{
    public function getCategories(): Collection;

    public function generate(int $length = 8): RandomWord;
}
