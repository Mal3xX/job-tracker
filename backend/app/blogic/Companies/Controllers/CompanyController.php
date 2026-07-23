<?php

declare(strict_types=1);

namespace App\blogic\Companies\Controllers;

use App\Http\Controllers\Controller;
use App\blogic\Companies\Models\Company;
use App\blogic\Companies\Requests\StoreCompanyRequest;
use App\blogic\Companies\Requests\UpdateCompanyRequest;
use App\blogic\Companies\Resources\CompanyIndexResource;
use App\blogic\Companies\Resources\CompanyShowResource;
use App\blogic\Companies\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Controller per la gestione delle aziende.
 * Fornisce metodi per CRUD completo su Companies.
 */
class CompanyController extends Controller
{
    public function __construct(
        private CompanyService $companyService
    ) {}

    /**
     * Restituisce lista paginata di tutte le aziende.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Company::class);
        $companies = $this->companyService->getAll($request->integer('per_page', 15));
        return response()->json([
            'data' => CompanyIndexResource::collection($companies),
            'meta' => [
                'current_page' => $companies->currentPage(),
                'last_page' => $companies->lastPage(),
                'per_page' => $companies->perPage(),
                'total' => $companies->total(),
            ]
        ]);
    }

    /**
     * Restituisce ID e nome di tutte le aziende (per autocomplete).
     */
    public function names(): JsonResponse
    {
        $this->authorize('viewAny', Company::class);
        return response()->json([
            'data' => $this->companyService->getNames()
        ]);
    }

    /**
     * Restituisce una singola azienda con tutti i dettagli.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $company = $this->companyService->getById($id);
        $this->authorize('view', $company);
        return response()->json([
            'data' => new CompanyShowResource($company),
        ]);
    }

    /**
     * Crea una nuova azienda.
     *
     * @param StoreCompanyRequest $request
     * @return JsonResponse
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $this->authorize('create', Company::class);
        $company = $this->companyService->store($request->validated(), $request->user());
        return response()->json([
            'data' => new CompanyShowResource($company),
            'message' => 'Azienda creata con successo.',
        ], 201);
    }

    /**
     * Aggiorna un'azienda esistente.
     *
     * @param UpdateCompanyRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateCompanyRequest $request, int $id): JsonResponse
    {
        $company = $this->companyService->getById($id);
        $this->authorize('update', $company);
        $company = $this->companyService->update($company, $request->validated());
        return response()->json([
            'data' => new CompanyShowResource($company),
            'message' => 'Azienda aggiornata con successo.',
        ]);
    }

    /**
     * Elimina un'azienda.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): Response
    {
        $company = $this->companyService->getById($id);
        $this->authorize('delete', $company);
        $this->companyService->delete($company);
        return response()->noContent();
    }
}
