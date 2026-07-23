<?php

declare(strict_types=1);

namespace App\blogic\Accounts\Controllers;

use App\blogic\Accounts\Requests\LoginRequest;
use App\blogic\Accounts\Requests\RegisterRequest;
use App\blogic\Accounts\Requests\UpdateProfileRequest;
use App\blogic\Accounts\Services\AccountService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * Controller per la gestione dell'autenticazione.
 * Delega la logica di business ad AccountService.
 */
class AccountController extends Controller
{
    public function __construct(
        private AccountService $accountService
    ) {}

    /**
     * Autentica l'utente e restituisce il token API.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = $this->accountService->login(
                $request->validated('email'),
                $request->validated('password')
            );
        } catch (RuntimeException) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        return response()->json(['data' => $data]);
    }

    /**
     * Registra un nuovo account e utente.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->accountService->register($request->validated());

        return response()->json(['data' => $data], 201);
    }

    /**
     * Revoca il token API dell'utente corrente.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $this->accountService->logout($request->user());
        return response()->json(['message' => 'Logged out']);
    }

    /**
     * Restituisce i dati dell'utente corrente autenticato.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $data = $this->accountService->me($request->user());
        return response()->json(['data' => $data]);
    }

    /**
     * Aggiorna il profilo dell'utente autenticato (email, password, avatar).
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $data = $this->accountService->updateProfile($request->user(), $request->validated());
        return response()->json(['data' => $data]);
    }
}
