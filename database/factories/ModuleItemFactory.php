<?php

namespace Database\Factories;

use App\Models\ModuleItem;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModuleItemFactory extends Factory
{
    protected $model = ModuleItem::class;

    public function definition()
    {
        return [
            'module_id' => Module::factory(),
            'title' => $this->faker->sentence,
            'order' => $this->faker->randomNumber(2),
            'content' => $this->faker->paragraph,
            'type' => 'overview',
        ];
    }
}
