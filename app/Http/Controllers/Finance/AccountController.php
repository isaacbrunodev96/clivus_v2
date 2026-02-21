<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Account::where('user_id', Auth::id())
            ->where('active', true)
            ->orderBy('created_at', 'desc');

        // Apply CNPJ selection filter if set
        $selectedType = session('selected_entity_type', null);
        $selectedCompany = session('selected_company_id', null);
        if ($selectedType === 'cnpj' && $selectedCompany) {
            $query->where('company_id', $selectedCompany);
        }

        $accounts = $query->get();

        return view('finance.accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('finance.accounts.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'bank' => 'nullable|string|max:255',
            'agency' => 'nullable|string|max:50',
            'account_number' => 'nullable|string|max:50',
            'holder' => 'nullable|string|max:255',
            'cpf' => 'nullable|string|max:20',
            'company_id' => 'nullable|exists:companies,id',
            'pix_key' => 'nullable|string|max:255',
            'balance' => 'nullable|numeric|min:0',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['balance'] = $validated['balance'] ?? 0;
        // Enforce separation CPF vs CNPJ based on selection (session)
        $selectedType = session('selected_entity_type', null);
        $selectedCompany = session('selected_company_id', null);

        if ($selectedType === 'cnpj') {
            // For CNPJ selection we must associate with a company
            $companyId = $validated['company_id'] ?? $selectedCompany;
            if (empty($companyId)) {
                return redirect()->back()->withErrors(['company_id' => 'Selecione ou cadastre uma empresa (CNPJ) antes de criar conta empresarial.'])->withInput();
            }
            $validated['company_id'] = $companyId;
            // Clear CPF when creating company-associated account
            $validated['cpf'] = null;
        } else {
            // CPF/default: ensure no company is attached
            $validated['company_id'] = null;
        }

        Account::create($validated);

        return redirect()->route('finance.accounts.index')
            ->with('success', 'Conta criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        Gate::authorize('view', $account);
        return view('finance.accounts.show', compact('account'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        Gate::authorize('update', $account);
        return redirect()->route('finance.accounts.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Account $account)
    {
        Gate::authorize('update', $account);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'bank' => 'nullable|string|max:255',
            'agency' => 'nullable|string|max:50',
            'account_number' => 'nullable|string|max:50',
            'holder' => 'nullable|string|max:255',
            'cpf' => 'nullable|string|max:20',
            'pix_key' => 'nullable|string|max:255',
            'balance' => 'nullable|numeric|min:0',
        ]);

        // Enforce separation CPF vs CNPJ on update too
        $selectedType = session('selected_entity_type', null);
        $selectedCompany = session('selected_company_id', null);
        if ($selectedType === 'cnpj') {
            $validated['company_id'] = $validated['company_id'] ?? $selectedCompany;
            if (empty($validated['company_id'])) {
                return redirect()->back()->withErrors(['company_id' => 'Selecione uma empresa (CNPJ) antes de associar esta conta.'])->withInput();
            }
            $validated['cpf'] = null;
        } else {
            $validated['company_id'] = null;
        }

        $account->update($validated);

        return redirect()->route('finance.accounts.index')
            ->with('success', 'Conta atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        Gate::authorize('delete', $account);
        $account->update(['active' => false]);
        
        return redirect()->route('finance.accounts.index')
            ->with('success', 'Conta removida com sucesso!');
    }
}
