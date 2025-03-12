<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Financial;

class InsightController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->input('authenticated_user_id');

        // Total de entradas e saídas
        $totalEntradas = Financial::where('user_id', $userId)
            ->where('tipo', 'entrada')
            ->sum('valor');

        $totalSaidas = Financial::where('user_id', $userId)
            ->where('tipo', 'saida')
            ->sum('valor');

        // Saldo final
        $saldo = $totalEntradas - $totalSaidas;

        // Contagem de movimentações por categoria
        $movimentacoesPorCategoria = Financial::where('user_id', $userId)
            ->selectRaw('categoria, COUNT(*) as total')
            ->groupBy('categoria')
            ->get();

        // Total de movimentações
        $totalMovimentacoes = Financial::where('user_id', $userId)->count();

        // Percentual de entradas e saídas
        $totalEntradasCount = Financial::where('user_id', $userId)->where('tipo', 'entrada')->count();
        $totalSaidasCount = Financial::where('user_id', $userId)->where('tipo', 'saida')->count();

        $percentEntradas = $totalMovimentacoes > 0 ? ($totalEntradasCount / $totalMovimentacoes) * 100 : 0;
        $percentSaidas = $totalMovimentacoes > 0 ? ($totalSaidasCount / $totalMovimentacoes) * 100 : 0;

        return response()->json([
            'totalEntradas' => $totalEntradas,
            'totalSaidas' => $totalSaidas,
            'saldo' => $saldo,
            'movimentacoesPorCategoria' => $movimentacoesPorCategoria,
            'percentEntradas' => round($percentEntradas, 2),
            'percentSaidas' => round($percentSaidas, 2),
        ]);
    }
}
