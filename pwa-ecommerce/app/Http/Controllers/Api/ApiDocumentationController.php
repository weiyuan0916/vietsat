<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class ApiDocumentationController extends Controller
{
    protected CommonMarkConverter $converter;

    public function __construct()
    {
        $this->converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    /**
     * Get API documentation in JSON format.
     *
     * GET /api/docs
     */
    public function index(Request $request): JsonResponse
    {
        $markdown = File::get(resource_path('docs/api.md'));

        $html = $this->converter->convert($markdown)->getContent();

        return response()->json([
            'success' => true,
            'data' => [
                'version' => 'v1',
                'base_url' => 'https://pwa-ecommerce.test/api/v1',
                'markdown' => $markdown,
                'html' => $html,
            ],
        ]);
    }

    /**
     * Get API documentation as HTML.
     *
     * GET /api/docs/html
     */
    public function html(): \Illuminate\Http\Response
    {
        $markdown = File::get(resource_path('docs/api.md'));
        $htmlContent = $this->converter->convert($markdown)->getContent();

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - Vietnam Satellite</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header {
            background: linear-gradient(135deg, #007aff 0%, #5856d6 100%);
            color: white;
            padding: 40px 20px;
            margin-bottom: 30px;
            border-radius: 8px;
        }
        .header h1 { font-size: 32px; margin-bottom: 10px; }
        .header p { opacity: 0.9; }
        .content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1, h2, h3 { color: #1a1a1a; margin-top: 30px; margin-bottom: 15px; }
        h1 { margin-top: 0; font-size: 28px; }
        h2 { font-size: 22px; border-bottom: 2px solid #007aff; padding-bottom: 10px; }
        h3 { font-size: 18px; color: #007aff; }
        p { margin-bottom: 15px; }
        code {
            background: #f0f0f0;
            padding: 2px 8px;
            border-radius: 4px;
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 14px;
        }
        pre {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            margin-bottom: 20px;
        }
        pre code {
            background: none;
            padding: 0;
            color: inherit;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th { background: #f5f5f5; font-weight: 600; }
        tr:nth-child(even) { background: #fafafa; }
        .endpoint {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
        }
        .get { background: #007aff; color: white; }
        .post { background: #34c759; color: white; }
        .put { background: #ff9500; color: white; }
        .delete { background: #ff3b30; color: white; }
        ul, ol { margin-bottom: 20px; padding-left: 30px; }
        li { margin-bottom: 8px; }
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #007aff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
        }
        .success-box {
            background: #e8f5e9;
            border-left: 4px solid #34c759;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
        }
        .error-box {
            background: #ffebee;
            border-left: 4px solid #ff3b30;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-green { background: #34c759; color: white; }
        .badge-yellow { background: #ff9500; color: white; }
        .badge-red { background: #ff3b30; color: white; }
        .badge-blue { background: #007aff; color: white; }
        a { color: #007aff; text-decoration: none; }
        a:hover { text-decoration: underline; }
        hr { border: none; border-top: 1px solid #ddd; margin: 30px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Vietnam Satellite API Documentation</h1>
            <p>Version: v1 | Base URL: <code>https://pwa-ecommerce.test/api/v1</code></p>
        </div>
        <div class="content">
            {$htmlContent}
        </div>
    </div>
</body>
</html>
HTML;

        return response($html)->header('Content-Type', 'text/html');
    }

    /**
     * Get OpenAPI/Swagger JSON spec.
     *
     * GET /api/docs/openapi
     */
    public function openapi(): JsonResponse
    {
        $spec = [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Vietnam Satellite API',
                'version' => '1.0.0',
                'description' => 'API documentation for Vietnam Satellite service',
            ],
            'servers' => [
                ['url' => 'https://pwa-ecommerce.test/api/v1'],
            ],
            'paths' => [
                '/services' => [
                    'get' => [
                        'summary' => 'List all services',
                        'parameters' => [
                            ['name' => 'page', 'in' => 'query', 'schema' => ['type' => 'integer', 'default' => 1]],
                            ['name' => 'per_page', 'in' => 'query', 'schema' => ['type' => 'integer', 'default' => 10, 'maximum' => 100]],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Successful response'],
                        ],
                    ],
                ],
                '/services/{id}' => [
                    'get' => [
                        'summary' => 'Get service details',
                        'parameters' => [
                            ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Successful response'],
                            '404' => ['description' => 'Service not found'],
                        ],
                    ],
                ],
                '/services/default' => [
                    'get' => [
                        'summary' => 'Get default service',
                        'responses' => [
                            '200' => ['description' => 'Successful response'],
                        ],
                    ],
                ],
                '/orders' => [
                    'post' => [
                        'summary' => 'Create a new order',
                        'requestBody' => [
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'facebook_profile_link' => ['type' => 'string'],
                                        ],
                                        'required' => ['facebook_profile_link'],
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            '201' => ['description' => 'Order created'],
                            '422' => ['description' => 'Validation error'],
                        ],
                    ],
                ],
                '/orders/{orderCode}' => [
                    'get' => [
                        'summary' => 'Get order details',
                        'parameters' => [
                            ['name' => 'orderCode', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Successful response'],
                            '404' => ['description' => 'Order not found'],
                        ],
                    ],
                ],
            ],
        ];

        return response()->json($spec);
    }
}









