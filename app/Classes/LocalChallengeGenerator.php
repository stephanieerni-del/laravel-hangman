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
            'animals' => collect([
                ['word' => 'HORSE', 'description' => 'A strong domesticated animal often used for riding and farm work.'],
                ['word' => 'SHEEP', 'description' => 'A woolly farm animal known for producing wool and living in flocks.'],
                ['word' => 'RABBIT', 'description' => 'A small mammal with long ears that is known for hopping quickly.'],
                ['word' => 'DONKEY', 'description' => 'A hardy working animal often used to carry loads in rural areas.'],
                ['word' => 'GIRAFFE', 'description' => 'The tallest land animal, famous for its very long neck.'],
                ['word' => 'TIGER', 'description' => 'A large wild cat recognized by its orange coat and black stripes.'],
                ['word' => 'PANDA', 'description' => 'A black-and-white bear native to China that mostly eats bamboo.'],
                ['word' => 'ZEBRA', 'description' => 'An African animal related to horses, known for its black-and-white stripes.'],
                ['word' => 'ELEPHANT', 'description' => 'The largest land mammal, with a trunk and large ears.'],
                ['word' => 'MONKEY', 'description' => 'A clever primate often seen climbing trees and living in groups.'],
            ]),
            'countries' => collect([
                ['word' => 'CANADA', 'description' => 'A North American country known for vast forests and cold winters.'],
                ['word' => 'JAPAN', 'description' => 'An island nation in East Asia known for technology and rich traditions.'],
                ['word' => 'BRAZIL', 'description' => 'The largest country in South America, home to the Amazon rainforest.'],
                ['word' => 'FRANCE', 'description' => 'A European country famous for art, cuisine, and the Eiffel Tower.'],
                ['word' => 'GERMANY', 'description' => 'A central European country known for engineering and historic cities.'],
                ['word' => 'INDIA', 'description' => 'A large South Asian country known for diverse cultures and languages.'],
                ['word' => 'CHINA', 'description' => 'A populous East Asian country with one of the world oldest civilizations.'],
                ['word' => 'EGYPT', 'description' => 'A North African country known for the Nile River and ancient pyramids.'],
                ['word' => 'SPAIN', 'description' => 'A southwestern European country known for flamenco and Mediterranean coasts.'],
                ['word' => 'ITALY', 'description' => 'A southern European country known for Roman history and famous cuisine.'],
            ]),
            'programming_languages' => collect([
                ['word' => 'JAVASCRIPT', 'description' => 'A popular scripting language used to build interactive web applications.'],
                ['word' => 'PYTHON', 'description' => 'A beginner-friendly language used in web development, automation, and AI.'],
                ['word' => 'SWIFT', 'description' => 'A programming language created by Apple for iOS and macOS apps.'],
                ['word' => 'KOTLIN', 'description' => 'A modern language commonly used for Android app development.'],
                ['word' => 'CSHARP', 'description' => 'A Microsoft language used for enterprise software and game development.'],
                ['word' => 'GOLANG', 'description' => 'A compiled language by Google known for simplicity and concurrency.'],
                ['word' => 'SCALA', 'description' => 'A JVM language blending object-oriented and functional programming.'],
                ['word' => 'ELIXIR', 'description' => 'A functional language built on Erlang for scalable systems.'],
                ['word' => 'HASKELL', 'description' => 'A pure functional language known for strong typing and immutability.'],
                ['word' => 'OBJECTIVEC', 'description' => 'An older Apple language used before Swift for iOS and macOS apps.'],
            ]),
        ]);
    }

    public function getCategories(): Collection
    {
        return $this->words->keys();
    }

    public function generate(): RandomWord
    {
        $category = $this->words->keys()->random();
        $entry = $this->words[$category]->random();

        return new RandomWord($category, $entry['word'], $entry['description']);
    }
}
