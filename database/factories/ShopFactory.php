<?php

namespace Modules\Vendor\Database\Factories;

use Modules\Vendor\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShopFactory extends Factory
{
    protected $model = Shop::class;

    public function definition()
    {
        return [
            'owner_id' => \Modules\User\Models\User::factory(),
            'name' => $this->faker->unique()->company,
            'slug' => $this->faker->unique()->slug,
            'description' => $this->faker->paragraph,
            'cover_image' => [
                'thumbnail' => $this->faker->imageUrl(200, 200),
                'original' => $this->faker->imageUrl(),
            ],
            'logo' => [
                'thumbnail' => $this->faker->imageUrl(100, 100),
                'original' => $this->faker->imageUrl(),
            ],
            'is_active' => true,
            'address' => [
                'street_address' => $this->faker->streetAddress,
                'city' => $this->faker->city,
                'state' => $this->faker->state,
                'zip' => $this->faker->postcode,
                'country' => $this->faker->country
            ],
            'settings' => [
                'contact' => $this->faker->phoneNumber,
                'socials' => [
                    ['icon' => 'FacebookIcon', 'url' => $this->faker->url],
                    ['icon' => 'TwitterIcon', 'url' => $this->faker->url]
                ],
                'website' => $this->faker->url,
                'location' => [
                    'lat' => $this->faker->latitude,
                    'lng' => $this->faker->longitude,
                    'city' => $this->faker->city,
                    'state' => $this->faker->stateAbbr,
                    'country' => $this->faker->country,
                    'formattedAddress' => $this->faker->address
                ]
            ],
            'created_at' => $this->faker->dateTimeBetween('-2 years'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year'),
        ];
    }
}