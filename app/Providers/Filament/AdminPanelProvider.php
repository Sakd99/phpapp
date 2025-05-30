<?php

namespace App\Providers\Filament;

use App\Models\Orders;
use App\Models\Products;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Js;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->login()
            ->colors([
                'primary' => Color::hex('#5755FE'),
                'secondary' => Color::hex('#7AA2E3'),
                'success' => Color::hex('#10B981'),
                'warning' => Color::hex('#F59E0B'),
                'danger' => Color::hex('#EF4444'),
                'info' => Color::hex('#97E7E1'),
                'gray' => Color::hex('#6B7280')
            ])
            ->profile()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->font('Tajawal', 'https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap')
            ->brandName('لوحة التحكم')
            ->favicon(asset('favicon.ico'))
            ->navigationGroups([
                'إدارة المحتوى',
                'إدارة المبيعات',
                'إدارة المزايدات',
                'إدارة المستخدمين',
                'التقارير والإحصائيات',
                'الإعدادات'
            ])
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->topNavigation(false)
            ->maxContentWidth('full')
            ->darkMode(true)
            ->sidebarCollapsibleOnDesktop()
//            ->asset([
//                Js::make('custom-script', 'https://maps.googleapis.com/maps/api/js?key=' . config('services.google.maps.key') . '&callback=initMap'),
//            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
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
            ])
            ->userMenuItems([
                'logout' => MenuItem::make()
                    ->label('تسجيل الخروج')
                    ->url('/logout'),
            ]);
    }
}
