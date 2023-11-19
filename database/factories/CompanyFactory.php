<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name' => "company@gmail.com",
            'tax_code' => fake()->buildingNumber(),
            'address' => fake()->address(),
            'founded_in' => fake()->date(),
            'name' => fake()->name(),
            'office' => 'Hà Nội',
            'email' => "company@gmail.com",
            'password' => Hash::make(123456),
            'phone' => "0123456789",
            'map' => fake()->address(),
            'logo' => fake()->image(),
            'link_web' => 'link_web',
            'image_paper' => fake()->image(),
            'description' => 0,
            'coin' => 0,
            'email_verified_at' => now(),
            'status' => 0,
            'remember_token' => Str::random(10),        ];
    }
}
