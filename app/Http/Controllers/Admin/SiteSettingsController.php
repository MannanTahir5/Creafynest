<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\ContentCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SiteSettingsController extends Controller
{
    public function edit(): Response
    {
        $row = SiteSetting::query()->first();
        if ($row === null) {
            $row = SiteSetting::query()->create([]);
            SiteSetting::forgetCache();
        }

        return Inertia::render('Admin/SiteSettings/Edit', [
            'settings' => [
                'app_name' => $row->app_name ?? '',
                'app_url' => $row->app_url ?? '',
                'timezone' => $row->timezone ?? '',
                'locale' => $row->locale ?? '',
                'ga4_measurement_id' => $row->ga4_measurement_id ?? '',
            ],
            'effective' => [
                'app_name' => (string) config('app.name'),
                'app_url' => (string) config('app.url'),
                'timezone' => (string) config('app.timezone'),
                'locale' => (string) config('app.locale'),
                'ga4_measurement_id' => (string) (config('analytics.ga4_measurement_id') ?? ''),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $nullableKeys = ['app_name', 'app_url', 'timezone', 'locale', 'ga4_measurement_id'];
        $trimmed = [];
        foreach ($nullableKeys as $key) {
            $v = $request->input($key);
            if ($v === null || $v === '') {
                $trimmed[$key] = null;
            } else {
                $trimmed[$key] = is_string($v) ? trim($v) : $v;
            }
        }
        $request->merge($trimmed);

        $data = $request->validate([
            'app_name' => ['nullable', 'string', 'max:120'],
            'app_url' => ['nullable', 'string', 'url', 'max:500'],
            'timezone' => ['nullable', 'timezone'],
            'locale' => ['nullable', 'string', 'max:16'],
            'ga4_measurement_id' => ['nullable', 'string', 'max:32', 'regex:/^$|^G-[A-Z0-9]+$/'],
        ]);

        $row = SiteSetting::query()->first();
        if ($row === null) {
            $row = new SiteSetting;
        }

        $row->fill([
            'app_name' => $data['app_name'],
            'app_url' => $data['app_url'],
            'timezone' => $data['timezone'],
            'locale' => $data['locale'],
            'ga4_measurement_id' => $data['ga4_measurement_id'],
        ]);
        $row->save();

        SiteSetting::forgetCache();
        ContentCache::forget();

        return redirect()->route('admin.site-settings.edit')->with('success', 'Site settings saved.');
    }
}
