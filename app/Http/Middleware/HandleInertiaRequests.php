<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{

    protected $rootView = 'app';


    public function version(Request $request)
    {
        return parent::version($request);
    }


    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'auth.user' => fn() => $request->user()
                ? $request->user()->only('id', 'first_name', 'last_name', 'email')
                : null,
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
            ],
            'showingMobileMenu' => false,
        ]);
    }
}
