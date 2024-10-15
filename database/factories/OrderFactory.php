<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class OrderFactory extends Factory
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
        $isTime = fake()->boolean;
        $user = User::find(rand(1,30));

        return [
            'user_id' => $user->id,  // Assuming a relation to a User model
            'coupon_id' => fake()->numberBetween(1,15),  // Assuming a relation to a Coupon model
            'status' => fake()->randomElement(['PREPARING', 'SHIPPING', 'DELIVERED']),
            'payment_status' => fake()->boolean,
            'time' => $isTime ? fake()->dateTime : null,
            'is_time' => $isTime,
            'note' => fake()->optional()->paragraph,
            'location_id' => Location::where('user_id',$user->id)->first() ?? null,
            'accepted_by_user' => 1,
            'is_prescription' => 0,
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
