<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminSmsCredential;
use Illuminate\Support\Facades\Validator;

class AdminSmsCredentialController extends Controller
{
    public function index()
    {
        $credential = AdminSmsCredential::where('is_active', true)->first()
            ?? AdminSmsCredential::first()
            ?? new AdminSmsCredential([
                'provider_name' => 'Greenweb',
                'api_url' => 'https://api.greenweb.com.bd/api.php',
                'is_active' => true,
            ]);

        return view('admin.sms.credentials', compact('credential'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider_name' => 'required|string|max:255',
            'api_key' => 'required|string|max:255',
            'sender_id' => 'nullable|string|max:100',
            'api_url' => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $credential = AdminSmsCredential::firstOrNew(['id' => $request->id]);
        $credential->fill([
            'provider_name' => $request->provider_name,
            'api_key' => $request->api_key,
            'sender_id' => $request->sender_id,
            'api_url' => $request->api_url ?: 'https://api.greenweb.com.bd/api.php',
            'is_active' => $request->boolean('is_active', true),
        ]);
        $credential->save();

        return redirect()->back()->with('success', 'SMS Credentials updated successfully.');
    }
}
