<?php

namespace App\Http\Controllers;

use App\Models\Financial;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    public function index(Request $request) {

        $user_id = $request->input('authenticated_user_id');

        $movimentacoes = Financial::where('user_id', $user_id)->get();

        return response()->json($movimentacoes);
    }

    public function store(Request $request) {

        $validatedData = $request->validate([
            'data' => 'required|date',
            'tipo' => 'required|in:entrada,saida',
            'valor' => 'required|numeric|min:0.01',
            'categoria' => 'required|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        $user_id = $request->input('authenticated_user_id');

        try {
            $financial = Financial::create([
                'data' => $validatedData['data'],
                'tipo' => $validatedData['tipo'],
                'valor' => $validatedData['valor'],
                'categoria' => $validatedData['categoria'],
                'descricao' => $validatedData['descricao'] ?? null,
                'user_id' => $user_id,
            ]);
        
            return response()->json([
                'message' => 'Movimentação cadastrada com sucesso', 
                'data' => $financial
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e
            ]. 500);
        }

    }

    public function update(Request $request, $id) {
        $financial = Financial::find($id);
    
        if (!$financial) {
            return response()->json(['error' => 'Movimentação não encontrada'], 404);
        }
    
        if ($financial->user_id !== $request->input('authenticated_user_id')) {
            return response()->json(['error' => 'Acesso não autorizado'], 403);
        }

        $validatedData = $request->validate([
            'data' => 'sometimes|date',
            'tipo' => 'sometimes|in:entrada,saida',
            'valor' => 'sometimes|numeric|min:0.01',
            'categoria' => 'sometimes|string|max:255',
            'descricao' => 'nullable|string',
        ]);
    
        $financial->update($validatedData);
    
        return response()->json([
            'message' => 'Movimentação atualizada com sucesso',
            'data' => $financial
        ], 200);
    }
    
    public function destroy(Request $request, $id) {
        $financial = Financial::find($id);
    
        if (!$financial) {
            return response()->json(['error' => 'Movimentação não encontrada'], 404);
        }
    
        if ($financial->user_id !== $request->input('authenticated_user_id')) {
            return response()->json(['error' => 'Acesso não autorizado'], 403);
        }

        $financial->delete();
    
        return response()->json(['message' => 'Movimentação deletada com sucesso'], 200);
    }

    public function getEntradasByUser(Request $request) {
        $user_id = $request->input('authenticated_user_id');
    
        $entradas = Financial::where('user_id', $user_id)
                             ->where('tipo', 'entrada')
                             ->get();
    
        return response()->json($entradas);
    }
    
    public function getSaidasByUser(Request $request) {
        $user_id = $request->input('authenticated_user_id');
    
        $saidas = Financial::where('user_id', $user_id)
                           ->where('tipo', 'saida')
                           ->get();
    
        return response()->json($saidas);
    }
    

}
