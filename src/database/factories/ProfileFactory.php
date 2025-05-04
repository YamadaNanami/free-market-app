<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'img_url' => $this->faker->imageUrl(),
            'post' => preg_replace('/^(\d{3})(\d{4})$/','$1-$2',$this->faker->postcode()),
            'address' => $this->faker->prefecture().$this->faker->city().$this->faker->streetAddress(),
            'building' => $this->faker->secondaryAddress(),
        ];
    }
}
