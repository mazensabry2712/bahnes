<?php

namespace Database\Seeders;

use App\Models\ServiceLine;
use Illuminate\Database\Seeder;

class ServiceLineSeeder extends Seeder
{
    public function run(): void
    {
        ServiceLine::query()->firstOrCreate(
            ['key' => 'it-infrastructure-security'],
            [
                'name_en' => 'IT Infrastructure & Security',
                'name_ar' => 'البنية التحتية لتقنية المعلومات وأنظمة الأمن',
                'sort_order' => 10,
                'is_active' => true,
            ],
        );

        ServiceLine::query()->firstOrCreate(
            ['key' => 'software-digital-solutions'],
            [
                'name_en' => 'Software & Digital Solutions',
                'name_ar' => 'الحلول البرمجية والرقمية',
                'sort_order' => 20,
                'is_active' => true,
            ],
        );
    }
}
