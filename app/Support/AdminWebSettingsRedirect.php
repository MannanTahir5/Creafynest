<?php

namespace App\Support;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AdminWebSettingsRedirect
{
    public static function afterSave(Request $request, string $tab, string $message, string $legacyRoute): RedirectResponse
    {
        if ($request->input('return_to') === 'web-settings') {
            return redirect()
                ->route('admin.web-settings.edit', ['tab' => $tab])
                ->with('success', $message);
        }

        return redirect()->route($legacyRoute)->with('success', $message);
    }
}
