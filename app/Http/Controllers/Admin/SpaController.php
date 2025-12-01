<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SpaController extends Controller
{
    /**
     * Show the admin-facing Vue.js application shell.
     * This is protected by the 'auth' middleware in web.php.
     */
    public function showAdminApp()
    {
        // This just returns the single blade file that will mount your Vue admin app.
        return view('admin');
    }
}
