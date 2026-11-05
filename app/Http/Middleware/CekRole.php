<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CekRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if ($request->user() === null) {
            return redirect()->route('login');
        }

        if ($request->user()->role !== $role) {
            $homeRoute = match ($request->user()->role) {
                'admin' => 'admin',
                'kasir' => 'kasir',
                default => null,
            };

            if ($homeRoute !== null) {
                return redirect()->route($homeRoute);
            }

            abort(403, 'Akses ditolak! Anda tidak memiliki hak akses.');
        }

        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        Log::info('Request selesai', [
            'path' => $request->path(),
            'status' => $response->getStatusCode(),
        ]);
    }
}