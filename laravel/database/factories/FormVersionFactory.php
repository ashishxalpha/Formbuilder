<?php

namespace Database\Factories;

use App\Models\FormVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormVersion>
 */
class FormVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_id' => \App\Models\Form::factory(),
            'schema_data' => ['version' => '1.0.0', 'fields' => []],
            'schema_hash' => hash('sha256', json_encode(['version' => '1.0.0', 'fields' => []])),
            'created_by' => \App\Models\User::factory(),
            'change_summary' => 'Initial version',
            'is_published' => false,
        ];
    }
}
