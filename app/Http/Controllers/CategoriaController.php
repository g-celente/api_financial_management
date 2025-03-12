<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Models\Financial;

class CategoriaController extends Controller
{
    public function index(Request $request) {
        $user_id = $request->input('authenticated_user_id');
        $categories = Categoria::where('user_id', $user_id)->get();
        return response()->json($categories, 200);
    }

    public function store(Request $request) {
        $user_id = $request->input('authenticated_user_id');
    
        $values = $request->validate([
            'nome' => 'required|string|max:255'
        ]);
    
        $category = Categoria::create([
            'nome' => $values['nome'],
            'user_id' => $user_id
        ]);
    
        return response()->json($category, 201);
    }

    public function destroy(Request $request, $id) {
        $user_id = $request->input('authenticated_user_id');
        
        $category = Categoria::where('id', $id)->where('user_id', $user_id)->first();
        
        if (!$category) {
            return response()->json(['message' => 'Categoria não encontrada ou sem permissão para excluir.'], 404);
        }

        $financialExists = Financial::where('category_id', $id)->exists();
    
        if ($financialExists) {
            return response()->json(['message' => 'Não é possível excluir uma categoria em uso.'], 400);
        }

        $category->delete();
    
        return response()->json(['message' => 'Categoria excluída com sucesso.'], 200);
    }
    
}
