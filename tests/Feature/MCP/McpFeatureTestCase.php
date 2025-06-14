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
        /*\Log::channel('mcp')->debug('[MCP] Respuesta del servidor', [
            'tool' => $tool,
            'arguments' => $arguments,
            'response' => $response,
        ]);*/
        $decoded = json_decode($response, true);
        $result = $decoded['result'] ?? $decoded;
        // Si la respuesta MCP viene en content[0][text] como JSON, decodificar automáticamente
        if (isset($result['content'][0]['text'])) {
            $json = json_decode($result['content'][0]['text'], true);
            if (is_array($json)) {
                return $json;
            }
        }
        return $result;
    }
}
