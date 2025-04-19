<?php


use App\Filament\Pages\Home;
use App\Filament\Resources\BannerResource;
use App\Filament\Resources\BidsResource;
use App\Filament\Resources\OrdersResource;

return [

    'resources' => [
        OrdersResource::class,
        BannerResource::class,
        BidsResource::class,
        home::class,
    ],

    'broadcasting' => [
        // 'echo' => [
        //     'broadcaster' => 'pusher',
        //     'key' => env('VITE_PUSHER_APP_KEY'),
        //     'cluster' => env('VITE_PUSHER_APP_CLUSTER'),
        //     'wsHost' => env('VITE_PUSHER_HOST'),
        //     'wsPort' => env('VITE_PUSHER_PORT'),
        //     'wssPort' => env('VITE_PUSHER_PORT'),
        //     'authEndpoint' => '/api/v1/broadcasting/auth',
        //     'disableStats' => true,
        //     'encrypted' => true,
        // ],
    ],

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),

    'assets_path' => null,

    'livewire_loading_delay' => 'default',

    'auth' => [
        'guard' => 'web',
        'login' => \App\Http\Livewire\Auth\Login::class,  // تأكد من أن هذه الكلاس موجودة
    ],
];
