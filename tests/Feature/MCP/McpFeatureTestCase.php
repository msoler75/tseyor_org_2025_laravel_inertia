<?php

namespace Tests\Feature\MCP;

use Tests\TestCase;

abstract class McpFeatureTestCase extends TestCase
{
    /**
     * Realiza una petición HTTP POST al MCP server usando cURL
     */
    protected function callMcpTool(string $tool, array $arguments = [])
    {
        $body = [
            'jsonrpc' => '2.0',
            'method' => 'tools/call',
            'params' => [
                'name' => $tool,
                'arguments' => $arguments,
            ],
            'id' => 1,
        ];
        $ch = curl_init('http://localhost/mcp');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);
        $decoded = json_decode($response, true);
        // Si la respuesta es JSON-RPC, devolver el resultado
        return $decoded['result'] ?? $decoded;
    }
}
