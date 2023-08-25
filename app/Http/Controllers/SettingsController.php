<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{

    public function index(Request $request)
    {
        $settings = Setting::get();

        foreach ($settings as $idx => $setting)
            $settings[$idx]->value = json_decode($setting->value, true);

        return response()->json(['settings' => $settings], 200);
    }


    public function show($id)
    {
        $setting = Setting::where('name', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        $setting->value = json_decode($setting->value, true);

        return response()->json($setting, 200);
    }
}
