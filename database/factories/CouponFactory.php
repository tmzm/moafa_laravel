<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class CouponFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' =>fake()->boolean ? fake()->numberBetween(1,30) : null,  // Assuming a relation to a User model
            'code' => strtoupper(fake()->bothify('???-####')),  // Example code format
            'discount' => fake()->numberBetween(1, 50),  // Random discount between 1 and 50
            'status' => fake()->boolean,
            'discount_type' => fake()->randomElement(['PERCENTAGE', 'FIXED']),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
