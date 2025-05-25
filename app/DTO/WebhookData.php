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

namespace App\DTO;

use Illuminate\Http\Request;

class WebhookData
{
    public int $order_id;
    public string $status;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->order_id = $data['order_id'];
        $this->status = strtolower($data['status']);
    }

    /**
     * @param Request $request
     * @return self
     */
    public static function fromRequest(Request $request): self
    {
        $validated = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'status' => 'required|string',
        ]);

        return new self($validated);
    }
}
