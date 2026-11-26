import assert from 'node:assert/strict';

/**
 * Browser regression suite for GNN theme and Elementor controls.
 *
 * Stages (enabled via GNN_CONTROLS_STAGES comma-separated list, or "all"; defaults to "all"):
 *   - A0: fixture seeding, Elementor panel save/reopen round-trip,
 *     Theme Panel form save/reload, coordinate row-1 measurement, and
 *     AJAX filter/pagination + popstate checks.
 *   - A1..A5: stage-specific acceptance checks.
 */
export async function testControlsBrowser({ browser, server, php, out, plugins = true }) {
 const tests = [];
 const rawStages = process.env.GNN_CONTROLS_STAGES ?? 'all';
 const stages = new Set(
  rawStages.trim().toLowerCase() === 'all'
   ? ['A0', 'A1', 'A2', 'A3', 'A4', 'A5']
   : rawStages.split(',').map((s) => s.trim().toUpperCase()).filter(Boolean)
 );

 const record = async (name, fn) => {
  try {
   await fn();
   tests.push({ name, pass: true });
  } catch (error) {
   tests.push({ name, pass: false, detail: String(error.stack || error) });
   console.log('FAILED:', name, String(error));
  }
 };

 const baseUrl = server.serverUrl;

 async function runPhp(code, admin = false) {
  return (await php(code, admin)).trim();
 }

 async function loginAdmin(page) {
  await runPhp("wp_set_password('gnn-qa-local-only', 1);", true);
  for (let attempt = 0; attempt < 2; attempt++) {
   await page.goto(`${baseUrl}/wp-login.php?redirect_to=${encodeURIComponent(`${baseUrl}/wp-admin/`)}`, { waitUntil: 'domcontentloaded', timeout: 60000 });
   await page.locator('#user_login').fill('admin');
   await page.locator('#user_pass').fill('gnn-qa-local-only');
   await Promise.all([
    page.waitForURL(/\/wp-admin/, { timeout: 60000 }),
    page.locator('#wp-submit').click(),
   ]);
   if ((await page.context().cookies()).some((c) => c.name.startsWith('wordpress_logged_in_'))) {
    return;
   }
  }
  assert.fail('Admin login failed in controls-browser suite.');
 }

 async function dismissElementorOverlays(page) {
  await page.evaluate(() => {
   const closeSelectors = [
    '.MuiDialog-root button[aria-label="close"]',
    '.MuiDialog-root button[aria-label="Close"]',
    '#elementor-notice-bar__close',
    '.dialog-close-button',
   ];
   for (const sel of closeSelectors) {
    document.querySelectorAll(sel).forEach((btn) => btn.click());
   }
  });
 }

 async function openElementorEditor(page, postId, firstWidgetId) {
  await page.goto(`${baseUrl}/wp-admin/post.php?post=${postId}&action=elementor`, { waitUntil: 'domcontentloaded', timeout: 120000 });
  await page.locator('#elementor-loading').waitFor({ state: 'hidden', timeout: 120000 }).catch(() => {});
  await page.waitForFunction(
   (wid) => {
    const el = window.elementor;
    if (!el?.loaded || !el?.documents?.getCurrent() || !el.getPreviewView?.()?.children) return false;
    const previewDoc = el.$previewContents?.[0];
    return !!(previewDoc && previewDoc.querySelector(`[data-id="${wid}"]`));
   },
   firstWidgetId,
   { timeout: 120000 }
  );
  await dismissElementorOverlays(page);
 }

 async function selectElementorWidget(page, widgetId) {
  await dismissElementorOverlays(page);
  const frame = page.frameLocator('#elementor-preview-iframe');
  const widget = frame.locator(`[data-id="${widgetId}"]`).first();
  const scrollBelowHeader = () => widget.evaluate((el) => {
   const win = el.ownerDocument.defaultView;
   const y = el.getBoundingClientRect().top + win.scrollY - 140;
   win.scrollTo({ top: Math.max(0, y), behavior: 'instant' });
  });
  await scrollBelowHeader();
  await widget.click({ position: { x: 20, y: 20 } });
  try {
   await page.locator('#elementor-controls .elementor-control').first().waitFor({ state: 'visible', timeout: 15000 });
  } catch {
   await scrollBelowHeader();
   await widget.hover();
   const editBtn = widget.locator('.elementor-editor-element-edit').first();
   if (await editBtn.isVisible().catch(() => false)) {
    await editBtn.click();
   } else {
    await widget.click({ position: { x: 20, y: 20 }, force: true });
   }
   try {
    await page.locator('#elementor-controls .elementor-control').first().waitFor({ state: 'visible', timeout: 30000 });
   } catch (error) {
    const geometry=await widget.evaluate(el=>{
     const doc=el.ownerDocument;
     const root=el.getBoundingClientRect();
     const header=doc.querySelector('#masthead')?.getBoundingClientRect();
     const hit=doc.elementFromPoint(root.x+20,root.y+20);
     return {widgetTop:root.top,headerBottom:header?.bottom,hit:hit?.className,insideWidget:el.contains(hit)};
    }).catch(()=>null);
    if(out) await page.screenshot({path:`${out}/elementor-${widgetId}-selection.png`,timeout:5000}).catch(()=>{});
    throw new Error(`Elementor did not open controls for ${widgetId}. ${JSON.stringify(geometry)}\n${error.message}`,{cause:error});
   }
  }
 }

 async function switchElementorTab(page, tab) {
  const tabLink = page.locator(`.elementor-panel-navigation-tab[data-tab="${tab}"]`).first();
  await tabLink.waitFor({ state: 'visible', timeout: 30000 });
  const isActive = await tabLink.evaluate((el) => el.classList.contains('elementor-active'));
  if (!isActive) {
   await tabLink.click();
  }
 }

 async function openElementorSection(page, sectionClass) {
  if (!sectionClass) return;
  const sec = page.locator(`#elementor-controls .elementor-control-${sectionClass}`).first();
  await sec.waitFor({ state: 'attached', timeout: 30000 });
  const isOpen = await sec.evaluate((el) => el.classList.contains('elementor-open'));
  if (!isOpen) {
   await sec.locator('.elementor-panel-heading').click();
   await sec.waitFor({ state: 'visible', timeout: 30000 });
  }
 }

 async function switchResponsiveDevice(page, controlLocator, device) {
  const target = device || 'desktop';
  const switcherHolder = controlLocator.locator('.elementor-control-responsive-switchers__holder').first();
  if (!(await switcherHolder.count())) return;
  const targetBtn = switcherHolder.locator(`button.elementor-responsive-switcher[data-device="${target}"]`).first();
  if (!(await targetBtn.isVisible())) {
   const visibleBtn = switcherHolder.locator('button.elementor-responsive-switcher:visible').first();
   if (await visibleBtn.count()) {
    await visibleBtn.click();
   }
  }
  await targetBtn.waitFor({ state: 'visible', timeout: 10000 });
  await targetBtn.click();
 }

 async function setElementorControl(page, { tab = 'content', sectionClass = null, setting, value, device = null }) {
  await switchElementorTab(page, tab);
  await openElementorSection(page, sectionClass);
  const fullKey = device && device !== 'desktop' ? `${setting}_${device}` : setting;
  const controlWrap = page.locator(`#elementor-controls .elementor-control-${fullKey}`).first();
  if (device) {
   const baseControl = page.locator(`#elementor-controls .elementor-control-${setting}`).first();
   if (await baseControl.count()) {
    await switchResponsiveDevice(page, baseControl, device);
   }
  }
  await controlWrap.waitFor({ state: 'visible', timeout: 30000 });
  const field = controlWrap.locator(`[data-setting="${fullKey}"]`).first();
  await field.waitFor({ state: 'visible', timeout: 30000 });
  const tag = await field.evaluate((el) => el.tagName.toLowerCase());
  if (tag === 'select') {
   await field.selectOption(String(value));
  } else {
   await field.fill(String(value));
   await field.dispatchEvent('input');
   await field.dispatchEvent('change');
  }
 }

 async function readElementorControl(page, { tab = 'content', sectionClass = null, setting, device = null }) {
  await switchElementorTab(page, tab);
  await openElementorSection(page, sectionClass);
  const fullKey = device && device !== 'desktop' ? `${setting}_${device}` : setting;
  if (device) {
   const baseControl = page.locator(`#elementor-controls .elementor-control-${setting}`).first();
   if (await baseControl.count()) {
    await switchResponsiveDevice(page, baseControl, device);
   }
  }
  const field = page.locator(`#elementor-controls .elementor-control-${fullKey} [data-setting="${fullKey}"]`).first();
  await field.waitFor({ state: 'visible', timeout: 30000 });
  return await field.inputValue();
 }

 async function saveElementorViaButton(page) {
  const publishLabelRe = /^(publish|update|yayınla|yayımla|güncelle)$/i;
  const diagButtons = async () => await page.evaluate(() => Array.from(document.querySelectorAll('#elementor-editor-wrapper-v2 button, #elementor-panel-footer button')).map((b) => ({
   id: b.id || null,
   text: (b.textContent || '').trim(),
   aria: b.getAttribute('aria-label'),
   disabled: b.disabled,
   visible: !!(b.offsetWidth || b.offsetHeight || b.getClientRects().length),
  })));
  await page.waitForFunction(
   () => Array.from(document.querySelectorAll('#elementor-editor-wrapper-v2 button, #elementor-panel-saver-button-publish')).some((b) => {
    const t = (b.textContent || '').trim();
    const visible = !!(b.offsetWidth || b.offsetHeight || b.getClientRects().length);
    return visible && (/^(publish|update|yayınla|güncelle)$/i.test(t) || b.id === 'elementor-panel-saver-button-publish');
   }),
   null,
   { timeout: 30000 }
  ).catch(async (err) => {
   throw new Error(`Publish button not found/visible (${err.message}). Buttons: ${JSON.stringify(await diagButtons())}`);
  });
  const publishBtn = page.locator('#elementor-editor-wrapper-v2 button:visible, #elementor-panel-saver-button-publish:visible').filter({ hasText: publishLabelRe }).first();
  await page.waitForFunction(
   () => {
    const candidates = Array.from(document.querySelectorAll('#elementor-editor-wrapper-v2 button, #elementor-panel-saver-button-publish'));
    const btn = candidates.find((b) => {
     const visible = !!(b.offsetWidth || b.offsetHeight || b.getClientRects().length);
     const t = (b.textContent || '').trim();
     return visible && (/^(publish|update|yayınla|güncelle)$/i.test(t) || b.id === 'elementor-panel-saver-button-publish');
    });
    return !!(btn && !btn.disabled && btn.getAttribute('aria-disabled') !== 'true' && !btn.classList.contains('elementor-disabled'));
   },
   null,
   { timeout: 30000 }
  ).catch(async (err) => {
   throw new Error(`Publish button stayed disabled (${err.message}). Buttons: ${JSON.stringify(await diagButtons())}`);
  });
  await Promise.all([
   page.waitForResponse(
    (resp) => {
     if (!resp.url().includes('admin-ajax.php') || resp.request().method() !== 'POST' || resp.status() !== 200) return false;
     const body = resp.request().postDataBuffer()?.toString('utf8') || resp.request().postData() || '';
     return body.includes('save_builder');
    },
    { timeout: 30000 }
   ),
   publishBtn.click(),
  ]);
  await page.waitForFunction(
   () => {
    const candidates = Array.from(document.querySelectorAll('#elementor-editor-wrapper-v2 button, #elementor-panel-saver-button-publish'));
    const btn = candidates.find((b) => {
     const visible = !!(b.offsetWidth || b.offsetHeight || b.getClientRects().length);
     const t = (b.textContent || '').trim();
     return visible && (/^(publish|update|yayınla|güncelle)$/i.test(t) || b.id === 'elementor-panel-saver-button-publish');
    });
    return !btn || btn.disabled || btn.getAttribute('aria-disabled') === 'true' || btn.classList.contains('elementor-disabled');
   },
   null,
   { timeout: 30000 }
  );
 }

 async function openPanelTab(page, tab) {
  const targetUrl = `${baseUrl}/wp-admin/admin.php?page=gnn-panel#gnn-tab-${tab}`;
  if (page.url().split('#')[0] === targetUrl.split('#')[0]) {
   await page.goto('about:blank');
  }
  await page.goto(targetUrl, { waitUntil: 'load', timeout: 60000 });
  await page.locator(`#gnn-nav-${tab}`).click();
  await page.locator(`#gnn-tab-${tab}:not([hidden])`).waitFor({ state: 'visible', timeout: 30000 });
 }

 async function savePanel(page) {
  const saveBtn = page.locator('#gnn-settings-form .gnn-btn-save').first();
  await Promise.all([
   page.waitForNavigation({ waitUntil: 'load', timeout: 30000 }),
   saveBtn.click(),
  ]);
 }

 /**
  * Measures a grid container by both computed CSS track count AND actual
  * row-1 card count derived from getBoundingClientRect() x/y coordinates.
  */
 async function measureGrid(page, selector) {
  return await page.evaluate((sel) => {
   const grid = document.querySelector(sel);
   if (!grid) return { found: false, sel };
   const cs = getComputedStyle(grid);
   const tracks = cs.gridTemplateColumns && cs.gridTemplateColumns !== 'none'
    ? cs.gridTemplateColumns.trim().split(/\s+/).length
    : 0;
   const items = Array.from(grid.children).filter((el) => {
    const r = el.getBoundingClientRect();
    return r.width > 0 && r.height > 0 && getComputedStyle(el).display !== 'none';
   });
   const rects = items.map((el) => {
    const r = el.getBoundingClientRect();
    return { top: r.top, left: r.left, right: r.right, bottom: r.bottom };
   });
   const top0 = rects.length ? Math.min(...rects.map((r) => r.top)) : 0;
   const row1Indices = rects
    .map((r, i) => ({ r, i }))
    .filter(({ r }) => Math.abs(r.top - top0) <= 6)
    .sort((a, b) => a.r.left - b.r.left);
   const row1DistinctLefts = [];
   for (const { r } of row1Indices) {
    if (!row1DistinctLefts.some((lx) => Math.abs(lx - r.left) <= 6)) {
     row1DistinctLefts.push(r.left);
    }
   }
   const row1Overlaps = row1Indices.some((entry, idx) => idx > 0 && entry.r.left < row1Indices[idx - 1].r.right - 2);
   const h3 = items[0]?.querySelector('h3, h2, .woocommerce-loop-product__title');
   const h3Style = h3 ? getComputedStyle(h3) : null;
   const itemIds = items.map((el) => el.dataset.postId || el.dataset.productId || el.getAttribute('href') || el.querySelector('a')?.getAttribute('href') || el.textContent?.trim().slice(0, 32));
   const titles = items.map((el) => (el.querySelector('h3, h2, .woocommerce-loop-product__title')?.textContent || '').trim());
   return {
    found: true,
    display: cs.display,
    tracks,
    itemCount: items.length,
    row1Cols: row1Indices.length,
    row1DistinctX: row1DistinctLefts.length,
    row1Overlaps,
    itemIds,
    titles,
    h3FontSize: h3Style?.fontSize || null,
    h3FontFamily: h3Style?.fontFamily || null,
    classes: grid.className,
   };
  }, selector);
 }

 // Isolate state before seeding control-regression fixtures.
 const seedPhp = `
  gnn_reset_panel_settings();
  delete_option('gnn_qa_home_id');
  update_option('gnn_github_updates_enable', 0);
  update_option('elementor_onboarded', true);
  update_option('elementor_checklist', wp_json_encode(array('is_hidden' => true)));
  update_user_meta(1, 'elementor_introduction', array(
   'ai-get-started-announcement' => true,
   'globals_introduction' => true,
   'e-checklist-first-time' => true,
  ));
  update_option('permalink_structure', '/%postname%/');
  flush_rewrite_rules();

  foreach (array('gnn_product', 'gnn_project', 'gnn_album', 'gnn_slide', 'gnn_layout', 'product') as $pt) {
   if (!post_type_exists($pt)) continue;
   foreach (get_posts(array('post_type' => $pt, 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids')) as $id) {
    wp_delete_post($id, true);
   }
  }
  foreach (get_posts(array('post_type' => 'page', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_gnn_qa_controls_fixture')) as $id) {
   wp_delete_post($id, true);
  }
  foreach (array('gnn_product_category', 'gnn_project_category', 'gnn_slide_group', 'product_cat') as $tax) {
   if (!taxonomy_exists($tax)) continue;
   $terms = get_terms(array('taxonomy' => $tax, 'hide_empty' => false));
   if (!is_wp_error($terms)) {
    foreach ($terms as $t) {
     if ('uncategorized' !== $t->slug) wp_delete_term($t->term_id, $tax);
    }
   }
  }

  require_once ABSPATH . 'wp-admin/includes/image.php';
  require_once ABSPATH . 'wp-admin/includes/file.php';
  require_once ABSPATH . 'wp-admin/includes/media.php';

  function gnn_qa_make_png($name, $w, $h, $r, $g, $b) {
   $im = imagecreatetruecolor($w, $h);
   imagefill($im, 0, 0, imagecolorallocate($im, $r, $g, $b));
   ob_start(); imagepng($im); $png = ob_get_clean(); imagedestroy($im);
   $upload = wp_upload_bits($name . '.png', null, $png);
   $id = wp_insert_attachment(array('post_mime_type' => 'image/png', 'post_title' => $name, 'post_status' => 'inherit'), $upload['file']);
   wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $upload['file']));
   return $id;
  }

  $img_land = gnn_qa_make_png('ctrl-land', 800, 600, 30, 90, 140);
  $img_wide = gnn_qa_make_png('ctrl-wide', 1200, 675, 40, 120, 90);
  $gal_imgs = array();
  for ($i = 1; $i <= 6; $i++) {
   $gal_imgs[] = gnn_qa_make_png('ctrl-gal-' . $i, 640, 480, 25 * $i, 80, 150);
  }

  $cat_a = wp_insert_term('CatAlpha', 'gnn_product_category')['term_id'];
  $cat_b = wp_insert_term('CatBeta', 'gnn_product_category')['term_id'];
  $prod_ids = array();
  for ($i = 1; $i <= 8; $i++) {
   $pid = wp_insert_post(array(
    'post_type' => 'gnn_product',
    'post_title' => sprintf('QA Product %02d', $i),
    'post_excerpt' => 'Product summary ' . $i,
    'post_status' => 'publish',
    'post_date' => sprintf('2026-03-%02d 10:00:00', 20 - $i),
   ));
   wp_set_object_terms($pid, array($i <= 5 ? $cat_a : $cat_b), 'gnn_product_category');
   set_post_thumbnail($pid, $img_land);
   update_post_meta($pid, '_gnn_gallery_ids', array_slice($gal_imgs, 0, 6));
   $prod_ids[] = $pid;
  }

  $pcat_1 = wp_insert_term('ProjAlpha', 'gnn_project_category')['term_id'];
  $pcat_2 = wp_insert_term('ProjBeta', 'gnn_project_category')['term_id'];
  $proj_ids = array();
  for ($i = 1; $i <= 8; $i++) {
   $pid = wp_insert_post(array(
    'post_type' => 'gnn_project',
    'post_title' => sprintf('QA Project %02d', $i),
    'post_excerpt' => 'Project summary ' . $i,
    'post_status' => 'publish',
    'post_date' => sprintf('2026-04-%02d 10:00:00', 20 - $i),
   ));
   wp_set_object_terms($pid, array($i <= 5 ? $pcat_1 : $pcat_2), 'gnn_project_category');
   set_post_thumbnail($pid, $img_land);
   update_post_meta($pid, '_gnn_gallery_ids', array_slice($gal_imgs, 0, 6));
   $proj_ids[] = $pid;
  }

  $album_id = wp_insert_post(array('post_type' => 'gnn_album', 'post_title' => 'QA Album 6', 'post_status' => 'publish'));
  update_post_meta($album_id, '_gnn_gallery_ids', $gal_imgs);

  $slide_group = wp_insert_term('QASlides', 'gnn_slide_group')['term_id'];
  for ($i = 1; $i <= 3; $i++) {
   $sid = wp_insert_post(array('post_type' => 'gnn_slide', 'post_title' => 'QA Slide ' . $i, 'post_status' => 'publish', 'menu_order' => $i));
   set_post_thumbnail($sid, $img_wide);
   update_post_meta($sid, '_gnn_slide_kicker', 'Kicker ' . $i);
   update_post_meta($sid, '_gnn_slide_text', 'Mobile slider description line ' . $i);
   update_post_meta($sid, '_gnn_slide_cta_label', 'Action ' . $i);
   update_post_meta($sid, '_gnn_slide_cta_url', home_url('/#slide-' . $i));
   wp_set_object_terms($sid, array($slide_group), 'gnn_slide_group');
  }

  $woo_cats = array();
  $woo_ids = array();
  if (${plugins ? 'true' : 'false'} && class_exists('WC_Product_Simple')) {
   for ($c = 1; $c <= 4; $c++) {
    $t = wp_insert_term('WooCat ' . $c, 'product_cat');
    if (!is_wp_error($t)) $woo_cats[] = $t['term_id'];
   }
   for ($i = 1; $i <= 6; $i++) {
    $product = new WC_Product_Simple();
    $product->set_name('QA Woo Product ' . $i);
    $product->set_regular_price('100');
    $product->set_status('publish');
    $product->set_image_id($img_land);
    if (!empty($woo_cats)) {
     $product->set_category_ids(array($woo_cats[($i - 1) % count($woo_cats)]));
    }
    $woo_ids[] = $product->save();
   }
  }

  function gnn_qa_ctrl_page($title, $post_content, $elements = null) {
   $id = wp_insert_post(array('post_type' => 'page', 'post_title' => $title, 'post_status' => 'publish', 'post_content' => $post_content));
   update_post_meta($id, '_gnn_qa_controls_fixture', 1);
   if (null !== $elements) {
    update_post_meta($id, '_elementor_edit_mode', 'builder');
    update_post_meta($id, '_elementor_template_type', 'wp-page');
    update_post_meta($id, '_elementor_data', wp_slash(wp_json_encode($elements)));
   }
   return $id;
  }

  $el_page = null;
  $el_slider_page = null;
  $el_typo_page = null;
  if (${plugins ? 'true' : 'false'} && did_action('elementor/loaded')) {
   $el_page = gnn_qa_ctrl_page('QA Controls Roundtrip', '', array(array(
    'id' => 'secctrl1',
    'elType' => 'container',
    'settings' => array(),
    'elements' => array(array(
     'id' => 'widcat1',
     'elType' => 'widget',
     'widgetType' => 'gnn-catalog',
     'settings' => array('columns' => 2, 'limit' => 4, 'filters' => 'yes', 'filter_layout' => 'links'),
    )),
   )));

   $el_slider_page = gnn_qa_ctrl_page('QA Slider Control', '', array(array(
    'id' => 'secsl1',
    'elType' => 'container',
    'settings' => array(),
    'elements' => array(array(
     'id' => 'widsl1',
     'elType' => 'widget',
     'widgetType' => 'gnn-slider',
     'settings' => array('category' => $slide_group, 'limit' => 3),
    )),
   )));

   $el_typo_page = gnn_qa_ctrl_page('QA Typo & Buttons', '', array(array(
    'id' => 'sectypo1',
    'elType' => 'container',
    'settings' => array(),
    'elements' => array(
     array(
      'id' => 'widh3plain',
      'elType' => 'widget',
      'widgetType' => 'text-editor',
      'settings' => array('editor' => '<h3>Plain Reference H3</h3>'),
     ),
     array(
      'id' => 'widcatdefault',
      'elType' => 'widget',
      'widgetType' => 'gnn-catalog',
      'settings' => array('columns' => 3, 'limit' => 3, 'filters' => 'no'),
     ),
     array(
      'id' => 'widcatcustom',
      'elType' => 'widget',
      'widgetType' => 'gnn-catalog',
      'settings' => array(
       'columns' => 3,
       'limit' => 3,
       'filters' => 'no',
       'card_title_typography' => 'custom',
       'card_title_font_size' => array('unit' => 'px', 'size' => 26),
       'card_title_font_weight' => '800',
      ),
     ),
     array(
      'id' => 'widbtn1',
      'elType' => 'widget',
      'widgetType' => 'button',
      'settings' => array('text' => 'Slot 1 Button', '_css_classes' => 'gnn-btn-style-1'),
     ),
     array(
      'id' => 'widbtn6',
      'elType' => 'widget',
      'widgetType' => 'button',
      'settings' => array('text' => 'Slot 6 Button', '_css_classes' => 'gnn-btn-style-6'),
     ),
    ),
   )));
  }

  $block_slider_page = gnn_qa_ctrl_page('QA Slider Block', '<!-- wp:gnn/slider {"category":' . $slide_group . '} /-->');
  $block_portfolio_page = gnn_qa_ctrl_page('QA Shared Portfolio Zero', '<!-- wp:gnn/portfolio {"columns":2,"limit":0,"filters":false} /-->');
  $block_portfolio_default_page = gnn_qa_ctrl_page('QA Shared Portfolio Default', '<!-- wp:gnn/portfolio {"columns":2,"filters":false} /-->');
  $block_catalog_filter_page = gnn_qa_ctrl_page('QA Filter Form', '<!-- wp:gnn/catalog {"columns":2,"limit":3,"filters":true,"filter_layout":"select"} /-->');
  $front_page = gnn_qa_ctrl_page('QA Controls Home', '<p>Controls verification home</p>');
  update_option('show_on_front', 'page');
  update_option('page_on_front', $front_page);

  echo wp_json_encode(array(
   'cat_a' => $cat_a,
   'cat_b' => $cat_b,
   'pcat_1' => $pcat_1,
   'album_id' => $album_id,
   'slide_group' => $slide_group,
   'prod_count' => count($prod_ids),
   'proj_count' => count($proj_ids),
   'woo_count' => count($woo_ids),
   'woo_cats' => count($woo_cats),
   'pages' => array(
    'el_page' => $el_page,
    'el_slider_page' => $el_slider_page,
    'el_typo_page' => $el_typo_page,
    'block_slider_page' => $block_slider_page,
    'block_portfolio_page' => $block_portfolio_page,
    'block_portfolio_default_page' => $block_portfolio_default_page,
    'block_catalog_filter_page' => $block_catalog_filter_page,
    'front_page' => $front_page,
   ),
  ));
 `;

 const fixtures = JSON.parse(await runPhp(seedPhp, true));

 const context = await browser.newContext({ viewport: { width: 1440, height: 900 } });
 await context.route('**/*', (route) => (route.request().url().startsWith(baseUrl) ? route.continue() : route.abort()));
 const adminPage = await context.newPage();

 try {
  await loginAdmin(adminPage);

  if (stages.has('A0')) {
   await record('A0-01 controls fixtures meet expected dataset counts', async () => {
    assert.equal(fixtures.prod_count, 8);
    assert.equal(fixtures.proj_count, 8);
    assert.ok(fixtures.cat_a && fixtures.cat_b);
    assert.ok(fixtures.pcat_1);
    const albumImages = Number(await runPhp(`echo count((array) get_post_meta(${fixtures.album_id}, '_gnn_gallery_ids', true));`));
    assert.equal(albumImages, 6);
    const slideCount = Number(await runPhp("echo wp_count_posts('gnn_slide')->publish;"));
    assert.equal(slideCount, 3);
    if (plugins) {
     assert.equal(fixtures.woo_count, 6);
     assert.equal(fixtures.woo_cats, 4);
    }
   });

   await record('A0-03 theme panel saves portfolio_per_page via UI submit button and persists after reload', async () => {
    await openPanelTab(adminPage, 'pages');
    await adminPage.locator('#gnn-portfolio_per_page').fill('6');
    await savePanel(adminPage);
    await openPanelTab(adminPage, 'pages');
    assert.equal(await adminPage.locator('#gnn-portfolio_per_page').inputValue(), '6');
    await adminPage.locator('#gnn-portfolio_per_page').fill('12');
    await savePanel(adminPage);
   });

   if (plugins && fixtures.pages.el_page) {
    await record('A0-02 Elementor panel edits persist via Publish button on reopen and match row-1 coordinate grid', async () => {
     await openElementorEditor(adminPage, fixtures.pages.el_page, 'widcat1');
     await selectElementorWidget(adminPage, 'widcat1');
     await setElementorControl(adminPage, { tab: 'content', setting: 'columns', value: '3' });
     await saveElementorViaButton(adminPage);

     const reopenPage = await context.newPage();
     try {
      await openElementorEditor(reopenPage, fixtures.pages.el_page, 'widcat1');
      await selectElementorWidget(reopenPage, 'widcat1');
      const persisted = await readElementorControl(reopenPage, { tab: 'content', setting: 'columns' });
      assert.equal(persisted, '3', 'Expected reopened Elementor columns field to persist value 3');
     } finally {
      await reopenPage.close();
     }

     const frontPage = await context.newPage();
     await frontPage.setViewportSize({ width: 1440, height: 900 });
     try {
      await frontPage.goto(`${baseUrl}/?page_id=${fixtures.pages.el_page}`, { waitUntil: 'networkidle', timeout: 60000 });
      const m = await measureGrid(frontPage, '[data-id="widcat1"] .gnn-module-grid');
      assert.equal(m.found, true);
      assert.equal(m.tracks, 3);
      assert.equal(m.row1Cols, 3);
      assert.equal(m.row1DistinctX, 3);
      assert.equal(m.row1Overlaps, false);
     } finally {
      await frontPage.close();
     }
    });

    await record('A0-04 AJAX filter, pagination, and browser back/forward preserve grid columns without fixed waits', async () => {
     const frontPage = await context.newPage();
     await frontPage.setViewportSize({ width: 1440, height: 900 });
     try {
      await frontPage.goto(`${baseUrl}/?page_id=${fixtures.pages.el_page}`, { waitUntil: 'networkidle', timeout: 60000 });
      await Promise.all([
       frontPage.waitForFunction((cid) => window.location.search.includes(`gnn_catalog_category=${cid}`), fixtures.cat_a, { timeout: 30000 }),
       frontPage.locator(`.gnn-module-filters a[href*="gnn_catalog_category=${fixtures.cat_a}"]`).first().click(),
      ]);
      await frontPage.waitForFunction(
       () => document.querySelectorAll('[data-id="widcat1"] .gnn-module-product').length === 4,
       null,
       { timeout: 30000 }
      );
      const filteredGrid = await measureGrid(frontPage, '[data-id="widcat1"] .gnn-module-grid');
      assert.equal(filteredGrid.tracks, 3);
      assert.equal(filteredGrid.row1Cols, 3);

      await Promise.all([
       frontPage.waitForFunction(() => window.location.search.includes('gnn_products_page=2'), null, { timeout: 30000 }),
       frontPage.locator('.gnn-module-pagination a.next').first().click(),
      ]);
      await frontPage.waitForFunction(
       () => document.querySelectorAll('[data-id="widcat1"] .gnn-module-product').length === 1,
       null,
       { timeout: 30000 }
      );
      const page2Grid = await measureGrid(frontPage, '[data-id="widcat1"] .gnn-module-grid');
      assert.equal(page2Grid.tracks, 3);
      assert.equal(page2Grid.itemCount, 1);

      await Promise.all([
       frontPage.waitForFunction(() => !window.location.search.includes('gnn_products_page=2'), null, { timeout: 30000 }),
       frontPage.goBack({ waitUntil: 'domcontentloaded' }),
      ]);
      await frontPage.waitForFunction(
       () => document.querySelectorAll('[data-id="widcat1"] .gnn-module-product').length === 4,
       null,
       { timeout: 30000 }
      );
      const backGrid = await measureGrid(frontPage, '[data-id="widcat1"] .gnn-module-grid');
      assert.equal(backGrid.tracks, 3);
      assert.equal(backGrid.itemCount, 4);
     } finally {
      await frontPage.close();
     }
    });
   }
  }

  // =========================================================================
  // Stage A1 — Column priority contract (Biçem > İçerik > Tema > mobil) & Woo
  // =========================================================================
  if (stages.has('A1') && plugins) {
   await record('A1-01 column priority matrix across catalog/portfolio/gallery and viewports', async () => {
    const viewports = [
     { w: 1440, h: 900, bp: 'desktop' },
     { w: 1024, h: 768, bp: 'tablet' },
     { w: 800, h: 900, bp: 'tablet' },
     { w: 768, h: 900, bp: 'tablet' },
     { w: 761, h: 900, bp: 'narrowDesktop' },
     { w: 760, h: 900, bp: 'narrowMid' },
     { w: 540, h: 900, bp: 'narrowSmall' },
     { w: 390, h: 844, bp: 'mobile' },
    ];
    const styles = [
     { name: 'empty', s: {} },
     { name: 'explicit_4_2_1', s: { display_columns: '4', display_columns_tablet: '2', display_columns_mobile: '1' } },
     { name: 'desktop_only_2', s: { display_columns: '2' } },
    ];
    const moduleSpecs = [
     { type: 'gnn-catalog', extra: { limit: 6, filters: 'no' } },
     { type: 'gnn-portfolio', extra: { limit: 6, filters: 'no' } },
     { type: 'gnn-gallery', extra: { limit: 6, source: 'album', record: fixtures.album_id } },
    ];

    // Seed matrix page containing Content 0..4 x Style variants per module, open in Elementor and save once so Elementor generates widget CSS.
    const matrixWidgets = [];
    for (let mi = 0; mi < moduleSpecs.length; mi++) {
     const mod = moduleSpecs[mi];
     for (const contentCol of [0, 1, 2, 3, 4]) {
      for (let si = 0; si < styles.length; si++) {
       const st = styles[si];
       const wid = `m${mi}c${contentCol}s${si}`;
       matrixWidgets.push({
        id: wid,
        elType: 'widget',
        widgetType: mod.type,
        settings: { columns: contentCol, ...mod.extra, ...st.s },
        meta: { mod: mod.type, contentCol, style: st.name },
       });
      }
     }
    }

    const matrixElements = [{ id: 'secmatrix', elType: 'container', settings: {}, elements: matrixWidgets.map(({ meta, ...w }) => w) }];
    const matrixPageId = Number(await runPhp(`
     $o = (array) get_option('gnn_options', array());
     $o['portfolio_columns'] = 3;
     update_option('gnn_options', $o);
     $id = wp_insert_post(array('post_type' => 'page', 'post_title' => 'QA A1 Matrix', 'post_status' => 'publish'));
     update_post_meta($id, '_gnn_qa_controls_fixture', 1);
     update_post_meta($id, '_elementor_edit_mode', 'builder');
     update_post_meta($id, '_elementor_template_type', 'wp-page');
     update_post_meta($id, '_elementor_data', wp_slash('${JSON.stringify(matrixElements).replace(/'/g, "\\'")}'));
     echo $id;
    `, true));

    // Edit one control in Elementor UI and click Save so Elementor compiles frontend CSS naturally.
    await openElementorEditor(adminPage, matrixPageId, matrixWidgets[0].id);
    const editorDialogs = adminPage.locator('.MuiDialog-root:visible').last();
    await adminPage.addLocatorHandler(editorDialogs, async dialog => {
     const close = dialog.getByRole('button', { name: /^(close|kapat)$/i }).first();
     if (await close.isVisible()) await close.click();
     else await adminPage.keyboard.press('Escape');
    });
    try {
     const closeStructure = adminPage.locator('#elementor-navigator__close');
     if (await closeStructure.isVisible()) await closeStructure.click();
     const matrixWidget = adminPage.frameLocator('#elementor-preview-iframe').locator(`[data-id="${matrixWidgets[0].id}"]`);
     // Catalog cards in the preview are interactive links. Use Elementor's own widget edit handle.
     await matrixWidget.hover({ position: { x: 20, y: 20 } });
     await matrixWidget.locator(':scope > .elementor-element-overlay .elementor-editor-element-edit').click();
     await setElementorControl(adminPage, { tab: 'content', setting: 'limit', value: '6' });
    } finally {
     await adminPage.removeLocatorHandler(editorDialogs);
    }
    await setElementorControl(adminPage, { tab: 'content', setting: 'limit', value: '6' });
    // Toggle columns once so document becomes dirty cleanly before real Publish click.
    await setElementorControl(adminPage, { tab: 'content', setting: 'columns', value: '1' });
    await setElementorControl(adminPage, { tab: 'content', setting: 'columns', value: '0' });
    await saveElementorViaButton(adminPage);

    const fp = await context.newPage();
    const mismatches = [];
    try {
     for (const vp of viewports) {
      await fp.setViewportSize({ width: vp.w, height: vp.h });
      await fp.goto(`${baseUrl}/?page_id=${matrixPageId}`, { waitUntil: 'networkidle', timeout: 60000 });
      for (const w of matrixWidgets) {
       const { contentCol, style } = w.meta;
       let expected;
       if (style === 'explicit_4_2_1') {
        expected = vp.w >= 1025 ? 4 : (vp.w >= 768 ? 2 : 1);
       } else if (style === 'desktop_only_2') {
        expected = 2; // Inherited Elementor Biçem stays active even below 760/540 per D1.
       } else {
        const base = contentCol === 0 ? 3 : contentCol; // theme portfolio_columns = 3
        if (vp.w <= 540) expected = 1;
        else if (vp.w <= 760) expected = Math.min(base, 2);
        else expected = base;
       }
       const m = await measureGrid(fp, `[data-id="${w.id}"] .gnn-module-grid`);
       if (m.tracks !== expected || m.row1Cols !== expected) {
        mismatches.push(`${w.id} (${w.meta.mod}, c=${contentCol}, style=${style}) at ${vp.w}px: expected ${expected}, got tracks=${m.tracks}, row1Cols=${m.row1Cols} (${m.classes})`);
       }
      }
     }
     assert.deepEqual(mismatches, [], `Column priority mismatches (${mismatches.length}):\n${mismatches.join('\n')}`);
    } finally {
     await fp.close();
    }
   });

   await record('A1-02 WooCommerce products widget and shop_columns respect 1..6 columns without auto-fit drift', async () => {
    const wooPageId = Number(await runPhp(`
     $id = wp_insert_post(array('post_type' => 'page', 'post_title' => 'QA A1 Woo', 'post_status' => 'publish'));
     update_post_meta($id, '_gnn_qa_controls_fixture', 1);
     update_post_meta($id, '_elementor_edit_mode', 'builder');
     update_post_meta($id, '_elementor_template_type', 'wp-page');
     update_post_meta($id, '_elementor_data', wp_slash(wp_json_encode(array(array(
      'id' => 'secwoo', 'elType' => 'container', 'settings' => array(), 'elements' => array(
       array('id' => 'wwoo1', 'elType' => 'widget', 'widgetType' => 'gnn-products', 'settings' => array('columns' => 1, 'limit' => 6)),
       array('id' => 'wwoo2', 'elType' => 'widget', 'widgetType' => 'gnn-products', 'settings' => array('columns' => 2, 'limit' => 6)),
       array('id' => 'wwoo3', 'elType' => 'widget', 'widgetType' => 'gnn-products', 'settings' => array('columns' => 3, 'limit' => 6)),
       array('id' => 'wwoo4', 'elType' => 'widget', 'widgetType' => 'gnn-products', 'settings' => array('columns' => 4, 'limit' => 6)),
      )
     )))));
     echo $id;
    `, true));

    const fp = await context.newPage();
    try {
     await fp.setViewportSize({ width: 1440, height: 900 });
     await fp.goto(`${baseUrl}/?page_id=${wooPageId}`, { waitUntil: 'networkidle', timeout: 60000 });
     for (const cols of [1, 2, 3, 4]) {
      const m = await measureGrid(fp, `[data-id="wwoo${cols}"] ul.products`);
      assert.equal(m.tracks, cols, `gnn-products columns=${cols} tracks=${m.tracks}`);
      assert.equal(m.row1Cols, cols, `gnn-products columns=${cols} row1Cols=${m.row1Cols}`);
     }

     await fp.setViewportSize({ width: 390, height: 844 });
     await fp.goto(`${baseUrl}/?page_id=${wooPageId}`, { waitUntil: 'networkidle', timeout: 60000 });
     for (const cols of [1, 2, 3, 4]) {
      const m = await measureGrid(fp, `[data-id="wwoo${cols}"] ul.products`);
      assert.equal(m.tracks, 1, `gnn-products columns=${cols} at 390px expected 1 track, got ${m.tracks}`);
      assert.equal(m.row1Cols, 1, `gnn-products columns=${cols} at 390px expected 1 row-1 card, got ${m.row1Cols}`);
     }

     await fp.setViewportSize({ width: 1440, height: 900 });
     for (const shopCols of [2, 3, 4, 6]) {
      await openPanelTab(adminPage, 'pages');
      await adminPage.locator('#gnn-shop_columns').fill(String(shopCols));
      await savePanel(adminPage);
      await fp.goto(`${baseUrl}/?post_type=product`, { waitUntil: 'networkidle', timeout: 60000 });
      const m = await measureGrid(fp, 'ul.products');
      assert.equal(m.tracks, shopCols, `Shop archive shop_columns=${shopCols} tracks=${m.tracks}`);
      assert.equal(m.row1Cols, shopCols, `Shop archive shop_columns=${shopCols} row1Cols=${m.row1Cols}`);
     }
     await fp.setViewportSize({ width: 390, height: 844 });
     await fp.goto(`${baseUrl}/?post_type=product`, { waitUntil: 'networkidle', timeout: 60000 });
     const shopMobile = await measureGrid(fp, 'ul.products');
     assert.equal(shopMobile.tracks, 1, `Shop archive at 390px expected 1 track, got ${shopMobile.tracks}`);
     assert.equal(shopMobile.row1Cols, 1, `Shop archive at 390px expected 1 row-1 card, got ${shopMobile.row1Cols}`);
    } finally {
     await fp.close();
    }
   });
  }

  // =========================================================================
  // Stage A2 — Hidden legacy header layout & empty accent_light preservation
  // =========================================================================
  if (stages.has('A2')) {
   await record('A2-01 explicit Classic structure overrides the centered legacy baseline', async () => {
    await runPhp(`
     $o = (array) get_option('gnn_options', array());
     $o['header_preset'] = 'legacy';
     $o['header_layout'] = 'centered';
     update_option('gnn_options', $o);
    `, true);
    await openPanelTab(adminPage, 'header');
    await adminPage.locator('label[for="gnn-header_structure-classic"]').click();
    await savePanel(adminPage);

    const fp = await context.newPage();
    try {
     for (const vp of [{ width: 1440, height: 900 }, { width: 1024, height: 768 }, { width: 390, height: 844 }]) {
      await fp.setViewportSize(vp);
      await fp.goto(`${baseUrl}/?page_id=${fixtures.pages.front_page}`, { waitUntil: 'networkidle', timeout: 60000 });
      const hasCentered = await fp.locator('.site-header.gnn-header-centered, header.gnn-header-centered').count();
      assert.equal(hasCentered, 0, `Unexpected .gnn-header-centered at ${vp.width}px after saving header_structure=classic`);
     }
    } finally {
     await fp.close();
    }
   });

   await record('A2-02 empty accent_light stays empty across dark accent save and light mode inherits dark accent', async () => {
    await runPhp(`
     $o = (array) get_option('gnn_options', array());
     $o['accent_light'] = '';
     update_option('gnn_options', $o);
     set_theme_mod('gnn_accent_color', '#2266dd');
    `, true);
    await openPanelTab(adminPage, 'styles');
    assert.equal(await adminPage.locator('#gnn-accent_light').inputValue(), '');
    await adminPage.locator('#gnn-accent').fill('#e11d48');
    await savePanel(adminPage);

    const panelErrors = await adminPage.evaluate(() => Array.from(document.querySelectorAll('.notice-error, .settings-error')).map((el) => el.textContent.trim()));
    assert.equal(await runPhp("echo get_theme_mod('gnn_accent_color');"), '#e11d48', `Dark accent theme_mod did not update on panel save. Panel errors: ${JSON.stringify(panelErrors)}`);
    assert.equal(await runPhp("echo gnn_option('accent_light');"), '');
    await openPanelTab(adminPage, 'styles');
    assert.equal(await adminPage.locator('#gnn-accent_light').inputValue(), '');

    const fp = await context.newPage();
    try {
     await fp.goto(`${baseUrl}/?page_id=${fixtures.pages.front_page}`, { waitUntil: 'networkidle', timeout: 60000 });
     const lightAccent = await fp.evaluate(() => {
      document.documentElement.setAttribute('data-theme', 'light');
      return getComputedStyle(document.documentElement).getPropertyValue('--accent').trim().toLowerCase();
     });
     assert.equal(lightAccent, '#e11d48', `Light mode --accent should inherit #e11d48, got ${lightAccent}`);
    } finally {
     await fp.close();
    }
   });
  }

  // =========================================================================
  // Stage A3 — Item limits: shared portfolio, catalog limit=0, gallery, slider, products max 24
  // =========================================================================
  if (stages.has('A3')) {
   await record('A3-01 portfolio_per_page=2 governs shared portfolio block and catalog limit=0', async () => {
    await openPanelTab(adminPage, 'pages');
    await adminPage.locator('#gnn-portfolio_per_page').fill('2');
    await savePanel(adminPage);

    const fp = await context.newPage();
    try {
     await fp.goto(`${baseUrl}/?page_id=${fixtures.pages.block_portfolio_default_page}`, { waitUntil: 'networkidle', timeout: 60000 });
     const defCount = await fp.locator('.gnn-content-module[data-gnn-collection="portfolio"] .gnn-module-project').count();
     assert.equal(defCount, 2, `Expected shared portfolio default block to show 2 projects per portfolio_per_page=2, got ${defCount}`);

     await fp.goto(`${baseUrl}/?page_id=${fixtures.pages.block_portfolio_page}`, { waitUntil: 'networkidle', timeout: 60000 });
     const zeroCount = await fp.locator('.gnn-content-module[data-gnn-collection="portfolio"] .gnn-module-project').count();
     assert.equal(zeroCount, 2, `Expected shared portfolio block limit=0 to show 2 projects per portfolio_per_page=2, got ${zeroCount}`);
    } finally {
     await fp.close();
    }
   });

   if (plugins && fixtures.pages.el_slider_page) {
    await record('A3-02 gallery limit=3, slider hides columns & applies limit=1, and products control max is 24', async () => {
     const galPageId = Number(await runPhp(`
      for ($i = 7; $i <= 25; $i++) {
       $product = new WC_Product_Simple();
       $product->set_name('QA Woo Extra ' . $i);
       $product->set_regular_price('100');
       $product->set_status('publish');
       $product->save();
      }
      $id = wp_insert_post(array('post_type' => 'page', 'post_title' => 'QA A3 Modules', 'post_status' => 'publish'));
      update_post_meta($id, '_gnn_qa_controls_fixture', 1);
      update_post_meta($id, '_elementor_edit_mode', 'builder');
      update_post_meta($id, '_elementor_template_type', 'wp-page');
      update_post_meta($id, '_elementor_data', wp_slash(wp_json_encode(array(array(
       'id' => 'seca3', 'elType' => 'container', 'settings' => array(), 'elements' => array(
        array('id' => 'wgal3', 'elType' => 'widget', 'widgetType' => 'gnn-gallery', 'settings' => array('source' => 'album', 'record' => ${fixtures.album_id}, 'limit' => 3)),
        array('id' => 'wgalproj', 'elType' => 'widget', 'widgetType' => 'gnn-gallery', 'settings' => array('source' => 'projects', 'limit' => 1)),
        array('id' => 'wcat0', 'elType' => 'widget', 'widgetType' => 'gnn-catalog', 'settings' => array('limit' => 0, 'filters' => 'no')),
        array('id' => 'wprod25', 'elType' => 'widget', 'widgetType' => 'gnn-products', 'settings' => array('columns' => 4, 'limit' => 25)),
       )
      )))));
      echo $id;
     `, true));

     await openElementorEditor(adminPage, fixtures.pages.el_slider_page, 'widsl1');
     await selectElementorWidget(adminPage, 'widsl1');
     await switchElementorTab(adminPage, 'content');
     const colsVisible = await adminPage.locator('#elementor-controls .elementor-control-columns').isVisible();
     assert.equal(colsVisible, false, 'columns control should be hidden for gnn-slider');
     await setElementorControl(adminPage, { tab: 'content', setting: 'limit', value: '1' });
     await saveElementorViaButton(adminPage);

     await openElementorEditor(adminPage, galPageId, 'wprod25');
     await selectElementorWidget(adminPage, 'wprod25');
     await switchElementorTab(adminPage, 'content');
     const limitMax = await adminPage.locator('#elementor-controls .elementor-control-limit [data-setting="limit"]').first().getAttribute('max');
     assert.equal(limitMax, '24', `Expected gnn-products limit max="24", got ${limitMax}`);

     const fp = await context.newPage();
     try {
      await fp.goto(`${baseUrl}/?page_id=${fixtures.pages.el_slider_page}`, { waitUntil: 'networkidle', timeout: 60000 });
      assert.equal(await fp.locator('[data-id="widsl1"] .gnn-hero__slide').count(), 1);
      assert.equal(await fp.locator('[data-id="widsl1"] .gnn-hero__arrow').count(), 0);
      assert.equal(await fp.locator('[data-id="widsl1"] .gnn-hero__dot').count(), 0);

      await fp.goto(`${baseUrl}/?page_id=${galPageId}`, { waitUntil: 'networkidle', timeout: 60000 });
      assert.equal(await fp.locator('[data-id="wgal3"] a.gnn-lightbox').count(), 3);
      assert.ok((await fp.locator('[data-id="wgalproj"] a.gnn-lightbox').count()) > 1, 'projects source limit should limit project count rather than truncating gallery images');
      assert.equal(await fp.locator('[data-id="wcat0"] .gnn-module-product').count(), 2);
      assert.equal(await fp.locator('[data-id="wprod25"] ul.products li.product').count(), 24);
     } finally {
      await fp.close();
     }
    });
   }
  }

  // =========================================================================
  // Stage A4 — Plain permalink filter fallback & mobile slider arrows
  // =========================================================================
  if (stages.has('A4')) {
   await record('A4-01 collection select filter works without JS and with JS on plain and pretty permalinks', async () => {
    for (const structure of ['', '/%postname%/']) {
     await runPhp(`update_option('permalink_structure', '${structure}'); flush_rewrite_rules();`, true);
     const noJsCtx = await browser.newContext({ viewport: { width: 1280, height: 800 }, javaScriptEnabled: false });
     const noJsPage = await noJsCtx.newPage();
     try {
      await noJsPage.goto(`${baseUrl}/?page_id=${fixtures.pages.block_catalog_filter_page}`, { waitUntil: 'networkidle', timeout: 60000 });
      await noJsPage.locator('select[name="gnn_catalog_category"]').first().selectOption(String(fixtures.cat_a));
      await Promise.all([
       noJsPage.waitForNavigation({ waitUntil: 'networkidle', timeout: 30000 }),
       noJsPage.locator('.gnn-module-filter-form button[type="submit"], .gnn-module-filters button[type="submit"], .gnn-module-filters input[type="submit"]').first().click(),
      ]);
      const finalUrl = noJsPage.url();
      assert.ok(finalUrl.includes(`gnn_catalog_category=${fixtures.cat_a}`), `no-JS URL missing category filter: ${finalUrl}`);
      if (!structure) {
       assert.ok(finalUrl.includes(`page_id=${fixtures.pages.block_catalog_filter_page}`), `no-JS plain permalink lost page_id: ${finalUrl}`);
      } else {
       assert.ok(finalUrl.includes('/qa-filter-form'), `no-JS pretty permalink landed on wrong path: ${finalUrl}`);
      }
      const noJsState = await noJsPage.evaluate(() => ({
       heading: (document.querySelector('h1.entry-title, .page-title, title')?.textContent || '').trim(),
       kickers: Array.from(document.querySelectorAll('.gnn-content-module[data-gnn-collection="catalog"] .gnn-module-product .gnn-module-kicker')).map((k) => k.textContent.trim()),
      }));
      assert.equal(noJsState.kickers.length, 3, 'no-JS filtered page must remain on collection page and show 3 matching products');
      assert.ok(noJsState.kickers.every((k) => k.includes('CatAlpha')), `no-JS products did not belong exclusively to CatAlpha: ${JSON.stringify(noJsState.kickers)}`);
     } finally {
      await noJsCtx.close();
     }

     const jsPage = await context.newPage();
     try {
      await jsPage.goto(`${baseUrl}/?page_id=${fixtures.pages.block_catalog_filter_page}`, { waitUntil: 'networkidle', timeout: 60000 });
      await jsPage.locator('select[name="gnn_catalog_category"]').first().selectOption(String(fixtures.cat_b));
      await Promise.all([
       jsPage.waitForFunction((cid) => window.location.search.includes(`gnn_catalog_category=${cid}`), fixtures.cat_b, { timeout: 30000 }),
       jsPage.locator('.gnn-module-filter-form button[type="submit"], .gnn-module-filters button[type="submit"], .gnn-module-filters input[type="submit"]').first().click(),
      ]);
      await jsPage.waitForFunction(
       () => Array.from(document.querySelectorAll('.gnn-content-module[data-gnn-collection="catalog"] .gnn-module-product .gnn-module-kicker')).length === 3
        && Array.from(document.querySelectorAll('.gnn-content-module[data-gnn-collection="catalog"] .gnn-module-product .gnn-module-kicker')).every((k) => k.textContent.includes('CatBeta')),
       null,
       { timeout: 30000 }
      );
     } finally {
      await jsPage.close();
     }
    }
   });

   await record('A4-02 mobile 390x844 slider arrows sit clear of title, text, and CTA with >=44px target', async () => {
    const visitorCtx = await browser.newContext({ viewport: { width: 390, height: 844 } });
    await visitorCtx.route('**/*', (route) => (route.request().url().startsWith(baseUrl) ? route.continue() : route.abort()));
    const fp = await visitorCtx.newPage();
    try {
     await fp.goto(`${baseUrl}/?page_id=${fixtures.pages.block_slider_page}`, { waitUntil: 'networkidle', timeout: 60000 });
     const hero = fp.locator('.gnn-hero').first();
     await hero.scrollIntoViewIfNeeded();
     const geom = await fp.evaluate(() => {
      const arrows = Array.from(document.querySelectorAll('.gnn-hero__arrow'));
      const obstacles = Array.from(document.querySelectorAll('.gnn-hero__slide.is-active .entry-kicker, .gnn-hero__slide.is-active .gnn-hero__title, .gnn-hero__slide.is-active .gnn-hero__text, .gnn-hero__slide.is-active .gnn-hero__actions .gnn-btn'));
      const overlaps = (a, b) => a.left < b.right && a.right > b.left && a.top < b.bottom && a.bottom > b.top;
      const results = arrows.map((arrow) => {
       const r = arrow.getBoundingClientRect();
       const cx = r.left + r.width / 2;
       const cy = r.top + r.height / 2;
       const topEl = document.elementFromPoint(cx, cy);
       const hitArrow = !!(topEl && (topEl === arrow || arrow.contains(topEl)));
       const intersects = obstacles.some((ob) => overlaps(r, ob.getBoundingClientRect()));
       return { width: r.width, height: r.height, hitArrow, intersects };
      });
      const cta = document.querySelector('.gnn-hero__slide.is-active .gnn-hero__actions .gnn-btn');
      let ctaClickable = false;
      if (cta) {
       const cr = cta.getBoundingClientRect();
       const hitCta = document.elementFromPoint(cr.left + cr.width / 2, cr.top + cr.height / 2);
       ctaClickable = !!(hitCta && (hitCta === cta || cta.contains(hitCta)));
      }
      return { count: arrows.length, results, ctaPresent: !!cta, ctaClickable };
     });
     assert.equal(geom.count, 2);
     assert.equal(geom.ctaPresent, true, 'Active slide CTA link must exist at 390x844');
     assert.equal(geom.ctaClickable, true, 'CTA center must be clickable at 390x844');
     for (const a of geom.results) {
      assert.ok(a.width >= 44 && a.height >= 44, `Arrow target too small: ${a.width}x${a.height}`);
      assert.equal(a.intersects, false, 'Mobile slider arrow must not overlap text or CTA');
      assert.equal(a.hitArrow, true, 'Mobile slider arrow center must hit the arrow button');
     }
    } finally {
     await visitorCtx.close();
    }
   });
  }

  // =========================================================================
  // Stage A5 — Card H3 theme typography scale & Elementor button styles
  // =========================================================================
  if (stages.has('A5') && plugins && fixtures.pages.el_typo_page) {
   await record('A5-01 card h3 inherits theme type_h3_size matching plain page H3 unless Elementor custom typography overrides it', async () => {
    await openPanelTab(adminPage, 'typography');
    const typeScaleDetails = adminPage.locator('#gnn-design-type_scale');
    if (!(await typeScaleDetails.evaluate((el) => el.hasAttribute('open')))) {
     await typeScaleDetails.locator('> summary').click();
    }
    const h3Subgroup = typeScaleDetails.locator('.gnn-design-subgroup', { hasText: 'H3' }).first();
    if (!(await h3Subgroup.evaluate((el) => el.hasAttribute('open')))) {
     await h3Subgroup.locator('> summary').click();
    }
    await adminPage.locator('#gnn-type_h3_size').fill('30');
    await savePanel(adminPage);

    const fp = await context.newPage();
    try {
     for (const vp of [{ width: 1440, height: 900 }, { width: 1024, height: 768 }, { width: 390, height: 844 }]) {
      await fp.setViewportSize(vp);
      await fp.goto(`${baseUrl}/?page_id=${fixtures.pages.el_typo_page}`, { waitUntil: 'networkidle', timeout: 60000 });
      const plainH3Size = await fp.evaluate(() => getComputedStyle(document.querySelector('[data-id="widh3plain"] h3')).fontSize);
      const inherited = await measureGrid(fp, '[data-id="widcatdefault"] .gnn-module-grid');
      const custom = await measureGrid(fp, '[data-id="widcatcustom"] .gnn-module-grid');
      assert.equal(inherited.h3FontSize, plainH3Size, `Expected card H3 (${inherited.h3FontSize}) to match plain H3 (${plainH3Size}) at ${vp.width}px`);
      assert.equal(plainH3Size, '30px', `Expected theme plain H3 30px at ${vp.width}px, got ${plainH3Size}`);
      assert.equal(custom.h3FontSize, '26px', `Expected custom card H3 26px at ${vp.width}px, got ${custom.h3FontSize}`);
     }
    } finally {
     await fp.close();
    }
   });

   await record('A5-02 gnn-btn-style-1 and gnn-btn-style-6 target Elementor inner .elementor-button', async () => {
    await runPhp(`
     $o = (array) get_option('gnn_options', array());
     $o['btn_style_1_label'] = 'Primary Custom';
     $o['btn_style_1_bg'] = '#112233';
     $o['btn_style_1_text'] = '#ffffff';
     $o['btn_style_6_label'] = 'Secondary Custom';
     $o['btn_style_6_bg'] = '#445566';
     $o['btn_style_6_text'] = '#ffeedd';
     update_option('gnn_options', $o);
    `, true);
    const fp = await context.newPage();
    try {
     await fp.goto(`${baseUrl}/?page_id=${fixtures.pages.el_typo_page}`, { waitUntil: 'networkidle', timeout: 60000 });
     const styles = await fp.evaluate(() => ({
      b1: getComputedStyle(document.querySelector('[data-id="widbtn1"] .elementor-button')).backgroundColor,
      t1: getComputedStyle(document.querySelector('[data-id="widbtn1"] .elementor-button')).color,
      b6: getComputedStyle(document.querySelector('[data-id="widbtn6"] .elementor-button')).backgroundColor,
      t6: getComputedStyle(document.querySelector('[data-id="widbtn6"] .elementor-button')).color,
     }));
     assert.equal(styles.b1, 'rgb(17, 34, 51)');
     assert.equal(styles.t1, 'rgb(255, 255, 255)');
     assert.equal(styles.b6, 'rgb(68, 85, 102)');
     assert.equal(styles.t6, 'rgb(255, 238, 221)');
    } finally {
     await fp.close();
    }
   });
  }
 } finally {
  await context.close();
 }

 return tests;
}
