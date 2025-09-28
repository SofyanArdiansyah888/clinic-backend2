<?php

namespace App\Http\Controllers;

use App\Utils\Generator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GeneratorController extends Controller
{
    /**
     * Generate unique ID for a given model key
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function generateNumber(Request $request): JsonResponse
    {
        try {
            $key = $request->query('key');

            if (!$key) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter key is required',
                    'data' => null
                ], 400);
            }

            // Validasi key yang diperbolehkan menggunakan method dari Generator
            $allowedKeys = array_keys(Generator::getAvailableKeys());

            if (!in_array($key, $allowedKeys)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid key. Allowed keys: ' . implode(', ', $allowedKeys),
                    'data' => null
                ], 400);
            }

            // Generate ID
            $generatedId = Generator::generateID($key);

            return response()->json([
                'success' => true,
                'message' => 'ID generated successfully',
                'last_id' => $generatedId,
                'key' => $key,
                'timestamp' => now()->toISOString()
            ], 200);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate ID: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Get available keys for ID generation
     *
     * @return JsonResponse
     */
    public function getAvailableKeys(): JsonResponse
    {
        $keys = Generator::getAvailableKeys();

        return response()->json([
            'success' => true,
            'message' => 'Available keys retrieved successfully',
            'data' => $keys
        ], 200);
    }
}
