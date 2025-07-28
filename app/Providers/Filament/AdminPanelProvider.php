<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use App\Helpers\InspiringID;
use Filament\Enums\ThemeMode;
use Filament\Support\Colors\Color;
use App\Filament\Admin\Pages\Auth\Login;
use Orion\FilamentGreeter\GreeterPlugin;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Filament\Admin\Widgets\SiswaAktifGenderChart;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Filament\Admin\Widgets\JumlahSiswaPerAngkatanChart;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use SolutionForest\FilamentSimpleLightBox\SimpleLightBoxPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->colors([
                'primary' => Color::Blue,
            ])
            ->breadcrumbs(false)
            ->maxContentWidth('full')
            ->plugin(SimpleLightBoxPlugin::make())
            ->brandName(env('APP_NAME'))
            ->maxContentWidth('full')
            ->sidebarCollapsibleOnDesktop()
            ->brandLogo(asset('images/logoSMPIT.png'))
            ->favicon(asset('favicons/android-chrome-192x192.png'))
            ->brandLogoHeight(fn() => request()->route()->getName() == 'filament.admin.auth.login' ? '10rem' : '5rem')
            ->defaultThemeMode(ThemeMode::Light)
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                \App\Filament\Admin\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->discoverClusters(in: app_path('Filament/Admin/Clusters'), for: 'App\\Filament\\Admin\\Clusters')
            ->plugins([
                FilamentApexChartsPlugin::make(),
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
                    
                    \Orion\FilamentGreeter\GreeterPlugin::make()
                    ->message('Selamat datang,')
                    ->name(function () {
                        $data = '';
                        $data .= auth()->user()->name;
                        if (!auth()->user()->hasRole(['superadmin'])) {
                            $roleSlug = auth()->user()->getRoleNames()->first();
                            $formattedRoleName = strtoupper(str_replace('-', ' ', $roleSlug));
                            $data .= ' [' . $formattedRoleName . ']';
                        }
                        return $data;
                    })
                    ->title(function () {
                        $data = \App\Helpers\InspiringID::quote();
                        return strip_tags($data);
                    })
                    ->avatar(size: 'w-16 h-16', url: asset('images/no_pic.jpg'))
                    ->timeSensitive(morningStart: 3, afternoonStart: 12, eveningStart: 15, nightStart: 18)
                    ->sort(-1)
                    ->columnSpan('full')
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
