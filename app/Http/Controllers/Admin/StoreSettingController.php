<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSettingRequest;
use App\Models\StoreSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class StoreSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings', ['store' => StoreSetting::current()]);
    }

    public function update(StoreSettingRequest $request): RedirectResponse
    {
        StoreSetting::current()->update($request->validated());

        return redirect()->route('admin.settings.edit')->with('status', 'Informasi toko berhasil diperbarui.');
    }
}
