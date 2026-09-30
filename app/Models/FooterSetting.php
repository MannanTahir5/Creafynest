<?php

namespace App\Models;

use App\Support\ContentCache;
use App\Support\WebSettingsData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FooterSetting extends Model
{
    protected $fillable = [
        'logo_path',
        'logo_alt',
        'home_aria_label',
        'resources_heading',
        'contact_heading',
        'studio_label',
        'studio_text',
        'contact_email',
        'connect_heading',
        'copyright_entity',
        'organization_description',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public static function defaultPayload(): array
    {
        return [
            'company_links' => [
                ['label' => 'Why Azee', 'href' => '/about', 'icon' => 'HelpCircle'],
                ['label' => 'Approach', 'href' => '/about', 'icon' => 'Compass'],
                ['label' => 'Contact', 'href' => '/contact', 'icon' => 'Mail'],
                ['label' => 'Careers', 'href' => '/contact', 'icon' => 'Briefcase'],
            ],
            'resource_links' => [
                ['label' => 'Blog', 'href' => '/blog', 'icon' => 'Newspaper', 'external' => false],
                ['label' => 'Robots', 'href' => '/robots.txt', 'icon' => 'Bot', 'external' => true],
            ],
            'columns' => [
                [
                    'key' => 'products',
                    'heading' => 'Products',
                    'section_icon' => 'Package',
                    'links' => [
                        ['label' => 'All services', 'href' => '/services', 'icon' => 'LayoutGrid'],
                        ['label' => 'Web development', 'href' => '/services#web-development', 'icon' => 'Monitor'],
                        ['label' => 'Mobile & frontend', 'href' => '/services#mobile-development', 'icon' => 'Smartphone'],
                        ['label' => 'AI & data', 'href' => '/services#ai-solutions', 'icon' => 'Cpu'],
                        ['label' => 'Growth & SEO', 'href' => '/services#digital-marketing', 'icon' => 'TrendingUp'],
                    ],
                ],
                [
                    'key' => 'marketplace',
                    'heading' => 'Marketplace',
                    'section_icon' => 'ShoppingBag',
                    'links' => [
                        ['label' => 'Case studies', 'href' => '/portfolio', 'icon' => 'FolderKanban'],
                        ['label' => 'All projects', 'href' => '/portfolio', 'icon' => 'Star'],
                        ['label' => 'Project inquiry', 'href' => '/contact', 'icon' => 'Send'],
                    ],
                ],
                [
                    'key' => 'consultancy',
                    'heading' => 'Consultancy',
                    'section_icon' => 'Lightbulb',
                    'links' => [
                        ['label' => 'Laravel & APIs', 'href' => '/services', 'icon' => 'Code2'],
                        ['label' => 'Vue & Inertia', 'href' => '/services', 'icon' => 'Layers'],
                        ['label' => 'Performance & SEO', 'href' => '/services', 'icon' => 'Gauge'],
                        ['label' => 'Cloud & deploy', 'href' => '/services', 'icon' => 'Cloud'],
                    ],
                ],
                [
                    'key' => 'insights',
                    'heading' => 'Insights',
                    'section_icon' => 'LineChart',
                    'links' => [
                        ['label' => 'Case studies', 'href' => '/portfolio', 'icon' => 'FolderOpen'],
                        ['label' => 'Blog', 'href' => '/blog', 'icon' => 'BookOpen'],
                        ['label' => 'Guides', 'href' => '/about', 'icon' => 'FileText'],
                    ],
                ],
                [
                    'key' => 'solutions',
                    'heading' => 'Solutions',
                    'section_icon' => 'Target',
                    'links' => [
                        ['label' => 'Product builds', 'href' => '/portfolio', 'icon' => 'Rocket'],
                        ['label' => 'Team augmentation', 'href' => '/contact', 'icon' => 'Users'],
                        ['label' => 'Technical audits', 'href' => '/contact', 'icon' => 'ClipboardCheck'],
                    ],
                ],
            ],
            'socials' => [
                ['label' => 'X', 'href' => 'https://twitter.com'],
                ['label' => 'LinkedIn', 'href' => 'https://linkedin.com'],
                ['label' => 'Facebook', 'href' => 'https://facebook.com'],
                ['label' => 'YouTube', 'href' => 'https://youtube.com'],
            ],
            'legal_links' => [
                ['label' => 'Terms of use', 'href' => '/contact', 'icon' => 'Scale'],
                ['label' => 'Privacy policy', 'href' => '/contact', 'icon' => 'Shield'],
                ['label' => 'Accessibility', 'href' => '/contact', 'icon' => 'Eye'],
            ],
        ];
    }

    public static function defaultScalars(string $siteName): array
    {
        return [
            'logo_path' => null,
            'logo_alt' => $siteName,
            'home_aria_label' => $siteName.' home',
            'resources_heading' => 'Resources',
            'contact_heading' => 'Contact us',
            'studio_label' => 'Studio',
            'studio_text' => 'Remote-first delivery. EU & US–friendly time zones.',
            'contact_email' => 'hello@example.com',
            'connect_heading' => 'Connect with us',
            'copyright_entity' => $siteName,
            'organization_description' => $siteName.' builds maintainable web products, mobile experiences, and growth-ready platforms with Laravel, Vue, and pragmatic delivery from discovery to launch.',
        ];
    }

    /**
     * Cached row for public + schema (cleared when admin updates footer).
     */
    public static function cached(): ?self
    {
        $hit = Cache::get(ContentCache::FOOTER);
        if ($hit instanceof self) {
            return $hit;
        }

        $row = self::query()->first();
        if ($row !== null) {
            Cache::put(ContentCache::FOOTER, $row, ContentCache::TTL);
        }

        return $row;
    }

    public static function forgetCache(): void
    {
        Cache::forget(ContentCache::FOOTER);
    }

    /**
     * Ordered footer column keys (fixed layout on the public site).
     *
     * @return list<string>
     */
    public static function columnKeys(): array
    {
        return ['products', 'marketplace', 'consultancy', 'insights', 'solutions'];
    }

    /**
     * Strip technical fields for a simple admin form (label + URL only).
     *
     * @return array{
     *   company_links: list<array{label: string, href: string}>,
     *   resource_links: list<array{label: string, href: string, external: bool}>,
     *   columns: array<string, array{heading: string, links: list<array{label: string, href: string}>}>,
     *   socials: list<array{label: string, href: string}>,
     *   legal_links: list<array{label: string, href: string}>
     * }
     */
    public static function payloadForEditForm(?array $payload): array
    {
        $payload = is_array($payload) ? $payload : [];
        $defaults = collect(self::defaultPayload()['columns'])->keyBy('key');

        $company = [];
        foreach ($payload['company_links'] ?? [] as $row) {
            $company[] = [
                'label' => (string) ($row['label'] ?? ''),
                'href' => (string) ($row['href'] ?? ''),
                'icon' => (string) ($row['icon'] ?? 'ChevronRight'),
            ];
        }

        $resource = [];
        foreach ($payload['resource_links'] ?? [] as $row) {
            $resource[] = [
                'label' => (string) ($row['label'] ?? ''),
                'href' => (string) ($row['href'] ?? ''),
                'external' => (bool) ($row['external'] ?? false),
                'icon' => (string) ($row['icon'] ?? 'ChevronRight'),
            ];
        }
        $resource = self::removeSitemapLinks($resource);

        $columns = [];
        foreach (self::columnKeys() as $key) {
            $col = collect($payload['columns'] ?? [])->firstWhere('key', $key);
            $def = $defaults->get($key, ['heading' => ucfirst($key), 'links' => []]);
            $links = [];
            foreach (($col['links'] ?? $def['links'] ?? []) as $row) {
                $links[] = [
                    'label' => (string) ($row['label'] ?? ''),
                    'href' => (string) ($row['href'] ?? ''),
                    'icon' => (string) ($row['icon'] ?? 'ChevronRight'),
                ];
            }
            $columns[$key] = [
                'heading' => (string) ($col['heading'] ?? $def['heading'] ?? ucfirst($key)),
                'section_icon' => (string) ($col['section_icon'] ?? $def['section_icon'] ?? 'Library'),
                'links' => $links,
            ];
        }

        $socials = [];
        foreach ($payload['socials'] ?? [] as $row) {
            $socials[] = [
                'label' => (string) ($row['label'] ?? ''),
                'href' => (string) ($row['href'] ?? ''),
            ];
        }

        $legal = [];
        foreach ($payload['legal_links'] ?? [] as $row) {
            $legal[] = [
                'label' => (string) ($row['label'] ?? ''),
                'href' => (string) ($row['href'] ?? ''),
                'icon' => (string) ($row['icon'] ?? 'ChevronRight'),
            ];
        }

        return [
            'company_links' => $company,
            'resource_links' => $resource,
            'columns' => $columns,
            'socials' => $socials,
            'legal_links' => $legal,
        ];
    }

    /**
     * Build stored payload from the simple admin form (icons filled in automatically).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function assemblePayloadFromForm(array $data): array
    {
        $company = [];
        foreach ($data['company_links'] ?? [] as $row) {
            $label = trim((string) ($row['label'] ?? ''));
            $href = trim((string) ($row['href'] ?? ''));
            if ($label === '' || $href === '') {
                continue;
            }
            $company[] = [
                'label' => $label,
                'href' => $href,
                'icon' => self::sanitizeIcon((string) ($row['icon'] ?? ''), 'ChevronRight'),
            ];
        }

        $resources = [];
        foreach ($data['resource_links'] ?? [] as $row) {
            $label = trim((string) ($row['label'] ?? ''));
            $href = trim((string) ($row['href'] ?? ''));
            if ($label === '' || $href === '') {
                continue;
            }
            $external = filter_var($row['external'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $resources[] = [
                'label' => $label,
                'href' => $href,
                'external' => $external,
                'icon' => self::sanitizeIcon((string) ($row['icon'] ?? ''), $external ? 'ExternalLink' : 'ChevronRight'),
            ];
        }
        $resources = self::removeSitemapLinks($resources);

        $defaultCols = collect(self::defaultPayload()['columns'])->keyBy('key');

        $columns = [];
        foreach (self::columnKeys() as $key) {
            $block = $data['columns'][$key] ?? [];
            $defRow = $defaultCols->get($key, []);
            $heading = trim((string) ($block['heading'] ?? ''));
            if ($heading === '') {
                $heading = is_array($defRow)
                    ? (string) ($defRow['heading'] ?? ucfirst($key))
                    : ucfirst($key);
            }
            $sectionIcon = self::sanitizeIcon(
                (string) ($block['section_icon'] ?? ''),
                is_array($defRow) ? (string) ($defRow['section_icon'] ?? 'Library') : 'Library',
            );
            $linksOut = [];
            foreach ($block['links'] ?? [] as $row) {
                $label = trim((string) ($row['label'] ?? ''));
                $href = trim((string) ($row['href'] ?? ''));
                if ($label === '' || $href === '') {
                    continue;
                }
                $linksOut[] = [
                    'label' => $label,
                    'href' => $href,
                    'icon' => self::sanitizeIcon((string) ($row['icon'] ?? ''), 'ChevronRight'),
                ];
            }
            $columns[] = [
                'key' => $key,
                'heading' => $heading,
                'section_icon' => $sectionIcon,
                'links' => $linksOut,
            ];
        }

        $socials = [];
        foreach ($data['socials'] ?? [] as $row) {
            $label = trim((string) ($row['label'] ?? ''));
            $href = trim((string) ($row['href'] ?? ''));
            if ($label === '' || $href === '') {
                continue;
            }
            $socials[] = ['label' => $label, 'href' => $href];
        }

        $legal = [];
        foreach ($data['legal_links'] ?? [] as $row) {
            $label = trim((string) ($row['label'] ?? ''));
            $href = trim((string) ($row['href'] ?? ''));
            if ($label === '' || $href === '') {
                continue;
            }
            $legal[] = [
                'label' => $label,
                'href' => $href,
                'icon' => self::sanitizeIcon((string) ($row['icon'] ?? ''), 'ChevronRight'),
            ];
        }

        return [
            'company_links' => $company,
            'resource_links' => $resources,
            'columns' => $columns,
            'socials' => $socials,
            'legal_links' => $legal,
        ];
    }

    /**
     * Public footer props for Inertia (DB or sane defaults if unseeded).
     */
    public static function publicOrFallback(): array
    {
        $row = self::cached();
        if ($row !== null) {
            return $row->toPublicArray();
        }

        $site = (string) config('app.name', 'Azee');
        $fallback = new self([
            ...self::defaultScalars($site),
            'payload' => self::defaultPayload(),
        ]);

        return $fallback->toPublicArray();
    }

    /**
     * @return array{description: ?string, same_as: array<int, string>, email: ?string}
     */
    public static function organizationExtrasForSchema(): array
    {
        $row = self::cached();
        if ($row !== null) {
            return $row->organizationSchemaExtras();
        }

        $site = (string) config('app.name', 'Azee');

        return (new self([
            ...self::defaultScalars($site),
            'payload' => self::defaultPayload(),
        ]))->organizationSchemaExtras();
    }

    public function organizationSchemaExtras(): array
    {
        $payload = is_array($this->payload) ? $this->payload : [];
        $socials = $payload['socials'] ?? [];
        $sameAs = [];
        foreach ($socials as $s) {
            $u = isset($s['href']) ? trim((string) $s['href']) : '';
            if ($u !== '' && (str_starts_with($u, 'http://') || str_starts_with($u, 'https://'))) {
                $sameAs[] = $u;
            }
        }

        $desc = trim((string) ($this->organization_description ?? ''));
        $email = trim((string) ($this->contact_email ?? ''));

        return [
            'description' => $desc !== '' ? $desc : null,
            'same_as' => array_values(array_unique($sameAs)),
            'email' => $email !== '' ? $email : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toPublicArray(): array
    {
        $siteName = config('app.name', 'Azee');
        $payload = is_array($this->payload) ? $this->payload : self::defaultPayload();
        $payload['resource_links'] = self::removeSitemapLinks($payload['resource_links'] ?? []);

        $logoUrl = WebSettingsData::footerLogoUrl($this) ?? '/images/creafynest-logo.png';

        return [
            'logo_url' => $logoUrl,
            'logo_alt' => $this->logo_alt ?: $siteName,
            'home_aria_label' => $this->home_aria_label ?: ($siteName.' home'),
            'resources_heading' => $this->resources_heading ?: 'Resources',
            'contact_heading' => $this->contact_heading ?: 'Contact us',
            'studio_label' => $this->studio_label ?: 'Studio',
            'studio_text' => $this->studio_text ?? '',
            'contact_email' => $this->contact_email ?? '',
            'connect_heading' => $this->connect_heading ?: 'Connect with us',
            'copyright_entity' => $this->copyright_entity ?: $siteName,
            'company_links' => $payload['company_links'] ?? [],
            'resource_links' => $payload['resource_links'] ?? [],
            'columns' => $payload['columns'] ?? [],
            'socials' => $payload['socials'] ?? [],
            'legal_links' => $payload['legal_links'] ?? [],
        ];
    }

    /**
     * Remove any stale sitemap link from saved footer data.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private static function removeSitemapLinks(array $items): array
    {
        return array_values(array_filter($items, function (array $item): bool {
            $label = strtolower((string) ($item['label'] ?? ''));
            $href = strtolower((string) ($item['href'] ?? ''));

            return ! str_contains($label, 'sitemap') && ! str_contains($href, 'sitemap.xml');
        }));
    }

    private static function sanitizeIcon(string $icon, string $fallback): string
    {
        $icon = trim($icon);
        if ($icon === '' || ! preg_match('/^[A-Za-z][A-Za-z0-9]*$/', $icon)) {
            return $fallback;
        }

        return $icon;
    }
}
