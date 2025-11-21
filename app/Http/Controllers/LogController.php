<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LogController extends Controller
{
    /**
     * Exibir a página de logs
     */
    public function index(Request $request)
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];
        $totalLines = 0;
        $filteredTotal = 0;
        $currentPage = (int) $request->get('page', 1);
        $perPage = 100; // Linhas por página
        $search = $request->get('search', '');

        if (File::exists($logFile) && File::isReadable($logFile)) {
            try {
                // Para arquivos grandes, ler linha por linha
                $file = fopen($logFile, 'r');
                $allLines = [];
                
                if ($file) {
                    while (($line = fgets($file)) !== false) {
                        $allLines[] = rtrim($line, "\r\n");
                    }
                    fclose($file);
                }
                
                $totalLines = count($allLines);

                // Filtrar por busca se houver
                if (!empty($search)) {
                    $filteredLines = array_filter($allLines, function($line) use ($search) {
                        return stripos($line, $search) !== false;
                    });
                    $allLines = array_values($filteredLines); // Reindexar array
                    $filteredTotal = count($allLines);
                } else {
                    $filteredTotal = $totalLines;
                }

                // Paginação reversa (mais recentes primeiro)
                $allLines = array_reverse($allLines);
                $offset = ($currentPage - 1) * $perPage;
                $logs = array_slice($allLines, $offset, $perPage);
            } catch (\Exception $e) {
                // Se houver erro ao ler o arquivo, retornar vazio
                $logs = [];
            }
        }

        return view('logs.index', compact('logs', 'totalLines', 'filteredTotal', 'currentPage', 'perPage', 'search'));
    }

    /**
     * Limpar os logs
     */
    public function clear(Request $request)
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            
            if (File::exists($logFile)) {
                File::put($logFile, '');
            }

            return response()->json([
                'success' => true,
                'message' => 'Logs limpos com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao limpar logs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download do arquivo de log
     */
    public function download()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (!File::exists($logFile)) {
            return redirect()->route('logs.index')
                ->with('messages', ['error' => ['Arquivo de log não encontrado!']]);
        }

        return response()->download($logFile, 'laravel-' . date('Y-m-d-His') . '.log');
    }
}

