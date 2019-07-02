<?php

namespace Napp\Core\Acl\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Napp\Core\Api\Exceptions\Exceptions\AuthorizationException;

/**
 * Class Authorize.
 */
class Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|array             $ability
     *
     * @throws \Illuminate\Auth\AuthenticationException
     * @throws \Napp\Core\Api\Exceptions\Exceptions\AuthorizationException
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$ability)
    {
        $user = auth(config('acl.guard'))->user();

        if (null === $user) {
            throw new AuthenticationException();
        }

        if (false === acl($ability)) {
            throw new AuthorizationException();
        }

        return $next($request);
    }
}
