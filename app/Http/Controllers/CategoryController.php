<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::select('id', 'name', 'image', 'percentage') // إضافة percentage هنا
        ->withCount('subCategories')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'image' => $category->image ? url('storage/app/public/' . $category->image) : null,
                    'percentage' => $category->percentage, // إضافة النسبة المئوية هنا
                    'sub_categories_count' => $category->sub_categories_count,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'percentage' => 'required|numeric|min:0|max:100', // التحقق من النسبة المئوية هنا
        ]);

        $imagePath = $request->file('image')->store('categories', 'public');

        $category = Category::create([
            'name' => $request->name,
            'image' => $imagePath,
            'percentage' => $request->percentage, // تخزين النسبة المئوية هنا
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'image' => url('storage/' . $category->image),
                'percentage' => $category->percentage, // عرض النسبة المئوية هنا
            ],
        ], 201);
    }

    public function show(Category $category)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'image' => $category->image ? url('storage/app/public/' . $category->image) : null,
                'percentage' => $category->percentage, // عرض النسبة المئوية هنا
                'sub_categories_count' => $category->subCategories()->count(),
            ],
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'image' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'percentage' => 'sometimes|required|numeric|min:0|max:100', // التحقق من النسبة المئوية هنا
        ]);

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $imagePath = $request->file('image')->store('categories', 'public');
            $category->image = $imagePath;
        }

        if ($request->has('name')) {
            $category->name = $request->name;
        }

        if ($request->has('percentage')) { // تحديث النسبة المئوية هنا
            $category->percentage = $request->percentage;
        }

        $category->save();

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'image' => url('storage/' . $category->image),
                'percentage' => $category->percentage, // عرض النسبة المئوية هنا
            ],
        ]);
    }


    public function getAllCategoriesWithSubcategories()
    {
        $categories = Category::with(['subCategories.subCategories'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'image' => $category->image ? url('storage/' . $category->image) : null,
                    'percentage' => $category->percentage,
                    'sub_categories' => $category->subCategories->map(function ($subCategory) {
                        return [
                            'id' => $subCategory->id,
                            'name' => $subCategory->name,
                            'image' => $subCategory->image ? url('storage/' . $subCategory->image) : null,
                            'sub_sub_categories' => $subCategory->subCategories->map(function ($subSubCategory) {
                                return [
                                    'id' => $subSubCategory->id,
                                    'name' => $subSubCategory->name,
                                    'image' => $subSubCategory->image ? url('storage/' . $subSubCategory->image) : null,
                                ];
                            }),
                        ];
                    }),
                ];
            }),
        ]);
    }


    public function destroy(Category $category)
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Category deleted successfully',
        ], 200);
    }

    public function getSubCategories($categoryId)
    {
        $category = Category::findOrFail($categoryId);

        $subCategories = $category->subCategories()
            ->select('id', 'name', 'image')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'image' => $category->image ? url('storage/' . $category->image) : null,
                    'percentage' => $category->percentage, // عرض النسبة المئوية هنا
                ],
                'sub_categories' => $subCategories->map(function ($subCategory) {
                    return [
                        'id' => $subCategory->id,
                        'name' => $subCategory->name,
                        'image' => $subCategory->image ? url('storage/' . $subCategory->image) : null,
                    ];
                }),
            ],
        ]);
    }
}
