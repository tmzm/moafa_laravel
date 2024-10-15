<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ProductFactory extends Factory
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
        $isOffer = fake()->boolean;
        $isQuantity = fake()->boolean;

        return [
            'name' => fake()->word,
            'slug' => fake()->slug,
            'is_offer' => $isOffer,
            'offer' => $isOffer ? fake()->optional()->numberBetween(5, 99) : null,
            'description' => fake()->paragraph,
            'price' => fake()->numberBetween(2, 10000, 100000),  // Random price between 10 and 1000
            'quantity' => $isQuantity ? fake()->numberBetween(0, 500) : 0,  // Random quantity between 1 and 500
            'is_quantity' => $isQuantity,
            'expiration' => fake()->date(),
            'image' => fake()->imageUrl(640, 480, 'products', true, 'product'),
            'meta_subtitle' => fake()->optional()->sentence,
            'meta_title' => fake()->optional()->sentence,
            'meta_description' => fake()->optional()->paragraph,
            'status' => fake()->boolean,
            'brand_id' => rand(1,15),  // Assuming a relation to a Brand model
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
