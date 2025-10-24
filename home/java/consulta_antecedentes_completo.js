(() => {
  const WRAP = document.getElementById('consola-apis');
  if (!WRAP) return;

  // Puede venir desde data-endpoint con http://localhost/dev/antecedentes/
  const ENDPOINT       = WRAP.dataset?.endpoint || 'https://api.guibis.com/dev/antecedentes/';
  const selectApp      = document.getElementById('selectApp');

  const keyInfo        = document.getElementById('keyInfo');
  const keyPreview     = document.getElementById('keyPreview');
  const btnCopyKey     = document.getElementById('btnCopyKey');

  const searchArea     = document.getElementById('searchArea');
  const parametro      = document.getElementById('parametro');
  const btnEnviar      = document.getElementById('btnEnviar');

  const alertArea      = document.getElementById('alertArea');
  const summary        = document.getElementById('summary');

  // JSON enviado
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
  let CURRENT_APP_NAME = ""; // para filenames
  let CURRENT_PARAM = "";    // para filenames

  // ===== Helpers seguros =====
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
      .replace(/[^a-zA-Z0-9_-]+/g, "-")
      .replace(/^-+|-+$/g, "")
      .slice(0, maxLen);
  }
  function ts(){
    const d = new Date();
    const pad = (n) => String(n).padStart(2, '0');
    return `${d.getFullYear()}${pad(d.getMonth()+1)}${pad(d.getDate())}-${pad(d.getHours())}${pad(d.getMinutes())}${pad(d.getSeconds())}`;
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

  // ===== Resumen robusto (usa quota y/o billing si vienen) =====
  function renderSummary(payload){
    const q = payload?.quota ?? {};
    const b = payload?.billing ?? {};

    const gratis = (q.gratis_restantes ?? '—');
    const usadas = (q.usadas_mes ?? '—');
    const maxm   = (q.mensual_max ?? '—');
    const esPago = Number(q.es_pago || 0) === 1;

    // Costo: prioridad quota.costo_consulta → billing.costo_total → billing.costo_unitario
    const costoNum       = toNumberOrNull(q.costo_consulta) ?? toNumberOrNull(b.costo_total) ?? toNumberOrNull(b.costo_unitario) ?? 0;
    const saldoInicioNum = toNumberOrNull(b.saldo_inicio) ?? 0;
    const saldoRestNum   = toNumberOrNull(b.saldo_restante) ?? 0;

    // Rango de mes (si viene)
    const rangoMes = Array.isArray(q.rango_mes) ? q.rango_mes.filter(Boolean).join(' → ') : '';

    // Si no hay nada útil que mostrar, ocultamos
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
      searchArea.classList.add('d-none');
      CURRENT_PARAM = "";
      return;
    }

    CURRENT_KEY = key;
    keyPreview.textContent = key; // mostrar COMPLETA
    keyInfo.classList.remove('d-none');
    searchArea.classList.remove('d-none');
    parametro.focus();
  });

  // Copiar KEY
  btnCopyKey.addEventListener('click', async () => {
    try {
      const val = keyPreview.textContent || '';
      if(!val) return;
      await navigator.clipboard.writeText(val);
      btnCopyKey.innerHTML = '<i class="fa-solid fa-check me-1"></i> Copiada';
      setTimeout(() => {
        btnCopyKey.innerHTML = '<i class="fa-regular fa-clone me-1"></i> Copiar KEY';
      }, 1200);
    } catch (e) {
      showAlert('danger', `<i class="fa-solid fa-circle-xmark me-2"></i>No se pudo copiar la KEY: ${e.message}`);
    }
  });

  // Consultar
  btnEnviar.addEventListener('click', async () => {
    clearPanels();

    if(!CURRENT_KEY){
      showAlert('warning', '<i class="fa-solid fa-triangle-exclamation me-2"></i>Selecciona una aplicación.');
      return;
    }
    const param = (parametro.value || '').trim();
    if(!param){
      showAlert('warning', '<i class="fa-solid fa-triangle-exclamation me-2"></i>Ingresa el parámetro de búsqueda.');
      parametro.focus();
      return;
    }
    CURRENT_PARAM = param;

    // JSON ENVIADO
    const requestBody = {
      parametro_busqueda: param,
      KEY: CURRENT_KEY
    };
    // Mostrar JSON a enviar
    jsonReqRaw.textContent = JSON.stringify(requestBody, null, 2);
    jsonReqBox.classList.remove('d-none');

    const original = btnEnviar.innerHTML;
    btnEnviar.disabled = true;
    btnEnviar.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Consultando…`;

    try{
      const res = await fetch(ENDPOINT, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(requestBody)
      });

      let data = null;
      try {
        data = await res.json();
      } catch (_e) {
        data = { error: 'JSON inválido' };
      }

      if(!res.ok || data?.error){
        showAlert('danger', `<i class="fa-solid fa-circle-xmark me-2"></i><b>Error:</b> ${data?.error ?? 'Error en la solicitud.'}`);
      }else{
        renderSummary(data);
        jsonBox.classList.remove('d-none');
        jsonRaw.textContent = JSON.stringify(data, null, 2);
        showAlert('success', '<i class="fa-solid fa-circle-check me-2"></i>Consulta realizada con éxito.');
      }
    }catch(err){
      showAlert('danger', `<i class="fa-solid fa-circle-xmark me-2"></i>${err?.message ?? err}`);
    }finally{
      btnEnviar.disabled = false;
      btnEnviar.innerHTML = original;
    }
  });

  // Copiar JSON enviado
  btnCopyReq.addEventListener('click', async ()=>{
    try{
      await navigator.clipboard.writeText(jsonReqRaw.textContent);
      btnCopyReq.innerHTML = '<i class="fa-solid fa-check me-1"></i>Copiado';
      setTimeout(()=> btnCopyReq.innerHTML = '<i class="fa-solid fa-copy me-1"></i>Copiar', 1200);
    }catch(e){}
  });

  // Descargar JSON enviado (formateado)
  btnDownloadReq.addEventListener('click', () => {
    try {
      const contenido = jsonReqRaw.textContent || '{}';
      let pretty = contenido;
      try {
        const parsed = JSON.parse(contenido);
        pretty = JSON.stringify(parsed, null, 2);
      } catch(_) {}
      const appSlug = slugify(CURRENT_APP_NAME || 'app');
      const paramSlug = slugify(CURRENT_PARAM || 'param');
      const filename = `requestANTECEDENTES_COMPLETO_${appSlug}_${paramSlug}_${ts()}.json`;

      const blob = new Blob([pretty], { type: "application/json" });
      const url = URL.createObjectURL(blob);

      const a = document.createElement("a");
      a.href = url; a.download = filename;
      document.body.appendChild(a); a.click(); document.body.removeChild(a);
      URL.revokeObjectURL(url);
    } catch (e) {
      showAlert('danger', `<i class="fa-solid fa-circle-xmark me-2"></i>Error al descargar: ${e.message}`);
    }
  });

  // Copiar JSON respuesta
  btnCopy.addEventListener('click', async ()=>{
    try{
      await navigator.clipboard.writeText(jsonRaw.textContent);
      btnCopy.innerHTML = '<i class="fa-solid fa-check me-1"></i>Copiado';
      setTimeout(()=> btnCopy.innerHTML = '<i class="fa-solid fa-copy me-1"></i>Copiar', 1200);
    }catch(e){}
  });

  // Descargar JSON respuesta (formateado)
  btnDownload.addEventListener('click', () => {
    try {
      const contenido = jsonRaw.textContent || '{}';
      let pretty = contenido;
      try {
        const parsed = JSON.parse(contenido);
        pretty = JSON.stringify(parsed, null, 2);
      } catch(_) {}
      const appSlug = slugify(CURRENT_APP_NAME || 'app');
      const paramSlug = slugify(CURRENT_PARAM || 'param');
      const filename = `responseANTECEDENTES_COMPLETO_${appSlug}_${paramSlug}_${ts()}.json`;

      const blob = new Blob([pretty], { type: "application/json" });
      const url = URL.createObjectURL(blob);

      const a = document.createElement("a");
      a.href = url; a.download = filename;
      document.body.appendChild(a); a.click(); document.body.removeChild(a);
      URL.revokeObjectURL(url);
    } catch (e) {
      showAlert('danger', `<i class="fa-solid fa-circle-xmark me-2"></i>Error al descargar: ${e.message}`);
    }
  });
})();
