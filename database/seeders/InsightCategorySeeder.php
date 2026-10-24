<?php

namespace Database\Seeders;

use App\Models\InsightCategory;
use Illuminate\Database\Seeder;

class InsightCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name_en' => 'Company Updates', 'name_ar' => 'أخبار الشركة', 'slug_en' => 'company-updates', 'slug_ar' => 'akhbar-al-sharika', 'sort_order' => 10],
            ['name_en' => 'IT Infrastructure', 'name_ar' => 'البنية التحتية لتقنية المعلومات', 'slug_en' => 'it-infrastructure', 'slug_ar' => 'binya-tahtiya-it', 'sort_order' => 20],
            ['name_en' => 'Software & Digital Solutions', 'name_ar' => 'الحلول البرمجية والرقمية', 'slug_en' => 'software-digital-solutions', 'slug_ar' => 'hulul-barmagiya-raqamiya', 'sort_order' => 30],
            ['name_en' => 'Guides & Insights', 'name_ar' => 'أدلة ورؤى تقنية', 'slug_en' => 'guides-insights', 'slug_ar' => 'adella-wa-ruaa', 'sort_order' => 40],
        ];

        foreach ($categories as $category) {
            InsightCategory::query()->firstOrCreate(
                ['slug_en' => $category['slug_en']],
                $category + ['is_active' => true],
            );
        }
    }
}
