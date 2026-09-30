<?php

namespace App\Support;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AdminHomeEditorRedirect
{
    public static function afterSave(Request $request, string $section, string $message, string $legacyRoute): RedirectResponse
    {
        if ($request->input('return_to') === 'home-page') {
            return redirect()
                ->route('admin.home-page.edit', ['section' => $section])
                ->with('success', $message);
        }

        return redirect()->route($legacyRoute)->with('success', $message);
    }
}
