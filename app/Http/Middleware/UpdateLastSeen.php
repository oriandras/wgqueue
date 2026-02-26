<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A felhasználó utolsó aktivitásának idejét frissítő middleware.
 */
class UpdateLastSeen
{
    /**
     * Kezeli a bejövő kérést.
     *
     * Ha a felhasználó be van jelentkezve, frissíti az 'last_seen_at' mezőjét
     * a jelenlegi időpontra anélkül, hogy eseményeket váltana ki (updateQuietly).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            // Az utolsó aktivitás időpontjának frissítése események kiváltása nélkül
            auth()->user()->updateQuietly(['last_seen_at' => now()]);
        }

        return $next($request);
    }
}
