<?php

namespace App\Classes;

use App\Interfaces\ChallengeGenerator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class RandomWordsApiChallengeGenerator implements ChallengeGenerator
{
    private array $categories = ['animals', 'countries', 'programming_languages'];

    public function getCategories(): Collection
    {
        return collect($this->categories);
    }

    public function generate(): RandomWord
    {
        $category = $this->categories[array_rand($this->categories)];

        $response = Http::withoutVerifying()
            ->get('https://random-words-api.kushcreates.com/api', [
                'language' => 'en',
                'category' => $category,
                'length' => 8,
                'type' => 'uppercase',
                'words' => 1,
            ]);

        $word = $response[0]['word'];
        $description = 'A random ' . str_replace('_', ' ', $category) . ' word generated from the API.';

        return new RandomWord($category, $word, $description);
    }
}
