<?php

namespace App\Pages;

use Throwable;
use Filament\Pages\Page;
use Composer\InstalledVersions;

class SystemInfo extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = 'Sistema';
    protected string $view = 'pages.system-info';

    public static function canAccess(): bool
    {
        return session('rol_activo') === 'superadmin' && (bool) auth()->user()?->is_superadmin;
    }

    public function getLaravelVersion(): string
    {
        return app()->version();
    }

    public function getFilamentVersion(): ?string
    {
        try {
            if (class_exists(InstalledVersions::class) && InstalledVersions::isInstalled('filament/filament')) {
                return InstalledVersions::getPrettyVersion('filament/filament');
            }
        } catch (Throwable $e) {}

        return null;
    }

    public function getFilamentPackages(): array
    {
        $results = [];

        try {
            if (! class_exists(InstalledVersions::class)) {
                return [];
            }

            $packages = InstalledVersions::getInstalledPackages();

            foreach ($packages as $package) {
                $lower = strtolower($package);
                if (str_contains($lower, 'filament') || str_starts_with($lower, 'filament/')) {
                    $results[] = [
                        'name' => $package,
                        'version' => InstalledVersions::getPrettyVersion($package) ?? InstalledVersions::getVersion($package) ?? 'unknown',
                    ];
                }
            }

            foreach ($packages as $package) {
                if (str_starts_with($package, 'filament/')) {
                    $results[] = [
                        'name' => $package,
                        'version' => InstalledVersions::getPrettyVersion($package) ?? 'unknown',
                    ];
                }
            }

            return collect($results)->unique('name')->values()->all();
        } catch (Throwable $e) {
            return [];
        }
    }

    public function getPhpInfoHtml(): string
    {
        ob_start();
        phpinfo();
        $s = ob_get_clean();
        $s = preg_replace('#^.*<body>#is', '', $s);
        $s = preg_replace('#</body>.*$#is', '', $s);
        return $s;
    }
}