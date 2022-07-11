<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
     * @OA\Info(
     *      version="1.0.0",
     *      title="API Web Umkm",
     *      description="This For API System,And You Can Try It",
     *      @OA\Contact(
     *          email="muhammadalghifari4321@gmail.com"
     *      ),
     *      @OA\License(
     *          name="Apache 2.0",
     *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *      )
     * )
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="Demo API Server"
     * )

     *
     * @OA\Tag(
     *     name="Projects UMKM",
     *     description="Endpoint API Project"
     * )
     * @OA\Get(
     *     path="/",
     *     @OA\Response(response="200", description="Welcome page")
     * )
     * @OA\Components(
     *     @OA\SecurityScheme(
     *         type="http",
     *         description="Use a global client_id / client_secret and your username / password combo to obtain a token",
     *         name="Authorization",
     *         in="header",
     *         scheme="bearer",
     *         bearerFormat="JWT",
     *         securityScheme="bearerAuth",
     *      )
     * )
     *
*/

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
