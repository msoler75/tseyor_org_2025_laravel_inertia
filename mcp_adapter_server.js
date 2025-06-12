#!/usr/bin/env node

import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import fetch from "node-fetch";

// Configuración del endpoint MCP de Laravel
const LARAVEL_MCP_URL = process.env.LARAVEL_MCP_URL || "http://localhost:8000/mcp";

async function main() {
  // Solo stdio transport
  const transport = new StdioServerTransport();

  // Escucha mensajes MCP entrantes
  for await (const message of transport.receiveMessages()) {
    try {
      // Reenvía el mensaje al backend MCP de Laravel
      const response = await fetch(LARAVEL_MCP_URL, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(message),
      });
      const data = await response.json();
      // Devuelve la respuesta al cliente MCP
      await transport.sendMessage(data);
    } catch (error) {
      await transport.sendMessage({
        error: true,
        message: `Error al conectar con el backend MCP de Laravel: ${error.message}`,
      });
    }
  }
}

main().catch((error) => {
  console.error("Fatal error in main():", error);
  process.exit(1);
});
