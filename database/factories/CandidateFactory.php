<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidate>
 */
class CandidateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => "candidate@gmail.com",
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('123456'), // password
            'phone' => "012345678",
            'gender' => 1,
            'desc' => "abc16",
            'image' => "asda",
            'status' => 1,
            'address' => fake()->address(),
            'type' => 1,
            'remember_token' => Str::random(10),
        ];
    }
}
