# mcpserver — Simple Notes + MCP proxy

This folder contains a small frontend-only note app and a pure-PHP proxy to forward JSON payloads to an external MCP server.

Files added
- `index.php` — frontend SPA. Notes are stored in the browser (`localStorage`). Use the on-page "MCP Endpoint" field and "Send to MCP" button to forward a note.
- `mcp_connect.php` — pure-PHP proxy that accepts `{ endpoint, apiKey, payload }` JSON via POST and forwards it as `application/json` to `endpoint`.

Algorithm (mcp_connect.php)
1. Read JSON POST body from the client: `{ endpoint, apiKey (optional), payload }`.
2. Validate `endpoint` using `FILTER_VALIDATE_URL`. (Optional host allowlist to reduce SSRF risk.)
3. Build an HTTP POST with `Content-Type: application/json` and optional `Authorization: Bearer <apiKey>` header.
4. Use `stream_context_create` + `file_get_contents` (pure PHP) to send the request and capture the response.
5. Return a JSON envelope `{ ok: true|false, status, raw, json? }` to the client.

Quick usage (client-side example)
```bash
curl -X POST -H "Content-Type: application/json" \
  -d '{"endpoint":"https://api.example.com/mcp","apiKey":"MY_KEY","payload":{"note":"Hello from notes"}}' \
  http://localhost/mcpserver/mcp_connect.php
```

Notes & security
- The notes app (`index.php`) keeps all notes in-browser and does not store them on the server.
- The proxy forwards requests to arbitrary endpoints by default. Enable the `$allowed_hosts` array in `mcp_connect.php` to restrict destinations and avoid SSRF.

If you want, I can:
- Add an allowlist configuration and simple server-side logging.
- Add server-side validation or rate limiting for the proxy endpoint.
