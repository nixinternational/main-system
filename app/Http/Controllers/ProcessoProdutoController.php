<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProcessoProduto;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProcessoProdutoController extends Controller
{
    /**
     * Exclui em lote os ProcessoProduto informados por id.
     * Recebe: ids => [1,2,3]
     */
    public function batchDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'distinct', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $ids = $request->input('ids', []);

        $user = $request->user();
        try {
            $produtos = ProcessoProduto::with('processo.cliente')->whereIn('id', $ids)->get();

            foreach ($produtos as $produto) {
                $clienteId = $produto->processo->cliente_id ?? null;
                if ($clienteId !== null && $user && !$user->canAccessCliente($clienteId)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cliente não autorizado para este usuário.',
                    ], 403);
                }
            }

            $deleted = ProcessoProduto::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'deleted_count' => $deleted,
                'deleted_ids' => $ids,
            ]);
        } catch (\Exception $ex) {
            // log se necessário
            Log::error('Erro ao excluir processo produtos: '.$ex->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir produtos. Contate o administrador.',
            ], 500);
        }
    }
}
