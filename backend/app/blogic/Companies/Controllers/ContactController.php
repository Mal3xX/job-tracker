<?php

declare(strict_types=1);

namespace App\blogic\Companies\Controllers;

use App\Http\Controllers\Controller;
use App\blogic\Companies\Requests\StoreContactRequest;
use App\blogic\Companies\Requests\UpdateContactRequest;
use App\blogic\Companies\Resources\ContactIndexResource;
use App\blogic\Companies\Resources\ContactShowResource;
use App\blogic\Companies\Models\Contact;
use App\blogic\Companies\Models\Company;
use App\blogic\Companies\Services\ContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Controller per la gestione dei contatti aziendali.
 * Fornisce metodi CRUD per i contatti associati alle aziende.
 */
class ContactController extends Controller
{
    public function __construct(
        private ContactService $contactService
    ) {}

    /**
     * Restituisce tutti i contatti di un'azienda.
     *
     * @param Company $company
     * @return JsonResponse
     */
    public function index(Company $company): JsonResponse
    {
        $this->authorize('viewAny', Contact::class);
        $contacts = $this->contactService->getByCompany($company);
        return response()->json([
            'data' => ContactIndexResource::collection($contacts),
        ]);
    }

    /**
     * Crea un nuovo contatto per un'azienda.
     *
     * @param StoreContactRequest $request
     * @param Company $company
     * @return JsonResponse
     */
    public function store(StoreContactRequest $request, Company $company): JsonResponse
    {
        $this->authorize('create', Contact::class);
        $contact = $this->contactService->store($company, $request->validated(), $request->user());
        return response()->json([
            'data' => new ContactShowResource($contact),
            'message' => 'Contatto creato con successo.',
        ], 201);
    }

    /**
     * Aggiorna un contatto esistente.
     *
     * @param UpdateContactRequest $request
     * @param Contact $contact
     * @return JsonResponse
     */
    public function update(UpdateContactRequest $request, Contact $contact): JsonResponse
    {
        $this->authorize('update', $contact);
        $contact = $this->contactService->update($contact, $request->validated());
        return response()->json([
            'data' => new ContactShowResource($contact),
            'message' => 'Contatto aggiornato con successo.',
        ]);
    }

    /**
     * Elimina un contatto.
     *
     * @param Contact $contact
     * @return JsonResponse
     */
    public function destroy(Contact $contact): Response
    {
        $this->authorize('delete', $contact);
        $this->contactService->delete($contact);
        return response()->noContent();
    }
}
