#!/usr/bin/env node

import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import fetch from "node-fetch";

// Configuración del endpoint MCP de Laravel
const LARAVEL_MCP_URL = process.env.LARAVEL_MCP_URL || "http://localhost:8000/mcp";

// Servidor MCP minimalista que reenvía todo al backend Laravel
class ProxyMcpServer {
  async handleMessage(message) {
    try {
      console.error("[MCP-ADAPTER] Enviando a backend:", JSON.stringify(message));
      const response = await fetch(LARAVEL_MCP_URL, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(message),
      });
      const data = await response.json();
      console.error("[MCP-ADAPTER] Respuesta backend:", JSON.stringify(data));
      return data;
    } catch (error) {
      console.error("[MCP-ADAPTER] Error backend:", error);
      return {
        error: true,
        message: `Error al conectar con el backend MCP de Laravel: ${error.message}`,
      };
    }
  }
}

async function main() {
  const transport = new StdioServerTransport();
  const server = new ProxyMcpServer();
  // Fallback universal: escuchar evento 'message' (EventEmitter)
  transport.on && transport.on('message', async (message) => {
    const response = await server.handleMessage(message);
    await transport.sendMessage(response);
  });
  // Intentar arrancar el transporte si existe start()
  if (typeof transport.start === 'function') {
    await transport.start();
  }
  // Mantener el proceso vivo
  await new Promise(() => {});
}

main().catch((error) => {
  console.error("Fatal error in main():", error);
  process.exit(1);
});
