<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
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
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
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

    public function staff(): static
    {
        return $this->state(fn (array $attributes) => [
            'sso_type' => 'staff',
            'username' => fake()->unique()->userName(),
            'sso_id' => fake()->randomNumber(5),
        ]);
    }

    public function pupil(): static
    {
        return $this->state(fn (array $attributes) => [
            'sso_type' => 'stu',
            'username' => fake()->unique()->userName(),
            'sso_id' => fake()->randomNumber(5),
        ]);
    }

    public function asParent(): static
    {
        return $this->state(fn (array $attributes) => [
            'sso_type' => 'parents',
            'username' => fake()->unique()->userName(),
            'sso_id' => fake()->randomNumber(5),
        ]);
    }
}
