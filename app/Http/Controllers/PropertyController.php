<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * الحصول على جميع الخصائص النشطة
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveProperties()
    {
        try {
            $properties = Property::where('is_active', true)->get();

            // تنسيق البيانات للاستجابة
            $formattedProperties = $properties->map(function ($property) {
                return [
                    'id' => $property->id,
                    'name' => $property->name,
                    'type' => $property->type,
                    'options' => $property->options,
                    'is_required' => $property->is_required,
                    'type_text' => $this->getPropertyTypeText($property->type),
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $formattedProperties
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء جلب الخصائص',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على تفاصيل خاصية محددة
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProperty($id)
    {
        try {
            $property = Property::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $property->id,
                    'name' => $property->name,
                    'type' => $property->type,
                    'options' => $property->options,
                    'is_required' => $property->is_required,
                    'is_active' => $property->is_active,
                    'type_text' => $this->getPropertyTypeText($property->type),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'لم يتم العثور على الخاصية',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * الحصول على النص العربي لنوع الخاصية
     *
     * @param string $type
     * @return string
     */
    private function getPropertyTypeText($type)
    {
        return match ($type) {
            'text' => 'نص',
            'select' => 'قائمة منسدلة',
            'multiselect' => 'قائمة متعددة الاختيارات',
            'number' => 'رقم',
            'boolean' => 'نعم/لا',
            default => $type,
        };
    }
}
