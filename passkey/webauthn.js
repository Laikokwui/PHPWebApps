const base64UrlToBuffer = (baseurl) => {
  const pad = '=='.slice(0, (4 - (baseurl.length % 4)) % 4);
  const b64 = (baseurl.replace(/-/g, '+').replace(/_/g, '/') + pad);
  const binary = atob(b64);
  const len = binary.length;
  const bytes = new Uint8Array(len);
  for (let i = 0; i < len; i++) bytes[i] = binary.charCodeAt(i);
  return bytes.buffer;
};

const bufferToBase64Url = (buffer) => {
  const bytes = new Uint8Array(buffer);
  let binary = '';
  for (let i = 0; i < bytes.byteLength; i++) binary += String.fromCharCode(bytes[i]);
  const b64 = btoa(binary);
  return b64.replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
};

async function register(username) {
  const res = await fetch('passkey/register_start.php', {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({username})});
  const data = await res.json();
  if (data.error) throw data.error;
  const opts = data.publicKey;
  opts.challenge = base64UrlToBuffer(opts.challenge);
  opts.user.id = base64UrlToBuffer(opts.user.id);
  const cred = await navigator.credentials.create({publicKey: opts});
  const rawId = bufferToBase64Url(cred.rawId);
  const attestationObject = bufferToBase64Url(cred.response.attestationObject);
  const clientDataJSON = bufferToBase64Url(cred.response.clientDataJSON);
  const finish = await fetch('passkey/register_finish.php', {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({rawId, attestationObject, clientDataJSON})});
  return finish.json();
}

async function login(username) {
  const res = await fetch('passkey/login_start.php', {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({username})});
  const data = await res.json();
  if (data.error) throw data.error;
  const opts = data.publicKey;
  opts.challenge = base64UrlToBuffer(opts.challenge);
  if (opts.allowCredentials) {
    opts.allowCredentials = opts.allowCredentials.map(c => ({type: c.type, id: base64UrlToBuffer(c.id)}));
  }
  const assertion = await navigator.credentials.get({publicKey: opts});
  const rawId = bufferToBase64Url(assertion.rawId);
  const clientDataJSON = bufferToBase64Url(assertion.response.clientDataJSON);
  const authenticatorData = bufferToBase64Url(assertion.response.authenticatorData);
  const signature = bufferToBase64Url(assertion.response.signature);
  const finish = await fetch('passkey/login_finish.php', {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({rawId, clientDataJSON, authenticatorData, signature})});
  return finish.json();
}

window.passkey = {register, login};
