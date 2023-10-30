<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function getAppSettings() {
        return config('settings');
    }
}
