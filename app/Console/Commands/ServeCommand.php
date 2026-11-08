<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ServeCommand as BaseServeCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'serve', description: 'Inicia el servidor de desarrollo de Bella Girls con enlace directo y comprobación de puertos')]
class ServeCommand extends BaseServeCommand
{
    /**
     * Descripción del comando.
     *
     * @var string
     */
    protected $description = 'Inicia el servidor de desarrollo de Bella Girls con enlace directo y comprobación de puertos';

    /**
     * Ejecuta el comando de consola.
     */
    public function handle()
    {
        $this->liberarPuertoSiEstaOcupado();

        $host = $this->host();
        $port = $this->port();
        $url = "http://{$host}:{$port}";

        $this->newLine();
        $this->line("  <fg=bright-magenta;options=bold>🌸 BELLA GIRLS — SISTEMA DE GESTIÓN DE TIENDA DE ROPA</>");
        $this->line("  <fg=gray>─────────────────────────────────────────────────────────────────</>");
        $this->line("  <fg=green;options=bold>➜</>  <fg=white;options=bold>Acceso Principal:</>  <fg=bright-cyan;options=bold,underscore>{$url}/login</>");
        $this->line("  <fg=green;options=bold>➜</>  <fg=white;options=bold>Panel de Control:</>  <fg=bright-cyan;options=bold,underscore>{$url}/dashboard</>");
        $this->line("  <fg=gray>─────────────────────────────────────────────────────────────────</>");
        $this->line("  <fg=yellow;options=bold>Credenciales de prueba:</>");
        $this->line("    • <fg=bright-yellow>Administrador:</> admin    <fg=gray>|</>  <fg=bright-yellow>Contraseña:</> admin123");
        $this->line("    • <fg=bright-yellow>Vendedor:</>      vendedor <fg=gray>|</>  <fg=bright-yellow>Contraseña:</> vendedor123");
        $this->line("  <fg=gray>─────────────────────────────────────────────────────────────────</>");
        $this->newLine();

        return parent::handle();
    }

    /**
     * Si otro proceso en Windows (como PalmaSIG o un php huérfano) está bloqueando el puerto,
     * lo libera para que Bella Girls responda siempre en el enlace esperado.
     */
    protected function liberarPuertoSiEstaOcupado(): void
    {
        $port = (int) $this->port();

        if (PHP_OS_FAMILY === 'Windows') {
            $output = @shell_exec("netstat -ano | findstr :{$port}");
            if ($output && preg_match('/LISTENING\s+(\d+)/', $output, $matches)) {
                $pid = (int) $matches[1];
                $miPid = getmypid();

                if ($pid > 0 && $pid !== $miPid) {
                    $cmdOutput = @shell_exec("powershell -NoProfile -Command \"(Get-CimInstance Win32_Process -Filter 'ProcessId = {$pid}').CommandLine\" 2>nul");
                    
                    // Si el proceso no pertenece a este proyecto, cerrarlo para evitar colisiones
                    if ($cmdOutput && !str_contains($cmdOutput, 'laravel-tienda-ropa')) {
                        @shell_exec("taskkill /F /PID {$pid} 2>nul");
                        $this->components->warn("Se liberó el puerto {$port} (se cerró el proceso externo PID {$pid} que lo estaba ocupando).");
                        usleep(400000);
                    }
                }
            }
        }
    }
}
