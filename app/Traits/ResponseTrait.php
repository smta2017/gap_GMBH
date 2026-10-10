<?php

namespace App\Traits;


trait ResponseTrait
{
    public function sendResponse($result, $message)
    {
        return response()->json($this->makeResponse($message, $result));
    }

    public function sendError($error, $code = 404, $data = [])
    {
        return response()->json($this->makeError($error, $data), $code);
    }

    public function sendSuccess($message)
    {
        return response()->json([
            'success' => true,
            'message' => $message
        ], 200);
    }


    private function makeResponse(string $message, mixed $data): array
    {
        return [
            'success' => true,
            'data'    => $data,
            'message' => $message,
        ];
    }

    private function makeError(string $message, array $data = []): array
    {
        $res = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($data)) {
            $res['data'] = $data;
        }

        return $res;
    }
}
