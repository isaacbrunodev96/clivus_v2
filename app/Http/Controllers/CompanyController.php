<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:30',
        ]);

        $validated['user_id'] = Auth::id();
        $company = Company::create($validated);
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'ok',
                'company' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'cnpj' => $company->cnpj,
                ]
            ]);
        }

        return redirect()->route('profile.index')->with('success', 'Empresa cadastrada com sucesso!');
    }
}

