<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GovernorateResource;
use App\Models\Governorate;

class LocationController extends Controller
{
    public function governorates()
    {
        $governorates = Governorate::all();
        return GovernorateResource::collection($governorates);
    }
}
