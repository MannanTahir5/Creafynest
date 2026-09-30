<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Project;
use App\Support\ContentCache;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        ContentCache::forget();

        $this->call(AdminUserSeeder::class);
        $this->call(HeroSeeder::class);
        $this->call(HomePageSettingSeeder::class);
        $this->call(DeliveryCategorySeeder::class);
        $this->call(AllServicesSeeder::class);
        $this->call(DesignDeliveryCategorySeeder::class);
        $this->call(DesignServicesSeeder::class);
        $this->call(TechnologySectionSeeder::class);
        $this->call(HomeSectionsSeeder::class);
        $this->call(ContactPageSeeder::class);
        $this->call(AboutPageSeeder::class);
        $this->call(FooterSettingSeeder::class);
        $this->call(SeoSettingsSeeder::class);

        $laravel = Category::query()->firstOrCreate(
            ['slug' => 'laravel'],
            ['name' => 'Laravel']
        );

        $vue = Category::query()->firstOrCreate(
            ['slug' => 'vue'],
            ['name' => 'Vue']
        );

        $seo = Category::query()->firstOrCreate(
            ['slug' => 'seo'],
            ['name' => 'SEO']
        );

        $this->call(TestimonialSeeder::class);

        // Seed curated portfolio case studies
        $this->call(CaseStudySeeder::class);

        // Seed editorial blog posts
        $this->call(SampleBlogsSeeder::class);

        // Seed site image assets
        $this->call(SiteImagesSeeder::class);

        // Only run dummy factories in local development environment if needed
        if (app()->environment('local')) {
            if (Project::query()->count() === 0) {
                Project::factory()->count(6)->create();
            }

            if (Blog::query()->count() <= 3) {
                Blog::factory()
                    ->count(6)
                    ->sequence(
                        ['category_id' => $laravel->id],
                        ['category_id' => $vue->id],
                        ['category_id' => $seo->id],
                    )
                    ->create();
            }
        }
    }
}
