<?php
/**
 * GB Developer
 *
 * @category GB_Developer
 * @package  GB
 *
 * @copyright Copyright (c) 2025 GB Developer.
 *
 * @author Geovan Brambilla <geovangb@gmail.com>
 */

namespace App\Http\Controllers;

use App\DTO\WebhookData;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WebhookController extends Controller
{
    protected WebhookService $service;

    /**
     * @param WebhookService $service
     */
    public function __construct(WebhookService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        if ($request->header('X-WEBHOOK-KEY') !== env('WEBHOOK_SECRET')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $data = WebhookData::fromRequest($request);
            $message = $this->service->handle($data);

            return response()->json(['message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
