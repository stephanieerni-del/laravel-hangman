<?php

namespace App\Classes;

use App\Interfaces\ChallengeGenerator;
use Illuminate\Support\Collection;

class LocalChallengeGenerator implements ChallengeGenerator
{
    private Collection $words;

    public function __construct()
    {
        $this->words = collect([
            'animals' => collect(['HORSE', 'SHEEP', 'RABBIT', 'DONKEY', 'GIRAFFE', 'TIGER', 'PANDA', 'ZEBRA', 'ELEPHANT', 'MONKEY']),
            'countries' => collect(['CANADA', 'JAPAN', 'BRAZIL', 'FRANCE', 'GERMANY', 'INDIA', 'CHINA', 'EGYPT', 'SPAIN', 'ITALY']),
            'programming_languages' => collect(['JAVASCRIPT', 'PYTHON', 'SWIFT', 'KOTLIN', 'CSHARP', 'GOLANG', 'SCALA', 'ELIXIR', 'HASKELL', 'OBJECTIVEC']),
        ]);
    }

    public function getCategories(): Collection
    {
        return $this->words->keys();
    }

    public function generate(int $length = 8): RandomWord
    {
        $category = $this->words->keys()->random();
        // Try to find a word of the requested length, fallback to any word
        $word = $this->words[$category]->first(function ($w) use ($length) {
            return strlen($w) === $length;
        }) ?? $this->words[$category]->random();

        return new RandomWord($category, $word);
    }
}
