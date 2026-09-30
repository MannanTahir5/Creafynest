<?php

namespace Database\Seeders;

use App\Models\ContactMessagingChannel;
use App\Models\ContactPageSection;
use App\Models\ContactSocialLink;
use Illuminate\Database\Seeder;

class ContactPageSeeder extends Seeder
{
    public function run(): void
    {
        $section = ContactPageSection::singleton();

        $defaults = ContactPageSection::defaultAttributes();

        foreach ($defaults as $key => $value) {
            $current = $section->{$key};

            if ($current === null) {
                $section->{$key} = $value;
                continue;
            }

            if (is_string($current) && trim($current) === '') {
                $section->{$key} = $value;
            }
        }

        $section->save();

        if ($section->messagingChannels()->count() === 0) {
            $channels = [
                [
                    'name' => 'WeChat',
                    'handle' => '+86 150 5326 2325',
                    'icon_slug' => 'wechat',
                    'icon_bg' => 'bg-emerald-500',
                    'qr_data' => 'wechat://creafynest',
                ],
                [
                    'name' => 'WhatsApp',
                    'handle' => '+44 7537 132205',
                    'icon_slug' => 'whatsapp',
                    'icon_bg' => 'bg-green-500',
                    'qr_data' => 'https://wa.me/447537132205',
                ],
            ];

            foreach ($channels as $i => $channel) {
                $section->messagingChannels()->create($channel + [
                    'sort_order' => $i,
                    'is_active' => true,
                ]);
            }
        }

        if ($section->socialLinks()->count() === 0) {
            $socials = [
                ['name' => 'Facebook', 'slug' => 'facebook', 'href' => 'https://facebook.com/', 'bg_class' => 'bg-[#1877F2]'],
                ['name' => 'Instagram', 'slug' => 'instagram', 'href' => 'https://instagram.com/', 'bg_class' => 'bg-gradient-to-tr from-[#feda75] via-[#d62976] to-[#4f5bd5]'],
                ['name' => 'WhatsApp', 'slug' => 'whatsapp', 'href' => 'https://wa.me/447537132205', 'bg_class' => 'bg-[#25D366]'],
                ['name' => 'LinkedIn', 'slug' => 'linkedin', 'href' => 'https://linkedin.com/', 'bg_class' => 'bg-[#0A66C2]'],
                ['name' => 'YouTube', 'slug' => 'youtube', 'href' => 'https://youtube.com/', 'bg_class' => 'bg-[#FF0033]'],
                ['name' => 'WeChat', 'slug' => 'wechat', 'href' => '#', 'bg_class' => 'bg-[#07C160]'],
            ];

            foreach ($socials as $i => $social) {
                $section->socialLinks()->create($social + [
                    'sort_order' => $i,
                    'is_active' => true,
                ]);
            }
        }
    }
}
