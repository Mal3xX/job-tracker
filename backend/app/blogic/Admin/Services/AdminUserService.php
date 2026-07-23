<?php

declare(strict_types=1);

namespace App\blogic\Admin\Services;

use App\blogic\Accounts\Models\Account;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Service per la logica di gestione utenti admin.
 * Gestisce recupero lista, dettaglio e modifica ruolo utenti.
 */
class AdminUserService
{
    /**
     * Recupera tutti gli account con profilo utente, ultimo accesso
     * e numero sessioni attive. Paginato.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return Account::with('user')
            ->select('accounts.*')
            ->selectSub(
                DB::table('login_histories')
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('login_histories.user_id', 'accounts.id'),
                'last_login_at'
            )
            ->selectSub(
                DB::table('sessions')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('sessions.user_id', 'accounts.id'),
                'session_count'
            )
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Recupera un singolo account con profilo utente e sessioni attive.
     *
     * @param int $id
     * @return array{account: Account, sessions: \Illuminate\Support\Collection, loginHistory: \Illuminate\Support\Collection}
     */
    public function getById(int $id): array
    {
        $account = Account::with('user')->findOrFail($id);
        $sessions = DB::table('sessions')
            ->where('user_id', $id)
            ->orderBy('last_activity', 'desc')
            ->get(['id', 'ip_address', 'user_agent', 'last_activity']);
        $loginHistory = DB::table('login_histories')
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'ip_address', 'user_agent', 'created_at']);
        return [
            'account'      => $account,
            'sessions'     => $sessions,
            'loginHistory' => $loginHistory,
        ];
    }
    
    /**
     * Cambia il ruolo di un account.
     * Quando promuovi a admin, registra chi ha effettuato la promozione.
     * Quando declassi a user, azzera il riferimento.
     *
     * @param int $id
     * @param string $role
     * @param int|null $promotedBy
     * @return Account
     */
    public function updateRole(int $id, string $role, ?int $promotedBy = null): Account
    {
        $account = Account::findOrFail($id);
        $account->role = $role;
        $account->promoted_by = $role === 'admin' ? $promotedBy : null;
        $account->save();
        return $account;
    }
}