<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsersRequest;
use App\Models\Users;

class UsersController extends Controller
{
    public function index()
    {
        return Users::all();
    }

    public function store(UsersRequest $request)
    {
        return Users::create($request->validated());
    }

    public function show(Users $users)
    {
        return $users;
    }

    public function update(UsersRequest $request, Users $users)
    {
        $users->update($request->validated());

        return $users;
    }

    public function destroy(Users $users)
    {
        $users->delete();

        return response()->json();
    }
}
