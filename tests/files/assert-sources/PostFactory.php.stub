<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;


class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;


    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->word(),
            'body' => $this->faker->text(),
            'user_id' => \Factories\Helpers\getRandomModelId(\App\Models\Post::class),
            'book_author_id' => \Factories\Helpers\getRandomModelId(\App\Models\Post::class),
            'tags' => [],
        ];
    }
}
