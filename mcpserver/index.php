<?php
// Simple Notes — frontend-only app. Notes stored in browser localStorage.
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Simple Notes</title>
  <style>
    body{font-family:Inter,Segoe UI,Arial,sans-serif;max-width:900px;margin:24px auto;padding:0 16px}
    h1{margin-bottom:0.2rem}
    .row{display:flex;gap:8px;align-items:center}
    input,textarea{width:100%;padding:8px;border:1px solid #ddd;border-radius:4px}
    button{padding:8px 10px;border-radius:4px;border:1px solid #ccc;background:#f5f5f5}
    ul{list-style:none;padding-left:0}
    li{padding:8px;border:1px solid #eee;margin:8px 0;border-radius:6px}
    .meta{color:#666;font-size:0.9rem}
    .mcp-config{margin:8px 0;display:flex;gap:8px}
    .mcp-config input{max-width:420px}
    pre#responseBox{background:#fafafa;border:1px solid #eee;padding:12px;border-radius:6px;min-height:60px}
  </style>
</head>
<body>
  <h1>Simple Notes (no backend)</h1>
  <p>Notes are stored locally in your browser. Use the "MCP Endpoint" below to forward a note to an external MCP server via the PHP proxy.</p>

  <div class="mcp-config">
    <input id="mcpEndpoint" placeholder="https://example.com/api/mcp" />
    <input id="mcpApiKey" placeholder="Optional API key" />
  </div>

  <div style="margin:8px 0">
    <input id="noteTitle" placeholder="Title" />
    <div style="height:8px"></div>
    <textarea id="noteContent" rows="6" placeholder="Write a note..."></textarea>
    <div style="height:8px"></div>
    <button id="addNote">Add Note</button>
  </div>

  <ul id="notesList"></ul>

  <h3>Last response</h3>
  <pre id="responseBox">(no activity yet)</pre>

  <script>
    const storageKey = 'simple_notes_v1';
    let notes = JSON.parse(localStorage.getItem(storageKey) || '[]');

    function save(){ localStorage.setItem(storageKey, JSON.stringify(notes)); }

    function escapeHtml(s){ if(typeof s !== 'string') return s; return s.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]); }

    function render(){
      const ul = document.getElementById('notesList'); ul.innerHTML = '';
      notes.forEach((n,i)=>{
        const li = document.createElement('li');
        const title = n.title ? escapeHtml(n.title) : '(no title)';
        const body = n.body ? escapeHtml(n.body) : '';
        li.innerHTML = `<strong>${title}</strong><div class="meta">${new Date(n.created).toLocaleString()}</div><div style="margin-top:6px">${body}</div>`;
        const send = document.createElement('button'); send.textContent = 'Send to MCP'; send.style.marginTop = '8px';
        send.onclick = () => sendToMcp(i);
        const del = document.createElement('button'); del.textContent = 'Delete'; del.style.marginLeft='8px';
        del.onclick = () => { notes.splice(i,1); save(); render(); }
        li.appendChild(send); li.appendChild(del);
        ul.appendChild(li);
      })
    }

    document.getElementById('addNote').addEventListener('click', ()=>{
      const t = document.getElementById('noteTitle').value.trim();
      const b = document.getElementById('noteContent').value.trim();
      if(!t && !b){ alert('Please enter a title or note content'); return; }
      notes.unshift({title:t, body:b, created: Date.now()}); save(); render();
      document.getElementById('noteTitle').value=''; document.getElementById('noteContent').value='';
    });

    render();

    async function sendToMcp(index){
      const endpoint = document.getElementById('mcpEndpoint').value.trim();
      if(!endpoint){ alert('Enter MCP endpoint URL first'); return; }
      const apiKey = document.getElementById('mcpApiKey').value.trim();
      const note = notes[index];
      const payload = {note, meta:{sentAt:new Date().toISOString(), source:'simple-notes'}};
      document.getElementById('responseBox').textContent = 'Sending...';
      try{
        const res = await fetch('mcp_connect.php', {
          method: 'POST',
          headers: {'Content-Type':'application/json'},
          body: JSON.stringify({endpoint, apiKey, payload})
        });
        const data = await res.json();
        document.getElementById('responseBox').textContent = JSON.stringify(data, null, 2);
      }catch(err){ document.getElementById('responseBox').textContent = 'Network error: ' + err.message }
    }
  </script>
</body>
</html>
