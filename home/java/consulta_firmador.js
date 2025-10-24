(() => {
  const WRAP = document.getElementById('consola-apis');
  if (!WRAP) return;

  // Endpoint (puede venir como http://localhost/dev/firmador/ desde data-endpoint)
  const ENDPOINT       = WRAP.dataset?.endpoint || 'https://api.guibis.com/dev/firmador/';
  const selectApp      = document.getElementById('selectApp');

  const keyInfo        = document.getElementById('keyInfo');
  const keyPreview     = document.getElementById('keyPreview');
  const btnCopyKey     = document.getElementById('btnCopyKey');

  const uploadArea     = document.getElementById('uploadArea');
  const xmlfile        = document.getElementById('xmlfile');
  const firmap12       = document.getElementById('firmap12');
  const clave          = document.getElementById('clave');
  const btnEnviar      = document.getElementById('btnEnviar');

  const alertArea      = document.getElementById('alertArea');
  const summary        = document.getElementById('summary');

  // JSON enviado (preview metadatos)
  const jsonReqBox     = document.getElementById('jsonReqBox');
  const jsonReqRaw     = document.getElementById('jsonReqRaw');
  const btnCopyReq     = document.getElementById('btnCopyReq');
  const btnDownloadReq = document.getElementById('btnDownloadReq');

  // JSON respuesta
  const jsonBox        = document.getElementById('jsonBox');
  const jsonRaw        = document.getElementById('jsonRaw');
  const btnCopy        = document.getElementById('btnCopy');
  const btnDownload    = document.getElementById('btnDownload');

  let CURRENT_KEY = "";
  let CURRENT_APP_NAME = ""; // para filename

  // ===== Helpers comunes =====
  function toNumberOrNull(v){
    if (typeof v === 'number' && isFinite(v)) return v;
    if (v === null || v === undefined || v === '') return null;
    const n = Number(v);
    return isFinite(n) ? n : null;
  }
  function money(v){
    const n = toNumberOrNull(v);
    return (n !== null) ? n.toFixed(2) : '0.00';
  }
  function slugify(str = "", maxLen = 40){
    return String(str)
      .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
      .replace(/[^a-zA-Z0-9_.-]+/g, "-")
      .replace(/^-+|-+$/g, "")
      .slice(0, maxLen);
  }
  function ts(){
    const d = new Date();
    const p = (n) => String(n).padStart(2, '0');
    return `${d.getFullYear()}${p(d.getMonth()+1)}${p(d.getDate())}-${p(d.getHours())}${p(d.getMinutes())}${p(d.getSeconds())}`;
  }
  function humanSize(bytes = 0){
    const k=1024, u=['B','KB','MB','GB','TB']; let i=0, n=+bytes||0;
    while(n>=k && i<u.length-1){ n/=k; i++; }
    return `${n.toFixed(2)} ${u[i]}`;
  }
  function saveBlob(blob, filename){
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url; a.download = filename;
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    URL.revokeObjectURL(url);
  }
  function base64ToBlob(b64, mime='application/octet-stream'){
    try{
      const byteChars = atob(b64);
      const byteNums = new Array(byteChars.length);
      for (let i=0; i<byteChars.length; i++) byteNums[i] = byteChars.charCodeAt(i);
      const byteArray = new Uint8Array(byteNums);
      return new Blob([byteArray], { type: mime });
    }catch(_e){
      return null;
    }
  }
  function guessExtFromMime(mime=''){
    if (mime.includes('xml') ) return 'xml';
    if (mime.includes('pdf') ) return 'pdf';
    if (mime.includes('zip') ) return 'zip';
    if (mime.includes('json')) return 'json';
    return 'bin';
  }

  function clearPanels(){
    alertArea.className = 'mt-3 d-none';
    alertArea.innerHTML = '';
    summary.className = 'mt-3 d-none';
    summary.innerHTML = '';
    jsonReqBox.classList.add('d-none');
    jsonReqRaw.textContent = '{}';
    jsonBox.classList.add('d-none');
    jsonRaw.textContent = '{}';
  }
  function showAlert(type, html){
    alertArea.className = `mt-3 alert alert-${type}`;
    alertArea.innerHTML = html;
  }

  // ===== Resumen robusto (quota/billing) =====
  function renderSummary(payload){
    const q = payload?.quota ?? {};
    const b = payload?.billing ?? {};

    const gratis = (q.gratis_restantes ?? '—');
    const usadas = (q.usadas_mes ?? '—');
    const maxm   = (q.mensual_max ?? '—');
    const esPago = Number(q.es_pago || 0) === 1;

    // Costo: quota.costo_consulta → billing.costo_total → billing.costo_unitario
    const costoNum       = toNumberOrNull(q.costo_consulta) ?? toNumberOrNull(b.costo_total) ?? toNumberOrNull(b.costo_unitario) ?? 0;
    const saldoInicioNum = toNumberOrNull(b.saldo_inicio) ?? 0;
    const saldoRestNum   = toNumberOrNull(b.saldo_restante) ?? 0;

    const rangoMes = Array.isArray(q.rango_mes) ? q.rango_mes.filter(Boolean).join(' → ') : '';

    // Si no hay nada útil, ocultar y salir
    const noData = (!q || (gratis === '—' && usadas === '—' && maxm === '—')) && !b;
    if (noData){
      summary.className = 'mt-3 d-none';
      summary.innerHTML = '';
      return;
    }

    summary.className = 'mt-3';
    summary.innerHTML = `
      <div class="row g-3">
        <div class="col-md-3">
          <div class="border rounded p-3 bg-white">
            <div class="small text-muted mb-1"><i class="fa-solid fa-circle-check me-1"></i>Status</div>
            <div class="fw-bold">${payload?.status ?? '—'}</div>
            ${rangoMes ? `<div class="small text-muted mt-1"><i class="fa-regular fa-calendar me-1"></i>${rangoMes}</div>` : ''}
          </div>
        </div>
        <div class="col-md-3">
          <div class="border rounded p-3 bg-white">
            <div class="small text-muted mb-1"><i class="fa-solid fa-gift me-1"></i>Gratis restantes</div>
            <div class="fw-bold">${gratis}</div>
            <div class="small text-muted">Usadas: ${usadas} / ${maxm}</div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="border rounded p-3 bg-white">
            <div class="small text-muted mb-1"><i class="fa-solid fa-credit-card me-1"></i>Simulación de cobro</div>
            <div class="d-flex flex-wrap gap-2">
              <span class="badge text-bg-${esPago ? 'danger' : 'success'}">${esPago ? 'De pago' : 'Gratis'}</span>
              <span class="badge text-bg-light">Costo: $${money(costoNum)}</span>
              <span class="badge text-bg-light">Saldo ini: $${money(saldoInicioNum)}</span>
              <span class="badge text-bg-light">Saldo post: $${money(saldoRestNum)}</span>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  // ===== Eventos =====
  selectApp.addEventListener('change', () => {
    clearPanels();
    const opt = selectApp.options[selectApp.selectedIndex];
    const key = (selectApp.value || '').trim();
    CURRENT_APP_NAME = opt ? (opt.getAttribute('data-nombre') || '') : '';

    if(!key){
      CURRENT_KEY = "";
      keyPreview.textContent = "";
      keyInfo.classList.add('d-none');
      uploadArea.classList.add('d-none');
      xmlfile.value = ""; firmap12.value = ""; clave.value = "";
      return;
    }
    CURRENT_KEY = key;
    keyPreview.textContent = key;
    keyInfo.classList.remove('d-none');
    uploadArea.classList.remove('d-none');
  });

  btnCopyKey.addEventListener('click', async () => {
    try {
      const val = keyPreview.textContent || '';
      if(!val) return;
      await navigator.clipboard.writeText(val);
      btnCopyKey.innerHTML = '<i class="fa-solid fa-check me-1"></i> Copiada';
      setTimeout(() => { btnCopyKey.innerHTML = '<i class="fa-regular fa-clone me-1"></i> Copiar KEY'; }, 1200);
    } catch (e) {
      showAlert('danger', `<i class="fa-solid fa-circle-xmark me-2"></i>No se pudo copiar la KEY: ${e.message}`);
    }
  });

  // ===== Enviar (multipart/form-data) =====
  btnEnviar.addEventListener('click', async () => {
    clearPanels();

    if(!CURRENT_KEY){
      showAlert('warning', '<i class="fa-solid fa-triangle-exclamation me-2"></i>Selecciona una aplicación.');
      return;
    }
    const fXML = xmlfile.files?.[0];
    const fP12 = firmap12.files?.[0];
    const pass = (clave.value || '').trim();

    if(!fXML){ showAlert('warning', '<i class="fa-solid fa-triangle-exclamation me-2"></i>Selecciona el archivo XML.'); xmlfile.focus(); return; }
    if(!fP12){ showAlert('warning', '<i class="fa-solid fa-triangle-exclamation me-2"></i>Selecciona el archivo .p12/.pfx.'); firmap12.focus(); return; }
    if(!pass){ showAlert('warning', '<i class="fa-solid fa-triangle-exclamation me-2"></i>Ingresa la clave del certificado.'); clave.focus(); return; }

    // Preview metadatos (clave oculta)
    const reqPreview = {
      KEY: CURRENT_KEY,
      clave: `•••• (${pass.length} chars)`,
      xmlfile: { name: fXML.name, size_bytes: fXML.size, size_human: humanSize(fXML.size), type: fXML.type || 'application/xml' },
      firmap12:{ name: fP12.name, size_bytes: fP12.size, size_human: humanSize(fP12.size), type: fP12.type || 'application/x-pkcs12' }
    };
    jsonReqRaw.textContent = JSON.stringify(reqPreview, null, 2);
    jsonReqBox.classList.remove('d-none');

    // FormData
    const fd = new FormData();
    fd.append('xmlfile', fXML);
    fd.append('firmap12', fP12);
    fd.append('clave', pass);
    fd.append('KEY', CURRENT_KEY);

    const original = btnEnviar.innerHTML;
    btnEnviar.disabled = true;
    btnEnviar.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Firmando…`;

    try{
      const res = await fetch(ENDPOINT, { method: 'POST', body: fd });
      const ctype = (res.headers.get('content-type') || '').toLowerCase();

      // === Respuesta BINARIA (XML/PDF/ZIP, etc.) ===
      if (!ctype.includes('application/json') && !ctype.includes('text/json')) {
        const blob = await res.blob();
        if (!res.ok) {
          showAlert('danger', `<i class="fa-solid fa-circle-xmark me-2"></i>Error ${res.status}: No se pudo firmar.`);
        } else {
          const ext  = guessExtFromMime(ctype) || 'xml';
          const base = slugify((fXML.name || 'documento').replace(/\.[^.]+$/,''));
          const file = `${base}_firmado_${ts()}.${ext}`;
          saveBlob(blob, file);

          // Pintar JSON informativo
          const info = { status: 'success', content_type: ctype, filename_sugerido: file, size_bytes: blob.size, size_human: humanSize(blob.size) };
          jsonBox.classList.remove('d-none');
          jsonRaw.textContent = JSON.stringify(info, null, 2);
          showAlert('success', `<i class="fa-solid fa-circle-check me-2"></i>Archivo firmado generado. Se inició la descarga.`);
        }
        return;
      }

      // === Respuesta JSON ===
      let data = null;
      try { data = await res.json(); } catch { data = { error: 'JSON inválido' }; }

      if(!res.ok || data?.error){
        showAlert('danger', `<i class="fa-solid fa-circle-xmark me-2"></i><b>Error:</b> ${data?.error ?? 'Error en la solicitud.'}`);
      } else {
        // 🔹 Mostrar resumen si vienen quota/billing
        renderSummary(data);

        // Soporte base64 / url de descarga
        const b64   = data.file_base64 || data.archivo_base64 || data.signed_base64 || null;
        const mime  = data.mime || data.content_type || 'application/octet-stream';
        const nombre= data.nombre_archivo || data.filename ||
          `${slugify((fXML.name||'documento').replace(/\.[^.]+$/,''))}_firmado_${ts()}.${guessExtFromMime(mime)}`;

        if (b64) {
          const blob = base64ToBlob(b64, mime);
          if (blob) {
            saveBlob(blob, nombre);
            data._descarga_generada = { filename: nombre, size_bytes: blob.size, size_human: humanSize(blob.size), content_type: mime };
            showAlert('success', `<i class="fa-solid fa-circle-check me-2"></i>Archivo firmado listo. Se inició la descarga.`);
          } else {
            showAlert('warning', `<i class="fa-solid fa-triangle-exclamation me-2"></i>Base64 inválido en la respuesta. Se muestra JSON.`);
          }
        } else if (data.file_url || data.url_descarga) {
          const url = data.file_url || data.url_descarga;
          const a = document.createElement('a');
          a.href = url; a.target = '_blank'; a.rel = 'noopener';
          a.click();
          showAlert('success', `<i class="fa-solid fa-circle-check me-2"></i>Abriendo archivo firmado…`);
        } else {
          showAlert('success', `<i class="fa-solid fa-circle-check me-2"></i>Solicitud completada.`);
        }

        jsonBox.classList.remove('d-none');
        jsonRaw.textContent = JSON.stringify(data, null, 2);
      }
    }catch(err){
      showAlert('danger', `<i class="fa-solid fa-circle-xmark me-2"></i>${err?.message ?? err}`);
    }finally{
      btnEnviar.disabled = false;
      btnEnviar.innerHTML = original;
    }
  });

  // Copiar/Descargar previews
  btnCopyReq.addEventListener('click', async ()=>{ try{ await navigator.clipboard.writeText(jsonReqRaw.textContent); btnCopyReq.innerHTML = '<i class="fa-solid fa-check me-1"></i>Copiado'; setTimeout(()=> btnCopyReq.innerHTML = '<i class="fa-solid fa-copy me-1"></i>Copiar', 1200);}catch(e){} });
  btnDownloadReq.addEventListener('click', () => {
    try {
      const contenido = jsonReqRaw.textContent || '{}';
      let pretty = contenido; try { pretty = JSON.stringify(JSON.parse(contenido), null, 2); } catch {}
      const appSlug = slugify(CURRENT_APP_NAME || 'app');
      const filename = `requestFIRMADOR_${appSlug}_${ts()}.json`;
      saveBlob(new Blob([pretty], { type: 'application/json' }), filename);
    } catch (e) {
      showAlert('danger', `<i class="fa-solid fa-circle-xmark me-2"></i>Error al descargar: ${e.message}`);
    }
  });

  btnCopy.addEventListener('click', async ()=>{ try{ await navigator.clipboard.writeText(jsonRaw.textContent); btnCopy.innerHTML = '<i class="fa-solid fa-check me-1"></i>Copiado'; setTimeout(()=> btnCopy.innerHTML = '<i class="fa-solid fa-copy me-1"></i>Copiar', 1200);}catch(e){} });
  btnDownload.addEventListener('click', () => {
    try {
      const contenido = jsonRaw.textContent || '{}';
      let pretty = contenido; try { pretty = JSON.stringify(JSON.parse(contenido), null, 2); } catch {}
      const appSlug = slugify(CURRENT_APP_NAME || 'app');
      const filename = `responseFIRMADOR_${appSlug}_${ts()}.json`;
      saveBlob(new Blob([pretty], { type: 'application/json' }), filename);
    } catch (e) {
      showAlert('danger', `<i class="fa-solid fa-circle-xmark me-2"></i>Error al descargar: ${e.message}`);
    }
  });
})();
