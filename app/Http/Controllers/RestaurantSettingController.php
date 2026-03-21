<?php

namespace App\Http\Controllers;

use App\Actions\Settings\GetRestaurantSettings;
use App\Actions\Settings\UpdateRestaurantSettings;
use App\Http\Requests\RestaurantSettingRequest;
use Inertia\Inertia;

class RestaurantSettingController extends Controller
{
    public function index(GetRestaurantSettings $getRestaurantSettings)
    {
        return Inertia::render('Settings/Index', $getRestaurantSettings->handle());
    }

    public function update(RestaurantSettingRequest $request, UpdateRestaurantSettings $updateRestaurantSettings)
    {
        $result = $updateRestaurantSettings->handle($request->validated());

        if (! $result['ok']) {
            return redirect()
                ->route('settings.index')
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('settings.index')
            ->with('success', $result['message']);
    }
}
