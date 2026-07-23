<?php

declare(strict_types=1);

namespace App\blogic\Admin\Controllers;

use App\blogic\Accounts\Models\Account;
use App\blogic\Admin\Services\AdminUserService;
use App\blogic\Admin\Resources\AdminUserIndexResource;
use App\blogic\Admin\Resources\AdminUserShowResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller per la gestione admin degli utenti.
 * Delega la logica di business ad AdminUserService.
 */
class AdminUserController extends Controller
{
    public function __construct(
        private AdminUserService $adminUserService
    ) {}

    /**
     * Restituisce lista paginata di tutti gli utenti
     * con ultimo accesso e sessioni attive.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Account::class);
        $accounts = $this->adminUserService->getAll($request->integer('per_page', 15));
        return response()->json([
            'data' => AdminUserIndexResource::collection($accounts),
            'meta' => [
                'current_page' => $accounts->currentPage(),
                'last_page' => $accounts->lastPage(),
                'per_page' => $accounts->perPage(),
                'total' => $accounts->total(),
            ],
        ]);
    }

    /**
     * Restituisce il dettaglio di un singolo utente
     * con profilo, sessioni attive e storico accessi.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $data = $this->adminUserService->getById($id);
        $this->authorize('view', $data['account']);
        return response()->json([
            'data' => new AdminUserShowResource($data['account'], $data['sessions'], $data['loginHistory']),
        ]);
    }

    /**
     * Aggiorna il ruolo di un utente.
     * Se promosso ad admin, registra chi ha effettuato la promozione.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'in:admin,user'],
        ]);
        $account = $this->adminUserService->getById($id)['account'];
        $this->authorize('update', $account);
        $this->adminUserService->updateRole($id, $validated['role'], $request->user()->id);
        return response()->json(['message' => 'Role updated.']);
    }
}
