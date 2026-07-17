<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RadioManifestController extends Controller
{
    public function manifest(Request $request)
    {
        $host = $request->getHost();

        $isRadioSubdomain = str_starts_with($host, 'radio.');

        return response()->json([
            'name' => 'Radio Tseyor',
            'short_name' => 'Radio Tseyor',
            'id' => 'org.tseyor.radio',
            'description' => 'Radio Tseyor — Talleres, meditaciones y cuentos',
            'theme_color' => '#60a5fa',
            'background_color' => '#0a2245',
            'display' => 'standalone',
            'orientation' => 'portrait-primary',
            'scope' => $isRadioSubdomain ? '/' : '/radio',
            'start_url' => $isRadioSubdomain ? '/' : '/radio',
            'icons' => [
                [
                    'src' => '/ic/android/android-launchericon-48-48.png',
                    'sizes' => '48x48',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => '/ic/android/android-launchericon-72-72.png',
                    'sizes' => '72x72',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => '/ic/android/android-launchericon-96-96.png',
                    'sizes' => '96x96',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => '/ic/android/android-launchericon-144-144.png',
                    'sizes' => '144x144',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => '/ic/android/android-launchericon-192-192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => '/ic/android/android-launchericon-512-512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'maskable',
                ],
            ],
        ]);
    }
}
