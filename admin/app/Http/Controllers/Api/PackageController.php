<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{

    public function GetPackage(Request $request)
    {
        try {
           $Package = Package::where('status',1)->get();
           return ResponseAPI(true,"Customer get Succesfull", "", $Package, 200);
        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something Wrongs.',"",array(),401);
        }
    }

}