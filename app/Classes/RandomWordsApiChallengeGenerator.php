<?php

namespace App\Classes;

use App\Interfaces\ChallengeGenerator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class RandomWordsApiChallengeGenerator implements ChallengeGenerator
{
    private array $categories = ['wordle', 'sports','animals', 'countries', 'programming_languages', 'birds', 'softwares', 'companies', 'games'];

    public function getCategories(): Collection
    {
        return collect($this->categories);
    }

    public function generate(int $length = 8): RandomWord
    {
        $category = $this->categories[array_rand($this->categories)];

        $response = Http::withoutVerifying()
            ->get('https://random-words-api.kushcreates.com/api', [
                'language' => 'en',
                'category' => $category,
                'length' => $length,
                'type' => 'uppercase',
                'words' => 1,
            ]);

        $word = $response[0]['word'];

        return new RandomWord($category, $word);
    }
}
