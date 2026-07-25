<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SmsPackage;
use Illuminate\Http\Request;

class SmsPackageController extends Controller
{
    public function index(Request $request)
    {
        $query = SmsPackage::query();

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'sms_quantity' => 'required|integer',
            'validity_days' => 'required|integer',
            'purchase_price' => 'required|numeric',
            'sale_price' => 'required|numeric',
        ]);

        $package = SmsPackage::create($request->all());

        return response()->json($package);
    }

    public function show(SmsPackage $smsPackage)
    {
        return response()->json($smsPackage);
    }

    public function update(Request $request, SmsPackage $smsPackage)
    {
        $smsPackage->update($request->all());
        return response()->json($smsPackage);
    }

    public function destroy(SmsPackage $smsPackage)
    {
        $smsPackage->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
