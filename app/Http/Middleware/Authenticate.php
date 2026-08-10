<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        session()->flash('error', 'Please log in first.');

        if ($request->is('superadmin*')) {
            return route('superadmin.login.page');
        }

        if ($request->is('admin*')) {
            return route('admin.login.page');
        }

        if ($request->is('elementary*')) {
            return route('elementary.login.page');
        }

        if ($request->is('junior-high-school*')) {
            return route('junior_high_school.login.page');
        }

        if ($request->is('senior-high-school*')) {
            return route('senior_high_school.login.page');
        }

        if ($request->is('college*')) {
            return route('college.login.page');
        }

        return route('superadmin.login.page');
    }
}

