<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Content\Temple\Temple;
use Illuminate\View\View;

class TempleController extends Controller
{
    public function show(Temple $temple): View
    {
        $temple->load([
            'content.categories',
            'content.mediaUsages.media',
            'address',
            'stat',
            'openingHours',
            'fees',
            'highlights',
            'visitRules',
            'travelInfos',
            'facilityItems.facility',
            'nearbyPlaces.nearbyTemple.content',
        ]);

        return view('frontend.temples.show', compact('temple'));
    }
}