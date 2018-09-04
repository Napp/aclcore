<?php

namespace Napp\Core\Acl\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Napp\Core\Api\Exceptions\Exceptions\AuthorizationException;

/**
 * Class Authorize
 * @package Napp\Core\Acl\Middleware
 */
class Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|array $ability
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     * @throws \Napp\Core\Api\Exceptions\Exceptions\AuthorizationException
     */
    public function handle($request, Closure $next, ...$ability)
    {
        $user = auth(config('acl.guard'))->user();

        if (null === $user) {
            throw new AuthenticationException;
        }

        if (false === acl($ability)) {
            throw new AuthorizationException;
        }

        return $next($request);
    }
}
