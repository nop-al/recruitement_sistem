<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Keys that should be excluded from sanitization (e.g., passwords, tokens).
     *
     * @var array<int, string>
     */
    protected array $except = [
        'password',
        'password_confirmation',
        'current_password',
        '_token',
    ];

    /**
     * Handle an incoming request by sanitizing string inputs against XSS.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();

        if (! empty($input)) {
            $request->merge($this->sanitizeArray($input));
        }

        return $next($request);
    }

    /**
     * Recursively sanitize input array values.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function sanitizeArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->except, true)) {
                continue;
            }

            if (is_array($value)) {
                $data[$key] = $this->sanitizeArray($value);
            } elseif (is_string($value)) {
                // Strip tags to prevent XSS injection, trim extraneous whitespace
                $clean = strip_tags($value);
                $data[$key] = trim($clean);
            }
        }

        return $data;
    }
}
