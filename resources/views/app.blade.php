<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php
            $seoMeta = $seoMeta ?? null;
            $pageSchemas = $pageSchemas ?? [];
            $defaultTitle = config('app.name', 'Azee');
            $twitterCard = ($seoMeta['image'] ?? null) ? 'summary_large_image' : 'summary';
        @endphp

        <title inertia>{{ $seoMeta['title'] ?? $defaultTitle }}</title>

        @if(!empty($seoMeta['description']))
            <meta inertia="meta-description" name="description" content="{{ $seoMeta['description'] }}">
        @endif
        @if(!empty($seoMeta['keywords']))
            <meta inertia="meta-keywords" name="keywords" content="{{ $seoMeta['keywords'] }}">
        @endif
        @if(!empty($seoMeta['canonical']))
            <link inertia="canonical" rel="canonical" href="{{ $seoMeta['canonical'] }}">
        @endif
        @if(!empty($seoMeta['robots']))
            <meta inertia="robots" name="robots" content="{{ $seoMeta['robots'] }}">
        @endif

        @if($seoMeta)
            <meta inertia="og-type" property="og:type" content="{{ $seoMeta['og_type'] ?? 'website' }}">
            <meta inertia="og-title" property="og:title" content="{{ $seoMeta['og_title'] ?? $seoMeta['title'] ?? $defaultTitle }}">
            @if(!empty($seoMeta['og_description'] ?? $seoMeta['description'] ?? ''))
                <meta inertia="og-description" property="og:description" content="{{ $seoMeta['og_description'] ?? $seoMeta['description'] }}">
            @endif
            @if(!empty($seoMeta['canonical']))
                <meta inertia="og-url" property="og:url" content="{{ $seoMeta['canonical'] }}">
            @endif
            @if(!empty($seoMeta['image']))
                <meta inertia="og-image" property="og:image" content="{{ $seoMeta['image'] }}">
            @endif
            @if(!empty($seoMeta['og_image_alt']) && !empty($seoMeta['image']))
                <meta inertia="og-image-alt" property="og:image:alt" content="{{ $seoMeta['og_image_alt'] }}">
            @endif
            @if(!empty($seoMeta['og_locale']))
                <meta inertia="og-locale" property="og:locale" content="{{ $seoMeta['og_locale'] }}">
            @endif
            <meta inertia="og-site-name" property="og:site_name" content="{{ $defaultTitle }}">

            <meta inertia="tw-card" name="twitter:card" content="{{ $twitterCard }}">
            <meta inertia="tw-title" name="twitter:title" content="{{ $seoMeta['og_title'] ?? $seoMeta['title'] ?? $defaultTitle }}">
            @if(!empty($seoMeta['twitter_site']))
                <meta inertia="tw-site" name="twitter:site" content="{{ '@'.$seoMeta['twitter_site'] }}">
            @endif
            @if(!empty($seoMeta['twitter_creator']))
                <meta inertia="tw-creator" name="twitter:creator" content="{{ '@'.$seoMeta['twitter_creator'] }}">
            @endif
            @if(!empty($seoMeta['og_description'] ?? $seoMeta['description'] ?? ''))
                <meta inertia="tw-description" name="twitter:description" content="{{ $seoMeta['og_description'] ?? $seoMeta['description'] }}">
            @endif
            @if(!empty($seoMeta['image']))
                <meta inertia="tw-image" name="twitter:image" content="{{ $seoMeta['image'] }}">
            @endif
            @if(!empty($seoMeta['article_published_time']))
                <meta property="article:published_time" content="{{ $seoMeta['article_published_time'] }}">
            @endif
            @if(!empty($seoMeta['article_modified_time']))
                <meta property="article:modified_time" content="{{ $seoMeta['article_modified_time'] }}">
            @endif
            @if(!empty($seoMeta['article_section']))
                <meta property="article:section" content="{{ $seoMeta['article_section'] }}">
            @endif
        @endif

        @if(is_array($seoMeta) && !empty($seoMeta['theme_color']))
            <meta name="theme-color" content="{{ $seoMeta['theme_color'] }}">
        @endif

        @foreach($globalSchemas ?? [] as $schema)
            <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @endforeach

        @foreach($pageSchemas as $schema)
            <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @endforeach

        <script>
            (function () {
                try {
                    var k = 'azee-theme';
                    var t = localStorage.getItem(k);
                    var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    var dark = t === 'dark' || (t !== 'light' && prefersDark);
                    document.documentElement.classList.toggle('dark', dark);
                } catch (e) {}
            })();
        </script>

        @php($gaId = config('analytics.ga4_measurement_id'))
        @php($gtmId = config('analytics.gtm_container_id'))
        @php($metaPixelId = config('analytics.meta_pixel_id'))

        @if(is_string($gtmId) && $gtmId !== '')
            <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer',@json($gtmId));</script>
        @endif

        @if(is_string($gaId) && $gaId !== '')
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', @json($gaId));
            </script>
        @endif

        @if(is_string($metaPixelId) && $metaPixelId !== '')
            <script>
                !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
                n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
                document,'script','https://connect.facebook.net/en_US/fbevents.js');
                fbq('init', @json($metaPixelId));
                fbq('track', 'PageView');
            </script>
        @endif

        @php($favicon = \App\Models\SiteSetting::cached()?->faviconUrl())
        @if(is_string($favicon) && $favicon !== '')
            <link rel="icon" href="{{ $favicon }}">
        @endif

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased bg-white text-slate-900 dark:bg-slate-950 dark:text-slate-100">
        @inertia
    </body>
</html>
