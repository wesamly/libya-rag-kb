<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpaController extends Controller
{
    /**
     * Show the public-facing Vue.js application shell.
     */
    public function showPublicApp()
    {
        // This just returns the single blade file that will mount your Vue app.
        return view('public');
    }

    /**
     * (For later) Show the admin-facing Vue.js application shell.
     */
    // public function showAdminApp()
    // {
    //     return view('admin');
    // }
}
