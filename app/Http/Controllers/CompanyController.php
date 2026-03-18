<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Super Admin não possui limites de empresas
        if (!$user->isSuperAdmin()) {
            $subscription = $user->activeSubscription();
            
            if (!$subscription || !$subscription->plan) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['status' => 'error', 'message' => 'Você precisa de um plano ativo para cadastrar empresas.'], 403);
                }
                return back()->with('error', 'Você precisa de um plano ativo para cadastrar empresas.');
            }

            $maxCompanies = $subscription->plan->max_companies ?? 1;
            
            // Verifica se o usuário possui o módulo Multi-empresa (que libera o limite)
            if ($user->hasModuleAccess('multi-company')) {
                $maxCompanies = 999; // Ilimitado para fins práticos
            }
            
            if ($user->companies()->count() >= $maxCompanies) {
                $message = "Seu plano permite o cadastro de no máximo {$maxCompanies} " . ($maxCompanies == 1 ? 'empresa' : 'empresas') . ".";
                $message .= " Faça um upgrade ou adquira o módulo Multi-Empresa na nossa loja.";
                
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['status' => 'error', 'message' => $message], 403);
                }
                return back()->with('error', $message);
            }
        }

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

