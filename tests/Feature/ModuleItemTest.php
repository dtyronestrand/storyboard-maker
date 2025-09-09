<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Models\ModuleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_create_a_module_item()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['user_id' => $user->id]);
        $module = Module::factory()->create(['course_id' => $course->id]);

        $response = $this->actingAs($user)->post(route('modules.items.store', $module), [
            'title' => 'New Module Item',
            'type' => 'overview',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('module_items', [
            'title' => 'New Module Item',
            'module_id' => $module->id,
        ]);
    }

    public function test_a_user_can_update_a_module_item()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['user_id' => $user->id]);
        $module = Module::factory()->create(['course_id' => $course->id]);
        $item = ModuleItem::factory()->create(['module_id' => $module->id]);

        $response = $this->actingAs($user)->put(route('items.update', $item), [
            'title' => 'Updated Module Item',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('module_items', [
            'id' => $item->id,
            'title' => 'Updated Module Item',
        ]);
    }

    public function test_a_user_can_delete_a_module_item()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['user_id' => $user->id]);
        $module = Module::factory()->create(['course_id' => $course->id]);
        $item = ModuleItem::factory()->create(['module_id' => $module->id]);

        $response = $this->actingAs($user)->delete(route('items.destroy', $item));

        $response->assertRedirect();
        $this->assertDatabaseMissing('module_items', [
            'id' => $item->id,
        ]);
    }

    public function test_a_user_can_reorder_module_items()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['user_id' => $user->id]);
        $module = Module::factory()->create(['course_id' => $course->id]);
        $item1 = ModuleItem::factory()->create(['module_id' => $module->id, 'order' => 1]);
        $item2 = ModuleItem::factory()->create(['module_id' => $module->id, 'order' => 2]);

        $response = $this->actingAs($user)->post(route('modules.items.reorder', $module), [
            'items' => [
                ['id' => $item1->id, 'order' => 2],
                ['id' => $item2->id, 'order' => 1],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('module_items', [
            'id' => $item1->id,
            'order' => 2,
        ]);
        $this->assertDatabaseHas('module_items', [
            'id' => $item2->id,
            'order' => 1,
        ]);
    }
}
