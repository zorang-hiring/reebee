<?php
declare(strict_types=1);

namespace App\Controller;

use App\Request;

/**
 * Simple controller to expose api documentation schema
 */
class DocsController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $path = realpath(__DIR__ . '/../');
        $openapi = \OpenApi\scan($path);
        header('Content-Type: application/x-yaml');
        echo $openapi->toYaml();
        exit;
    }
}