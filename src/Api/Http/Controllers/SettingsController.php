<?php

namespace Eav\Api\Http\Controllers;

class SettingsController extends Controller
{
    public function backendType()
    {
        return config('eav.fieldTypes', []);
    }

    public function frontendType()
    {
        return config('eav.elementTypes', []);
    }

    public function selectSources()
    {
        return config('eav.selectSources', []);
    }
}
