<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserSelectionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:cpf,cnpj',
            'company_id' => 'nullable|integer',
        ]);

        session(['selected_entity_type' => $data['type']]);
        session(['selected_company_id' => $data['company_id'] ?? null]);

        return response()->json([
            'status' => 'ok',
            'type' => $data['type'],
            'company_id' => $data['company_id'] ?? null,
        ]);
    }
}

