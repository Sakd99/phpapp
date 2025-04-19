<x-filament-panels::page>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    مرحباً بك في لوحة التحكم
                </h2>
                <p class="text-gray-600 dark:text-gray-300 mt-1">
                    استخدم لوحة التحكم لإدارة المحتوى والمستخدمين والإحصائيات بشكل سهل وفعال.
                </p>
            </div>
            <div class="hidden md:block">
                <x-filament::badge color="primary" size="xl" class="text-lg">
                    {{ now()->format('Y-m-d') }}
                </x-filament::badge>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
            <div class="bg-primary-50 dark:bg-primary-950 p-4 rounded-lg border border-primary-200 dark:border-primary-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primary-600 dark:text-primary-400 text-sm font-medium">روابط سريعة</p>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-1">الفئات</h3>
                    </div>
                    <div class="bg-primary-100 dark:bg-primary-900 p-2 rounded-lg">
                        <x-heroicon-o-tag class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('filament.admin.resources.categories.index') }}" class="text-primary-600 dark:text-primary-400 text-sm font-medium hover:underline">إدارة الفئات &rarr;</a>
                </div>
            </div>

            <div class="bg-success-50 dark:bg-success-950 p-4 rounded-lg border border-success-200 dark:border-success-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-success-600 dark:text-success-400 text-sm font-medium">روابط سريعة</p>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-1">المنتجات</h3>
                    </div>
                    <div class="bg-success-100 dark:bg-success-900 p-2 rounded-lg">
                        <x-heroicon-o-cube class="w-6 h-6 text-success-600 dark:text-success-400" />
                    </div>
                </div>
                <div class="mt-4">
                    <a href="#" class="text-success-600 dark:text-success-400 text-sm font-medium hover:underline">إدارة المنتجات &rarr;</a>
                </div>
            </div>

            <div class="bg-warning-50 dark:bg-warning-950 p-4 rounded-lg border border-warning-200 dark:border-warning-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-warning-600 dark:text-warning-400 text-sm font-medium">روابط سريعة</p>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-1">الطلبات</h3>
                    </div>
                    <div class="bg-warning-100 dark:bg-warning-900 p-2 rounded-lg">
                        <x-heroicon-o-shopping-cart class="w-6 h-6 text-warning-600 dark:text-warning-400" />
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('filament.admin.resources.orders.index') }}" class="text-warning-600 dark:text-warning-400 text-sm font-medium hover:underline">إدارة الطلبات &rarr;</a>
                </div>
            </div>

            <div class="bg-danger-50 dark:bg-danger-950 p-4 rounded-lg border border-danger-200 dark:border-danger-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-danger-600 dark:text-danger-400 text-sm font-medium">روابط سريعة</p>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-1">المزايدات</h3>
                    </div>
                    <div class="bg-danger-100 dark:bg-danger-900 p-2 rounded-lg">
                        <x-heroicon-o-tag class="w-6 h-6 text-danger-600 dark:text-danger-400" />
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('filament.admin.resources.bids.index') }}" class="text-danger-600 dark:text-danger-400 text-sm font-medium hover:underline">إدارة المزايدات &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
