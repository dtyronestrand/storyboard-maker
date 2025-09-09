<?php

namespace Database\Factories;

use App\Models\Module;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModuleFactory extends Factory
{
    protected $model = Module::class;

    public function definition()
    {
        return [
            'course_id' => Course::factory(),
            'title' => $this->faker->sentence,
            'number' => $this->faker->randomNumber(2),
        ];
    }
}
