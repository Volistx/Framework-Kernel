<?php

namespace Volistx\FrameworkKernel\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Volistx\FrameworkKernel\Models\AdminLog;

class AdminLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AdminLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'url' => $this->faker->url(),
            'method' => $this->faker->randomElement(['POST', 'GET', 'PUT', 'DELETE', 'PATCH']),
            'ip' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
        ];
    }
}
