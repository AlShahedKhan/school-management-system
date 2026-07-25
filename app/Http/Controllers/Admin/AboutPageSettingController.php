<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AboutPageSettingRequest;
use App\Models\AboutPageSetting;
use App\Support\AboutPageContentResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AboutPageSettingController extends Controller
{
    public function edit(AboutPageContentResolver $resolver): View
    {
        return view('admin.about.settings', [
            'settings' => AboutPageSetting::firstOrNew(['id' => 1]),
            'payload' => $resolver->editorPayload(),
        ]);
    }

    public function update(AboutPageSettingRequest $request): RedirectResponse
    {
        AboutPageSetting::query()->updateOrCreate(
            ['id' => 1],
            $request->validated()
        );

        return redirect()
            ->route('admin.about.settings.edit')
            ->with('success', 'About page settings updated successfully.');
    }
}
