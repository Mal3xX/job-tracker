<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\blogic\Applications\Models\Application;
use App\blogic\Dashboard\Services\DashboardService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Aggiorna automaticamente a "nessuna risposta" le candidature
 * il cui stato non viene modificato da almeno 2 mesi.
 */
class UpdateNoResponseApplications extends Command
{
    /**
     * Nome e firma del comando.
     *
     * @var string
     */
    protected $signature = 'applications:update-no-response
                            {--dry-run : Mostra quante candidature verrebbero aggiornate senza modificare il database}';

    /**
     * Descrizione del comando.
     *
     * @var string
     */
    protected $description = 'Marca come "nessuna risposta" le candidature inattive da oltre 2 mesi';

    /**
     * Esegue il comando.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $cutoff = now()->subMonths(2);

        $query = Application::query()
            ->whereIn('status', ['pending', 'interview'])
            ->where('updated_at', '<', $cutoff);

        $count = $query->count();

        if ($count === 0) {
            $this->info('Nessuna candidatura da aggiornare.');
            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->info("{$count} candidature sarebbero aggiornate a \"nessuna risposta\".");
            return self::SUCCESS;
        }

        $affectedUserIds = $query->pluck('user_id')->unique()->all();

        $updated = DB::transaction(function () use ($query) {
            return $query->update([
                'status' => 'no_response',
                'status_changed_at' => now(),
            ]);
        });

        foreach ($affectedUserIds as $userId) {
            DashboardService::clearStatsCache((int) $userId);
        }

        $this->info("{$updated} candidature aggiornate a \"nessuna risposta\".");

        return self::SUCCESS;
    }
}
