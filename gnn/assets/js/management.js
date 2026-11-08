/* GNN management interactions. */
(function () {
 'use strict';
 var config = window.gnnManagement || {};
 document.querySelectorAll('.gnn-management-form').forEach(function (form) {
  form.addEventListener('submit', function () {
   var status = form.querySelector('.gnn-operation-status');
   if (status) { status.textContent = config.busy || 'Working…'; }
   var button = form.querySelector('[type=submit]');
   if (button) { button.disabled = true; }
   form.setAttribute('aria-busy', 'true');
  });
 });
 document.querySelectorAll('.gnn-design-topics a').forEach(function (link) {
  link.addEventListener('click', function () {
   var group = document.querySelector(link.hash);
   if (group) { group.open = true; }
  });
 });

 function setting(key) {
  var input = document.querySelector('[name="gnn_options[' + key + ']"]');
  return input ? input.value : '';
 }
 function previews() {
  document.querySelectorAll('[data-type-preview]').forEach(function (preview) {
   var base = 'type_' + preview.dataset.typePreview + '_';
   var size = Number(setting(base + 'size'));
   var leading = Number(setting(base + 'leading'));
   preview.style.fontSize = size ? size + 'px' : '';
   preview.style.lineHeight = leading ? String(leading / 100) : '';
   preview.style.fontWeight = setting(base + 'weight');
   var tracking = setting(base + 'tracking');
   preview.style.letterSpacing = tracking !== '' ? Number(tracking) / 100 + 'em' : '';
   preview.style.textTransform = setting(base + 'transform');
  });
  document.querySelectorAll('.gnn-button-preview').forEach(function (preview, index) {
   var base = 'btn_style_' + (index + 1) + '_';
   preview.textContent = setting(base + 'label') || String(index + 1);
   ['bg', 'text', 'border'].forEach(function (part, i) {
    var value = setting(base + part);
    preview.style[['backgroundColor', 'color', 'borderColor'][i]] = /^#[a-f0-9]{3,8}$/i.test(value) ? value : '';
   });
  });
 }
 document.querySelectorAll('.gnn-design-fields input,.gnn-design-fields select').forEach(function (input) { input.addEventListener('input', previews); });
 previews();
 var running = false;
 async function wipe(start, form, status, button) {
  if (running) { return; }
  running = true;
  button.disabled = true;
  status.textContent = config.busy;
  try {
   var next = start;
   for (;;) {
    var body = new URLSearchParams();
    body.set('action', 'gnn_wipe');
    body.set('nonce', config.nonce);
    if (next && form) {
     new FormData(form).forEach(function (value, key) { body.set(key, value); });
     body.set('start', '1');
    }
    var response = await fetch(config.ajax, {method: 'POST', credentials: 'same-origin', body: body});
    var result = await response.json().catch(function () { return {success: false, data: {message: config.failure}}; });
    if (!response.ok || !result.success) { throw new Error(result.data && result.data.message || config.failure); }
    next = false;
    status.textContent = config.busy + ' (' + result.data.done + ')';
    if (result.data.phase === 'complete') {
     status.textContent = config.done;
     window.location.assign(result.data.redirect);
     break;
    }
   }
  } catch (error) {
   status.textContent = error.message || config.failure;
   button.textContent = config.retry;
   button.disabled = false;
   if (form) { form.dataset.resume = '1'; }
  } finally { running = false; }
 }
 var form = document.querySelector('.gnn-wipe-form');
 if (form) {
  form.addEventListener('submit', function (event) {
   event.preventDefault();
   if (!form.reportValidity()) { return; }
   wipe(!form.dataset.resume, form, form.querySelector('.gnn-wipe-status'), form.querySelector('button'));
  });
 }
 var resume = document.querySelector('.gnn-wipe-resume');
 if (resume) {
  resume.addEventListener('click', function () { wipe(false, null, document.querySelector('.gnn-wipe-status'), resume); });
 }

 var dialog = document.getElementById('gnn-demo-confirm');
 var demoForm = dialog && dialog.querySelector('.gnn-demo-form');
 var demoRunning = false;
 async function replaceDemo(button, status, job, form) {
  if (demoRunning) { return; }
  demoRunning = true;
  button.disabled = true;
  if (dialog) { dialog.querySelector('.gnn-demo-cancel').disabled = true; }
  status.textContent = config.busy;
  try {
   for (;;) {
    var body = new URLSearchParams({action: 'gnn_demo_replace', nonce: config.demoNonce});
    if (job) {
     body.set('job', job);
    } else {
     body.set('start', '1');
     body.set('profile', form.elements.profile.value);
     body.set('confirm_delete', '1');
     body.set('revision', form.elements.gnn_revision.value);
    }
    var response = await fetch(config.ajax, {method: 'POST', credentials: 'same-origin', body: body});
    var result = await response.json().catch(function () { return {success: false, data: {message: config.failure}}; });
    if (!response.ok || !result.success) { throw new Error(result.data && result.data.message || config.failure); }
    job = result.data.job;
    button.dataset.job = job;
    if (result.data.phase === 'complete') {
     status.textContent = config.done;
     window.location.assign(result.data.redirect);
     return;
    }
    status.textContent = (result.data.phase === 'import' ? config.installing : config.deleting) + ' (' + result.data.done + ')';
    await new Promise(function (resolve) { window.setTimeout(resolve, 50); });
   }
  } catch (error) {
   status.textContent = error.message || config.failure;
   button.textContent = config.retry;
   button.disabled = false;
   if (dialog) { dialog.querySelector('.gnn-demo-cancel').textContent = config.close; }
  } finally {
   demoRunning = false;
   if (dialog) { dialog.querySelector('.gnn-demo-cancel').disabled = false; }
  }
 }
 if (dialog && demoForm) {
  document.querySelectorAll('.gnn-demo-open').forEach(function (button) {
   button.addEventListener('click', function () {
    demoForm.elements.profile.value = button.dataset.profile;
    dialog.querySelector('#gnn-demo-title').textContent = button.dataset.label;
    dialog.showModal();
   });
  });
  dialog.addEventListener('cancel', function (event) { if (demoRunning) { event.preventDefault(); } });
  dialog.querySelector('.gnn-demo-cancel').addEventListener('click', function () { if (!demoRunning) { dialog.close(); if (demoForm.dataset.attempted) { window.location.assign(config.setup); } } });
  demoForm.addEventListener('submit', function (event) {
   event.preventDefault();
   demoForm.dataset.attempted = '1';
   var button = demoForm.querySelector('.gnn-demo-submit');
   replaceDemo(button, demoForm.querySelector('.gnn-demo-status'), button.dataset.job || '', demoForm);
  });
 }
 document.querySelectorAll('.gnn-demo-resume').forEach(function (button) {
  button.addEventListener('click', function () {
   replaceDemo(button, button.parentNode.querySelector('.gnn-demo-status'), button.dataset.job, null);
  });
 });
})();
