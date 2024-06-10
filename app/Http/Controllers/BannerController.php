<?php

namespace App\Http\Controllers;

use App\Http\Requests\BannerRequest;
use App\Models\Banner;

class BannerController extends Controller
{
    public function index()
    {
        return Banner::all();
    }

    public function store(BannerRequest $request)
    {
        return Banner::create($request->validated());
    }

    public function show(Banner $banner)
    {
        return $banner;
    }

    public function update(BannerRequest $request, Banner $banner)
    {
        $banner->update($request->validated());

        return $banner;
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();

        return response()->json();
    }
}
