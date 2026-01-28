<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProcessoAereoProduto;
use App\Models\ProcessoProduto;
use App\Models\ProcessoRodoviarioProduto;
use App\Services\Auditoria\ProcessoAuditService;
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
        $tipoProcesso = $request->input('tipo_processo');
        if (!$tipoProcesso) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de processo não informado.',
            ], 422);
        }

        $user = $request->user();
        try {
            $modelClass = ProcessoProduto::class;
            $relation = 'processo';
            $defaultProcessType = 'maritimo';

            if ($tipoProcesso === 'aereo') {
                $modelClass = ProcessoAereoProduto::class;
                $relation = 'processoAereo';
                $defaultProcessType = 'aereo';
            } elseif ($tipoProcesso === 'rodoviario') {
                $modelClass = ProcessoRodoviarioProduto::class;
                $relation = 'processoRodoviario';
                $defaultProcessType = 'rodoviario';
            } elseif ($tipoProcesso !== 'maritimo') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de processo inválido.',
                ], 422);
            }

            $produtos = $modelClass::with($relation . '.cliente')->whereIn('id', $ids)->get();

            foreach ($produtos as $produto) {
                $processo = $produto->$relation ?? null;
                $clienteId = $processo?->cliente_id ?? null;
                if ($clienteId !== null && $user && !$user->canAccessCliente($clienteId)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cliente não autorizado para este usuário.',
                    ], 403);
                }
            }

            $deleted = $modelClass::whereIn('id', $ids)->delete();
            $auditService = app(ProcessoAuditService::class);
            foreach ($produtos as $produto) {
                $processo = $produto->$relation ?? null;
                $auditService->logDelete([
                    'auditable_type' => $modelClass,
                    'auditable_id' => $produto->id,
                    'process_type' => $processo->tipo_processo ?? $defaultProcessType,
                    'process_id' => $processo?->id ?? null,
                    'client_id' => $processo?->cliente_id ?? null,
                    'context' => 'processo.produto.delete',
                ], $produto->getAttributes());
            }

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
