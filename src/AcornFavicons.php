<?php

declare(strict_types=1);

namespace ItinerisLtd\AcornFavicons;

use Illuminate\Support\Arr;
use Roots\Acorn\Application;

class AcornFavicons
{
    private array $faviconConfig = [
        'appleIcon' => [
            'rel' => 'apple-touch-icon',
            'type' => 'image/png',
            'prefix' => 'apple-touch-icon-',
        ],
        'favicons' => [
            'rel' => 'icon',
            'type' => 'image/png',
            'prefix' => 'favicon-',
        ],
        'windows' => [
            'rel' => 'msapplication-TileImage',
            'type' => 'image/png',
            'prefix' => 'mstile-',
        ],
    ];

    /**
     * @param Application $app
     * @param array $config
     * @param array $paths
     */
    public function __construct(
        protected Application $app,
        private array $config,
        private array $paths = []
    ) {
        // Decide what hook to use in dependency if the site icon is set.
        if (has_site_icon()) {
            add_filter('site_icon_meta_tags', [$this, 'generateAllFaviconTags']);
        } else {
            add_action('wp_head', [$this, 'getFaviconHeadTags']);
            add_action('login_head', [$this, 'getFaviconHeadTags']);
        }
    }

    public static function register(): ?self
    {
        $config = array_filter((array) config('favicons'));
        if (empty($config)) {
            return null;
        }

        return new self($config);
    }

    protected function getPublicPath(string $path, bool $addToPaths = true): ?string
    {
        if (! defined('ABSPATH')) {
            return null;
        }

        $webPath = rtrim(ABSPATH, '/wp');
        $path = ltrim($path, '/');

        if (! file_exists($webPath . '/' . $path)) {
            return null;
        }

        if ($addToPaths) {
            $this->paths[] = $path;
        }

        return home_url($path);
    }

    public function getFaviconHeadTags(): void
    {
        $meta_tags = $this->generateAllFaviconTags();
        if (empty($meta_tags)) {
            return;
        }

        echo implode(PHP_EOL, $meta_tags);
    }

    /**
     * Generate meta tags for all favicon types
     *
     * @param array $tags
     * @return array
     */
    public function generateAllFaviconTags(array $tags = []): array
    {
        return [
            ...$tags,
            ...$this->generateManifestTags(),
            ...$this->generatePwaMetaTags(),
            ...$this->generateAppleIconMetaTags(),
            ...$this->generateFaviconsMetaTags(),
            ...$this->generateWindowsMetaTags(),
        ];
    }

    /**
     * Generate manifest related tags
     *
     * @return array
     */
    protected function generateManifestTags(): array
    {
        $attributes = [
            [
                'rel' => 'shortcut icon',
                'href' => $this->getPublicPath('favicon.ico'),
            ],
            [
                'rel' => 'icon',
                'href' => $this->getPublicPath('favicon.svg'),
            ],
        ];

        return array_map(
            fn (array $attr): string => $this->buildMetaTag($attr),
            $attributes,
        );
    }

    /**
     * Generate PWA specific meta tags
     *
     * @return array
     */
    protected function generatePwaMetaTags(): array
    {
        $attributes = [
            [
                'name' => 'apple-mobile-web-app-title',
                'content' => $this->getAppName(),
            ],
        ];

        if (! empty($this->config['theme_color'])) {
            $attributes[] = [
                'name' => 'theme-color',
                'content' => $this->config['theme_color'],
            ];
        }

        return array_map(
            fn (array $attr): string => $this->buildMetaTag($attr, 'meta'),
            $attributes,
        );
    }

    /**
     * Generate Apple icon meta tags
     *
     * @return array
     */
    protected function generateAppleIconMetaTags(): array
    {
        return [
            $this->generateLinkTag('appleIcon', 'apple-touch-icon.png', '180x180'),
            $this->generateLinkTag('appleIcon', 'apple-touch-icon-precomposed.png', '180x180'),
        ];
    }

    /**
     * Generate standard favicon meta tags
     *
     * @return array
     */
    protected function generateFaviconsMetaTags(): array
    {
        $sizes = [16, 32, 48];
        return $this->generateSizedMetaTags('favicons', $sizes);
    }

    /**
     * Generate Windows tile meta tags
     *
     * @return array
     */
    protected function generateWindowsMetaTags(): array
    {
        $sizes = ['150x150'];
        $tags = array_map(
            fn (string $size): string => $this->generateLinkTag('windows', "mstile-{$size}.png", $size),
            $sizes,
        );

        $attributes = [
            [
                'name' => 'msapplication-TileColor',
                'content' => $this->config['msapplicationTileColor'] ?? '#ffffff',
            ],
            [
                'name' => 'msapplication-config',
                'content' => $this->getPublicPath('browserconfig.xml'),
            ],
        ];

        return array_merge(
            $tags,
            array_map(
                fn (array $attr): string => $this->buildMetaTag($attr, 'meta'),
                $attributes,
            ),
        );
    }

    /**
     * Generate meta tags for icons with specific sizes
     */
    protected function generateSizedMetaTags(string $type, array $sizes): array
    {
        return array_map(function (int|string $size) use ($type): string {
            $dimension = "{$size}x{$size}";
            return $this->generateLinkTag(
                $type,
                "{$this->faviconConfig[$type]['prefix']}{$dimension}.png",
                $dimension,
            );
        }, $sizes);
    }

    /**
     * Generate a single meta link tag
     */
    protected function generateLinkTag(string $type, string $filename, ?string $size = null): string
    {
        $config = $this->faviconConfig[$type];
        $attributes = [
            'rel' => $config['rel'] ?? '',
            'type' => $config['type'] ?? '',
            'href' => $this->getPublicPath($filename),
        ];

        if ($size) {
            $attributes['sizes'] = $size;
        }

        return $this->buildMetaTag($attributes);
    }

    /**
     * Generate a meta tag for a specific name and content
     */
    protected function generateMetaTag(string $name, string $content): string
    {
        return $this->buildMetaTag([
            'name' => $name,
            'content' => $content,
        ], 'meta');
    }

    /**
     * Build HTML meta tag from attributes
     *
     * @param array $attributes
     */
    protected function buildMetaTag(array $attributes, string $tag = 'link'): string
    {
        $attrs = [];
        foreach ($attributes as $key => $value) {
            if (empty($value)) {
                continue;
            }

            $attrs[] = sprintf('%s="%s"', $key, htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        }

        return sprintf('<%s %s>', $tag, implode(' ', $attrs));
    }

    /**
     * Get the app name
     */
    protected function getAppName(): string
    {
        return $this->config['appName'] ?? get_bloginfo('name');
    }
}
