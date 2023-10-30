<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeneralController extends Controller
{
    /**
     * Get application settings.
     *
     * @return array
     */
    public function getAppSettings()
    {
        // Retrieve the application settings from the configuration.
        return config('settings');
    }
}
