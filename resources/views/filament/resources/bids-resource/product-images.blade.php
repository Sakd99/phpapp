@php
    $record = $getRecord();
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @if(isset($record->product_image1) && !empty($record->product_image1))
        <div class="relative overflow-hidden rounded-lg shadow-md">
            @php
                // تعديل مسار الصورة ليعمل بشكل صحيح - استخدام المسار المباشر
                $imageUrl = asset('storage/' . $record->product_image1);
            @endphp
            <img src="{{ $imageUrl }}" alt="صورة المنتج 1" class="w-full h-64 object-cover">
            <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-2 text-center">
                الصورة الأولى
            </div>
        </div>
    @endif

    @if(isset($record->product_image2) && !empty($record->product_image2))
        <div class="relative overflow-hidden rounded-lg shadow-md">
            @php
                // تعديل مسار الصورة ليعمل بشكل صحيح - استخدام المسار المباشر
                $imageUrl = asset('storage/' . $record->product_image2);
            @endphp
            <img src="{{ $imageUrl }}" alt="صورة المنتج 2" class="w-full h-64 object-cover">
            <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-2 text-center">
                الصورة الثانية
            </div>
        </div>
    @endif

    @if(isset($record->product_image3) && !empty($record->product_image3))
        <div class="relative overflow-hidden rounded-lg shadow-md">
            @php
                // تعديل مسار الصورة ليعمل بشكل صحيح - استخدام المسار المباشر
                $imageUrl = asset('storage/' . $record->product_image3);
            @endphp
            <img src="{{ $imageUrl }}" alt="صورة المنتج 3" class="w-full h-64 object-cover">
            <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-2 text-center">
                الصورة الثالثة
            </div>
        </div>
    @endif
</div>

@if(!isset($record->product_image1) && !isset($record->product_image2) && !isset($record->product_image3))
    <div class="text-center p-4 bg-gray-100 dark:bg-gray-800 rounded-lg">
        <p class="text-gray-500 dark:text-gray-400">لا توجد صور متاحة للمنتج</p>
    </div>
@endif

<div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
    @if(isset($record->available_colors) && count($record->available_colors) > 0)
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">الألوان المتاحة</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($record->available_colors as $color)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                        {{ $color }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    @if(isset($record->available_sizes) && count($record->available_sizes) > 0)
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">الأحجام المتاحة</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($record->available_sizes as $size)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200">
                        {{ $size }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif
</div>
