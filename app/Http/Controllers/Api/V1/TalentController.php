<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TalentResource;
use App\Models\Talent;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TalentController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return TalentResource::collection(Talent::query()->orderBy('name')->get());
    }
}
