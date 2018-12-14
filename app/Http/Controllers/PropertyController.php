<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PropertyService;

class PropertyController extends Controller
{

    // Property service acts as a downstream API...
    protected $propertyService;

    // Constructor with Dependency injection for PropertySearchService...
    public function __construct(PropertyService $propertySearch)
    {
        $this->propertyService = $propertySearch;
    }

    /* API Endpoint - Meant to act as a downstream for availability controller. Now acting to demonstrate Services and reusability in Laravel.
     */
    public function index (Request $request) {
        $data = $this->propertyService->searchProperty(123, date_format(date_create('2018-12-11'), 'Y-m-d'), 2);
        return response()->json($data);
    }
}
