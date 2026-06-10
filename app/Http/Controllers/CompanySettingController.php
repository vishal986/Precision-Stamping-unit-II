<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\CompanySetting;

class CompanySettingController extends Controller
{
    public function edit()
    {
        $setting = CompanySetting::first() ?? new CompanySetting();
        return view('settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:100',
            'currency' => 'nullable|string|max:10',
        ]);

        $setting = CompanySetting::first();
        if ($setting) {
            $setting->update($validated);
        } else {
            CompanySetting::create($validated);
        }

        return redirect()->back()->with('success', 'Company settings updated successfully.');
    }
}
