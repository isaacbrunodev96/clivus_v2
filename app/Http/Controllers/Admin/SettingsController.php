<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'asaas_api_key' => 'nullable|string',
            'asaas_sandbox' => 'nullable|string', // Checkbox vem como "1" ou null
            'mail_mailer' => 'nullable|string',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'nullable|email',
            'mail_from_name' => 'nullable|string',
            'mercadopago_access_token' => 'nullable|string',
            'mercadopago_public_key' => 'nullable|string',
            'mercadopago_webhook_token' => 'nullable|string',
        ]);

        try {
            // Atualizar configurações do Asaas
            if ($request->has('asaas_api_key')) {
                $this->updateEnv('ASAAS_API_KEY', $validated['asaas_api_key']);
            }
            
            // Checkbox: se não vier no request, é false
            $sandboxValue = $request->has('asaas_sandbox') ? 'true' : 'false';
            $this->updateEnv('ASAAS_SANDBOX', $sandboxValue);

            // Atualizar outras configurações
            foreach ($validated as $key => $value) {
                if (str_starts_with($key, 'mail_') || str_starts_with($key, 'mercadopago_')) {
                    if ($value !== null) {
                        $envKey = strtoupper($key);
                        $this->updateEnv($envKey, $value);
                    }
                }
            }

            // Limpar cache de configuração para aplicar mudanças
            if (app()->environment('production')) {
                \Illuminate\Support\Facades\Artisan::call('config:clear');
                \Illuminate\Support\Facades\Artisan::call('config:cache');
            } else {
                \Illuminate\Support\Facades\Artisan::call('config:clear');
            }

            return redirect()->route('admin.settings.index')
                ->with('success', 'Configurações atualizadas com sucesso!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao salvar configurações: ' . $e->getMessage());
            return redirect()->route('admin.settings.index')
                ->with('error', 'Erro ao salvar configurações: ' . $e->getMessage());
        }
    }

    private function updateEnv($key, $value)
    {
        $envFile = base_path('.env');
        if (!file_exists($envFile)) {
            \Illuminate\Support\Facades\Log::error("SettingsController: .env file not found at $envFile");
            return;
        }

        // Se o valor contiver espaços ou caracteres especiais, envolver em aspas
        if (preg_match('/\s/', $value) || str_contains($value, '$')) {
            $value = '"' . str_replace('"', '\"', $value) . '"';
        }

        $envContent = file_get_contents($envFile);
        $lines = explode("\n", $envContent);
        $found = false;

        foreach ($lines as $i => $line) {
            if (str_starts_with($line, "{$key}=")) {
                $lines[$i] = "{$key}={$value}";
                $found = true;
                break;
            }
        }

        if (!$found) {
            $lines[] = "{$key}={$value}";
        }

        $newContent = implode("\n", $lines);
        
        if (file_put_contents($envFile, $newContent) === false) {
            \Illuminate\Support\Facades\Log::error("SettingsController: Failed to write to .env file. Check permissions.");
            throw new \Exception("Erro ao salvar no arquivo .env. Verifique as permissões de escrita.");
        }
    }
}
