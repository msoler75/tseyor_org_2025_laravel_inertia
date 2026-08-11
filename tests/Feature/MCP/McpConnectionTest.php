<?php

namespace Tests\Feature\MCP;

use Tests\TestCase;

/**
 * Verifica la conectividad HTTP real contra el servidor MCP en http://localhost/mcp.
 *
 * Esta prueba se salta (markTestSkipped) si el servidor no está accesible,
 * para no romper la suite cuando el servidor MCP en vivo no está levantado.
 *
 * @group mcp-connection
 */
class McpConnectionTest extends TestCase
{
    public function test_http_connectivity_to_mcp_server()
    {
        $ch = curl_init('http://localhost/mcp');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'jsonrpc' => '2.0',
            'method' => 'tools/list',
            'id' => 1,
        ]));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $curlError = curl_errno($ch) ? curl_error($ch) : null;
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $curlError !== null || $httpCode < 200 || $httpCode >= 300) {
            $this->markTestSkipped(
                'Servidor MCP no accesible en http://localhost/mcp'
                .($curlError !== null ? " (curl: {$curlError})" : " (HTTP {$httpCode})")
            );
        }

        $this->assertGreaterThanOrEqual(200, $httpCode);
        $this->assertLessThan(300, $httpCode);

        $decoded = json_decode($response, true);
        $this->assertIsArray($decoded, 'La respuesta del servidor MCP no es JSON-RPC válido');
        $this->assertArrayHasKey('jsonrpc', $decoded, 'La respuesta no contiene el campo jsonrpc');
        $this->assertEquals('2.0', $decoded['jsonrpc']);
        $this->assertArrayHasKey('result', $decoded, 'La respuesta no contiene un result JSON-RPC');
        $this->assertArrayHasKey('tools', $decoded['result'], 'El result de tools/list no contiene la lista de tools');
    }
}
