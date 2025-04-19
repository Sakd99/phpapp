<?php

namespace App\Http\Controllers;

use App\Models\HomeBanner;
use Illuminate\Http\Request;

class HomeBannerController extends Controller
{
    /**
     * Get all home banners.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHomeBanners()
    {
        $banners = HomeBanner::with(['category', 'subCategory', 'subSubCategory'])
            ->orderBy('priority', 'asc')
            ->get()
            ->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'category' => [
                        'id' => $banner->category_id,
                        'name' => $banner->category ? $banner->category->name : 'غير متوفر',
                    ],
                    'subcategory' => [
                        'id' => $banner->subcategory_id,
                        'name' => $banner->subCategory ? $banner->subCategory->name : 'غير متوفر',
                    ],
                    'subsubcategory' => [
                        'id' => $banner->subsubcategory_id,
                        'name' => $banner->subSubCategory ? $banner->subSubCategory->name : 'غير متوفر',
                    ],
                    'image' => $banner->image ? asset('storage/app/public/' . $banner->image) : null,
                    'priority' => $banner->priority,
                ];
            });

        return response()->json($banners);
    }


    /**
     * Store a new home banner.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:sub_categories,id',
            'subsubcategory_id' => 'nullable|exists:sub_categories,id',
            'image' => 'nullable|image|max:2048',
            'priority' => 'required|integer',
        ]);

        $banner = HomeBanner::create($request->all());

        return response()->json([
            'message' => 'تم إنشاء البانر بنجاح',
            'banner' => $banner,
        ]);
    }
}
