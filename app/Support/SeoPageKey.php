<?php

namespace App\Support;

/**
 * Keys for {@see \App\Models\SeoPageSetting} rows (public index pages).
 */
final class SeoPageKey
{
    public const HOME = 'home';

    public const SERVICES = 'services';

    public const PORTFOLIO = 'portfolio';

    public const BLOG = 'blog';

    public const ABOUT = 'about';

    public const CONTACT = 'contact';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::HOME,
            self::SERVICES,
            self::PORTFOLIO,
            self::BLOG,
            self::ABOUT,
            self::CONTACT,
        ];
    }

    /**
     * @return array<string, array{label: string, path: string}>
     */
    public static function definitions(): array
    {
        return [
            self::HOME => ['label' => 'Homepage', 'path' => '/'],
            self::SERVICES => ['label' => 'Services index', 'path' => '/services'],
            self::PORTFOLIO => ['label' => 'Portfolio / case studies', 'path' => '/portfolio'],
            self::BLOG => ['label' => 'Blog index', 'path' => '/blog'],
            self::ABOUT => ['label' => 'About', 'path' => '/about'],
            self::CONTACT => ['label' => 'Contact', 'path' => '/contact'],
        ];
    }
}
