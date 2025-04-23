<?php

namespace Framework\Middleware;

use Framework\Session;


class Authorize
{
    /**
     * Check is user is authenticated
     * 
     * @return bool
     */
    public function isAuthenticated()
    {
        return Session::has('user');
    }

    /**
     * Handle the user's redirection
     * 
     * @param string $role
     * @return bool
     */
    public function handle($role)
    {
        if ($role === 'guest' && $this->isAuthenticated()) {
            return redirct('/');
        } elseif ($role == 'auth' && !$this->isAuthenticated()) {
            return redirct('/auth/login');
        }
    }
}
