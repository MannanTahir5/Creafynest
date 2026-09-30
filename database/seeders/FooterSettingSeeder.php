<?php

namespace Database\Seeders;

use App\Models\FooterSetting;
use Illuminate\Database\Seeder;

class FooterSettingSeeder extends Seeder
{
    public function run(): void
    {
        $site = (string) config('app.name', 'Creafynest');
        $site = $site === 'Laravel' ? 'Creafynest' : $site;

        $row = FooterSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                ...FooterSetting::defaultScalars($site),
                'payload' => FooterSetting::defaultPayload(),
            ]
        );

        if (in_array($row->copyright_entity, ['', 'Laravel', 'Azee'], true)) {
            $row->copyright_entity = 'Creafynest';
            $row->save();
        }

        FooterSetting::forgetCache();
    }
}
