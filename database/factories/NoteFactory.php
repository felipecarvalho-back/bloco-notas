<?php

namespace Database\Factories;

use App\Models\Note;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Note>
 */
class NoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Geral', 'Trabalho', 'Pessoal', 'Ideias'];

        return [
            'title' => fake()->sentence(3),
            'content' => fake()->paragraphs(3, true),
            'category' => fake()->randomElement($categories),
            'is_pinned' => fake()->boolean(20),
            'is_archived' => false,
        ];
    }
}
