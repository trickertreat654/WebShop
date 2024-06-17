<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Sequence;







class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Product::factory(4)
        ->hasVariants(5)
        // ->has(Image::factory(3)->sequence(fn(Sequence $sequence) => ['featured' => [$sequence->index === 0]]))
        // ->has(Image::factory(3)->sequence(fn(Sequence $sequence) => ['featured' => [$sequence->index % 3 === 0]]))
        ->has(Image::factory(3)->sequence(fn(Sequence $sequence) => ['featured' => ($sequence->index%3) === 0]))
        
        ->create();
    }
}
