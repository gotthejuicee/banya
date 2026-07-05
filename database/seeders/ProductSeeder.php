<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // За оновленим дизайном кожен колір боксу — окрема картка зі своєю ціною.
        // 'image' — базовий шлях БЕЗ розширення (пара .webp/.png)
        $maleContents = [
            'Повстяна шапка «з характером»',
            'Повстяна рукавиця',
            'Дубовий віник ручного в’язання',
            'Аромаолія «Карпатська хвоя»',
            'Мило ручної роботи',
            'Дерев’яний бокс із гравіюванням',
        ];

        $femaleContents = [
            'Повстяний капелюшок',
            'Рушник-кілт на ґудзиках',
            'Скраб «Мед і кава»',
            'Аромаолія «Лаванда»',
            'Дерев’яний бокс із гравіюванням',
        ];

        $products = [
            [
                'slug' => 'dlia-nezaimanoho',
                'category' => 'male',
                'name' => 'Чоловічий набір — світлий бокс',
                'contents' => $maleContents,
                'price' => 2290,
                'image' => 'images/products/box-male-light',
                'sort' => 1,
            ],
            [
                'slug' => 'dlia-batka-lazni',
                'category' => 'male',
                'name' => 'Чоловічий набір — темний бокс',
                'contents' => $maleContents,
                'price' => 2490,
                'image' => 'images/products/box-male-dark',
                'sort' => 2,
            ],
            [
                'slug' => 'dlia-nezaimanoi',
                'category' => 'female',
                'name' => 'Жіночий набір — світлий бокс',
                'contents' => $femaleContents,
                'price' => 2290,
                'image' => 'images/products/box-female-light',
                'sort' => 1,
            ],
            [
                'slug' => 'dlia-tsarytsi-paru',
                'category' => 'female',
                'name' => 'Жіночий набір — темний бокс',
                'contents' => $femaleContents,
                'price' => 2490,
                'image' => 'images/products/box-female-dark',
                'sort' => 2,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['slug' => $product['slug']], $product + [
                'tagline' => 'Подарунковий банний набір',
                'description' => 'Готовий подарунок у дерев’яному боксі з гравіюванням.',
                'old_price' => null,
                'badge' => null,
                'photo' => null,
                'photo_dark' => null,
                'photo_light' => null,
                'image_dark' => null,
                'image_light' => null,
            ]);
        }
    }
}
