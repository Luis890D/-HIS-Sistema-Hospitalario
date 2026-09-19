<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AppointmentScheduleConflictException extends Exception
{
    public function __construct(
        string $message,
        protected string $field = 'doctor_id'
    ) {
        parent::__construct($message, 409);
    }

    /**
     * Renderiza la excepción como respuesta HTTP 409 Conflict para la API
     * o redirección con errores para peticiones web tradicionales.
     */
    public function render(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'status'  => 409,
                'error'   => 'Conflict',
                'message' => $this->getMessage(),
                'errors'  => [
                    $this->field => [$this->getMessage()]
                ]
            ], 409);
        }

        return redirect()
            ->back()
            ->withInput()
            ->withErrors([
                $this->field => $this->getMessage()
            ]);
    }
}
