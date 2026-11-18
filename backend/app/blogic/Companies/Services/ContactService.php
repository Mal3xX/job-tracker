<?php

declare(strict_types=1);

namespace App\blogic\Companies\Services;

use App\blogic\Companies\Models\Company;
use App\blogic\Companies\Models\Contact;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service per la logica di business dei contatti aziendali.
 * Gestisce creazione, aggiornamento, eliminazione e recupero dati.
 */
class ContactService
{
    /**
     * Recupera tutti i contatti di un'azienda.
     *
     * @param Company $company
     * @return Collection
     */
    public function getByCompany(Company $company): Collection
    {
        return $company->contacts;
    }

    /**
     * Crea un nuovo contatto per un'azienda.
     * Il contatto appartiene sempre al proprietario dell'azienda.
     *
     * @param Company $company
     * @param array $data
     * @return Contact
     */
    public function store(Company $company, array $data): Contact
    {
        $contact = $company->contacts()->make($data);
        $contact->creator_id = $company->creator_id;
        $contact->save();

        return $contact;
    }

    /**
     * Aggiorna un contatto esistente.
     *
     * @param Contact $contact
     * @param array $data
     * @return Contact
     */
    public function update(Contact $contact, array $data): Contact
    {
        $contact->update($data);
        return $contact->fresh();
    }

    /**
     * Elimina un contatto.
     *
     * @param Contact $contact
     */
    public function delete(Contact $contact): void
    {
        $contact->delete();
    }
}
