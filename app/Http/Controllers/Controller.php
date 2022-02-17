<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
     /**
     * @OA\Info(
     *     version="1.0",
     *     title="Api documentation for pet-shop assignment"
     * )
     * 
     * @OAS\SecurityScheme(
     *      securityScheme="bearer_token",
     *      type="https",
     *      scheme="bearer"
     * )
     */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
