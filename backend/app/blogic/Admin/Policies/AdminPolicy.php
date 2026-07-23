<?php

declare(strict_types=1);

namespace App\blogic\Admin\Policies;

use App\blogic\Accounts\Models\Account;

/**
 * Policy per l'accesso alle funzionalità admin.
 * Solo gli account con ruolo 'admin' possono gestire gli utenti,
 * con regole aggiuntive per il cambio ruolo.
 */
class AdminPolicy
{
    /**
     * Non-admin: accesso negato. Admin: prosegue con i singoli metodi.
     */
    public function before(Account $account, string $ability): ?bool
    {
        if ($account->role !== 'admin') {
            return false;
        }
        return null;
    }

    /**
     * Determina se l'account può visualizzare la lista utenti.
     */
    public function viewAny(Account $account): bool
    {
        return true;
    }

    /**
     * Determina se l'account può visualizzare un utente specifico.
     */
    public function view(Account $account, Account $target): bool
    {
        return true;
    }
    
    /**
     * Determina se l'account può modificare il ruolo di un utente.
     * Regole:
     * - Non puoi declassare te stesso.
     * - Non puoi declassare l'admin che ti ha promosso.
     */
    public function update(Account $current, Account $target): bool
    {
        if ($current->id === $target->id) {
            return false;
        }
        if ($current->promoted_by === $target->id && $target->role === 'admin') {
            return false;
        }
        return true;
    }
}