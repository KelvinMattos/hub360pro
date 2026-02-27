<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Integration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File; // Necessário para ler logs

class SettingsController extends Controller
{
    /**
     * Exibe a tela de Logs do Sistema.
     */
    public function logs()
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];

        if (File::exists($logFile)) {
            // Lê o arquivo, pega as últimas 100 linhas e inverte para ver o mais recente primeiro
            $file = File::get($logFile);
            $logs = array_slice(array_reverse(explode("\n", $file)), 0, 200);
        }

        return view('settings.logs', compact('logs'));
    }

    public function integrations()
    {
        $company = Auth::user()->company;
        $integrations = $company->integrations->keyBy('platform');
        return view('settings.integrations', compact('integrations', 'company'));
    }

    public function updateKeys(Request $request, $platform)
    {
        $request->validate([
            'app_id' => 'required',
            'client_secret' => 'required',
        ]);

        Integration::updateOrCreate(
            [
                'company_id' => Auth::user()->company_id,
                'platform' => $platform
            ],
            [
                'app_id' => $request->app_id,
                'client_secret' => $request->client_secret,
                'status' => 'pending_auth' // Reset status until OAuth flow completes
            ]
        );

        return redirect()->back()->with('success', 'Credenciais salvas com sucesso! Agora clique em "Conectar".');
    }

    public function updateFinance(Request $request)
    {
        $user = Auth::user();
        $user->company->update([
            'tax_rate' => $request->tax_rate,
            'operational_rate' => $request->operational_rate
        ]);

        return redirect()->back()->with('success', 'Regras financeiras atualizadas!');
    }

    public function deleteIntegration($id)
    {
        $integration = Integration::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $integration->delete();
        return redirect()->back()->with('success', 'Integração removida.');
    }

    public function handleWebhook(Request $request, $platform)
    {
        Log::info("Webhook recebido de $platform", $request->all());
        return response()->json(['status' => 'received']);
    }
}