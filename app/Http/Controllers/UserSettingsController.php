<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserSettingsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json($request->user()->settings ?? []);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'settings' => 'required|array',
        ]);

        $user = $request->user();
        $user->settings = array_merge($user->settings ?? [], $validated['settings']);
        $user->save();

        return response()->json($user->settings);
    }
}
