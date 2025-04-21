@php
    $record = $getRecord();
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @if(isset($record->product_image1) && !empty($record->product_image1))
        <div class="relative overflow-hidden rounded-lg shadow-md">
            @php
                // تعديل مسار الصورة ليعمل بشكل صحيح - استخدام المسار المباشر
                $imageUrl = asset('storage/app/public/' . $record->product_image1);
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
                $imageUrl = asset('storage/app/public/' . $record->product_image2);
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
                $imageUrl = asset('storage/app/public/' . $record->product_image3);
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

<div class="mt-8 grid grid-cols-1 gap-6">
    @php
        $propertiesData = $record->properties_data;
        $properties = \App\Models\Property::whereIn('id', array_keys($propertiesData ?? []))->get();
    @endphp

    @if(count($propertiesData) > 0)
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">خصائص المنتج</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($properties as $property)
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ $property->name }}:
                        </div>
                        <div class="mt-1 text-base font-semibold text-gray-900 dark:text-white">
                            @php
                                $value = $propertiesData[$property->id] ?? null;

                                // إذا كانت القيمة مصفوفة (للقوائم متعددة الاختيارات)
                                if (is_array($value)) {
                                    echo implode(', ', $value);
                                } elseif ($property->type === 'boolean') {
                                    echo $value ? 'نعم' : 'لا';
                                } else {
                                    echo $value;
                                }
                            @endphp
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
