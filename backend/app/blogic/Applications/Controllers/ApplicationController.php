<?php

declare(strict_types=1);

namespace App\blogic\Applications\Controllers;

use App\Http\Controllers\Controller;
use App\blogic\Applications\Models\Application;
use App\blogic\Applications\Requests\StoreApplicationRequest;
use App\blogic\Applications\Requests\UpdateApplicationRequest;
use App\blogic\Applications\Resources\ApplicationIndexResource;
use App\blogic\Applications\Resources\ApplicationShowResource;
use App\blogic\Applications\Resources\PendingCompanyResource;
use App\blogic\Applications\Services\ApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Controller per la gestione delle candidature.
 * Fornisce metodi per CRUD completo su Applications.
 * Gestisce automaticamente status_changed_at al cambio di stato.
 */
class ApplicationController extends Controller
{
    public function __construct(
        private ApplicationService $applicationService
    ) {}

    /**
     * Restituisce lista paginata di tutte le candidature dell'utente.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Application::class);
        $applications = $this->applicationService->getAll(
            $request->user(),
            $request->integer('per_page', 15),
            $request->integer('company_id')
        );
        return response()->json([
            'data' => ApplicationIndexResource::collection($applications),
            'meta' => [
                'current_page' => $applications->currentPage(),
                'last_page' => $applications->lastPage(),
                'per_page' => $applications->perPage(),
                'total' => $applications->total(),
            ]
        ]);
    }

    /**
     * Restituisce una singola candidatura con tutti i dettagli.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $application = $this->applicationService->getById($request->user(), $id);
        $this->authorize('view', $application);
        return response()->json([
            'data' => new ApplicationShowResource($application),
        ]);
    }

    /**
     * Crea una nuova candidatura.
     *
     * @param StoreApplicationRequest $request
     * @return JsonResponse
     */
    public function store(StoreApplicationRequest $request): JsonResponse
    {
        $this->authorize('create', Application::class);
        $application = $this->applicationService->store($request->validated(), $request->user());
        return response()->json([
            'data' => new ApplicationShowResource($application),
            'message' => 'Candidatura creata con successo.',
        ], 201);
    }

    /**
     * Aggiorna una candidatura esistente.
     *
     * @param UpdateApplicationRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateApplicationRequest $request, int $id): JsonResponse
    {
        $application = $this->applicationService->getById($request->user(), $id);
        $this->authorize('update', $application);
        $application = $this->applicationService->update($application, $request->validated());
        return response()->json([
            'data' => new ApplicationShowResource($application),
            'message' => 'Candidatura aggiornata con successo.',
        ]);
    }

    /**
     * Elimina una candidatura.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request, int $id): Response
    {
        $application = $this->applicationService->getById($request->user(), $id);
        $this->authorize('delete', $application);
        $this->applicationService->delete($application);
        return response()->noContent();
    }

    /**
     * Restituisce l'elenco dei nomi azienda inseriti nelle candidature
     * ma non ancora collegati ad aziende reali (company_id IS NULL).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function pendingCompanies(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Application::class);
        $pending = $this->applicationService->getPendingCompanies($request->user());
        return response()->json([
            'data' => PendingCompanyResource::collection($pending),
        ]);
    }

    /**
     * Restituisce le piattaforme distinte usate nelle candidature dell'utente.
     * Usato per popolare la tendina di autocomplete nel form candidatura.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function platforms(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Application::class);
        return response()->json([
            'data' => $this->applicationService->getPlatforms($request->user()),
        ]);
    }
}
