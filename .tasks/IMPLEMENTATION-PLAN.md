# GNN Tema — Sayfa Düzeni, Öne Çıkarılan Görsel, Sidebar, Dinamik Boşluk ve Google Icons Spesifikasyonu (PRD & Implementation Plan)

> **Amaç:** Bu doküman, GNN WordPress temasına eklenecek/düzenlenecek yeni sayfa düzeni, Elementor başlık/breadcrumb entegrasyonu, öne çıkarılan görsel yerleşimi (Sidebar ve içerik üstünde), sidebar dikey hizalaması, dinamik içerik üst/alt boşluğu ve **Google Material Symbols / Icons (fonts.google.com/icons) entegrasyonunun** AI Agent (Claude Code) tarafından eksiksiz uygulanabilmesi için hazırlanmış teknik spesifikasyondur.

---

## 📋 Kullanıcı Talepleri & Teknik Spesifikasyonlar

### 1. İletişim Şablonunun (Contact Template) Kaldırılması
- **Gereksinim:** `page-templates/page-contact.php` şablonu tamamen silinecektir.
- **Kapsam:**
  - `gnn/page-templates/page-contact.php` dosyası silinecek.
  - `gnn/inc/page-layouts.php` içerisindeki `gnn_translate_page_templates()` fonksiyonundan `page-contact.php` kaldırılacak.
  - `gnn/inc/template-tags.php` içerisindeki `gnn_breadcrumb()` fonksiyonunda bulunan `page-contact.php` istisna kontrolü temizlenecek.

---

### 2. Breadcrumb ve Başlık Gizleme Butonlarının Elementor Entegrasyonu
- **Gereksinim:** `_gnn_hide_title` (Başlığı Gizle) ve `_gnn_hide_breadcrumb` (Breadcrumb Gizle) meta box tercihleri Elementor ile oluşturulan sayfalarda da eksiksiz çalışmalıdır.
- **Kapsam:**
  - `gnn/inc/page-meta.php` ve `gnn/template-parts/content-page.php` incelenerek:
    - Elementor'un kendi sayfa ayarlarındaki `hide_title` seçeneği ile temadaki `gnn_hide_title()` fonksiyonu senkronize edilecek (`'1' === get_post_meta($post_id, '_gnn_hide_title', true) || 'yes' === get_post_meta($post_id, '_elementor_page_settings', true)['hide_title']`).
    - `gnn_hide_breadcrumb()` kontrolünün Elementor sayfa şablonlarında da breadcrumb alanını sorunsuz gizlediği garanti altına alınacak.

---

### 3. Öne Çıkarılan Görsel (Featured Image) & Sidebar Mimarisi
- **Gereksinim (Kritik Düzenleme):**
  - Sayfa veya yazıda öne çıkarılan görsel (Featured Image) varsa, görsel **Sidebar ve Sayfa İçeriğinin ÜSTÜNDE** (en tepede, Header'ın hemen 0px altında) yer alacaktır.
  - Görsel hiçbir şekilde sidebar'ın sağında veya solunda (sadece sol/sağ içerik sütununa sıkışmış halde) render edilmeyecektir.
  - Yüksekliği **maksimum 250px** (`max-height: 250px; object-fit: cover; width: 100%;`) olacaktır.
  - **Şablon Genişliği Uyumu:**
    - **Full Width (Tam Genişlik) Şablon:** Görsel ekranın en solundan en sağına %100 kenardan kenara (edge-to-edge / full width) uzanacaktır.
    - **Boxed Şablon:** Görsel max 1200px genişliğinde ortalanmış konteyner sınırlarında olacaktır.
    - **Sidebar'lı Şablonlar (Right / Left Sidebar):** Öne çıkarılan görsel, 2 sütunlu (İçerik + Sidebar) yapının en üstünde tam genişlikte uzanacak; altındaki içerik alanı ve sidebar ise kendi 2 sütunlu düzeninde başlayacaktır.
  - **Sidebar Dikey Hizalaması:**
    - Sidebar'ın dikey başlangıç noktası (üst kenarı), ana sayfa içeriğinin (content) başlangıç noktası ile **birebir aynı hizada (paralel)** olacaktır.

---

### 4. Dinamik İçerik Üst Boşluğu (Content Top Padding) Panel Ayarı
- **Gereksinim:** GNN Tema Yönetim Paneli -> `Pages Layout` sekmesine **`content_top_padding`** adında yeni bir sayısal ayar eklenecektir.
- **Teknik Detaylar:**
  - **Ayar Adı:** `content_top_padding`
  - **Varsayılan Değer:** `50` (px)
  - **Kapsam / Aralık:** `0` ile `200` (px) arası (`absint()` ile doğrulanacak).
  - **CSS Değişkeni:** `--gnn-content-top-pad: 50px;` (Theme panel/options tarafında head'e inline stil olarak eklenir).
- **Çalışma Mantığı & Boşluk Sıralaması:**
  - **Öne çıkarılan görsel YOKSA:** Bu boşluk Header ile sayfa başlığı/breadcrumb/içerik (ve Sidebar) arasında uygulanır.
  - **Öne çıkarılan görsel VARSA:** Görsel Header'a 0px yapışır; panelden girilen bu boşluk **Öne Çıkarılan Görselin hemen altında** (görsel ile içerik/sidebar alanı arasında) uygulanır.

---

### 5. Dinamik İçerik ve Footer Arası Alt Boşluk (Content Bottom Spacing) Panel Ayarı
- **Gereksinim:** Sayfa içeriği (`.site-main` / `.entry-content` / Sidebar) ile Footer bölgesi (`.site-footer` / `.site-footer__widgets`) arasında kaç px boşluk olacağı GNN Tema Yönetim Panelinden ayarlanabilmelidir.
- **Teknik Detaylar:**
  - **Ayar Adı:** `content_bottom_padding`
  - **Konum:** GNN Tema Yönetim Paneli -> `Pages Layout` sekmesi.
  - **Varsayılan Değer:** `64` (px)
  - **Kapsam / Aralık:** `0` ile `300` (px) arası (`absint()` ile doğrulanacak).
  - **CSS Değişkeni:** `--gnn-content-bottom-pad: 64px;`
- **Çalışma Mantığı:**
  - Sayfa içeriğinin bittiği nokta ile Footer başlangıcı/`site-footer__widgets` arasındaki mesafe bu değere bağlanır.

---

### 6. Google Material Symbols & Icons (fonts.google.com/icons) Entegrasyonu
- **Gereksinim:** [Google Fonts Icons / Material Symbols](https://fonts.google.com/icons) kütüphanesinin temaya entegre edilmesi ve içeriklerde/menülerde kullanılabilmesi.
- **Teknik Detaylar:**
  - **GNN Tema Yönetim Paneli -> Icons Sekmesi:**
    - **Google Material Icons Aktif Etme:** `google_material_icons` (Açık/Kapalı toggle, varsayılan: Aktif).
    - **Stil Seçimi:** `material_icons_style` (`outlined`, `rounded`, `sharp`, `filled` whitelisting).
  - **Front-end Yükleme:**
    - `functions.php` / `options.php` içerisinden Google Fonts CDN üzerinden `https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined...` bağlantısı `esc_url()` ve `preconnect` desteği ile performanslı şekilde yüklenecek.
  - **Kullanım Kolaylığı & CSS:**
    - `<span class="material-symbols-outlined">search</span>` veya `<i class="material-symbols-outlined">home</i>` etiketlerinin temadaki tüm içerik, buton ve menülerde tam boyutsal ve dikey uyum (`vertical-align: middle; display: inline-flex;`) içerisinde çalışması sağlanacak.

---

## 🛠️ Değiştirilecek Dosyalar Listesi

1. **[DELETE] `gnn/page-templates/page-contact.php`**
2. **[MODIFY] `gnn/inc/page-layouts.php`**
3. **[MODIFY] `gnn/inc/template-tags.php`**
4. **[MODIFY] `gnn/inc/options.php`** (Google Material Icons CDN enqueue & stil fonksiyonu)
5. **[MODIFY] `gnn/inc/admin-panel.php`** (`Pages Layout` ve `Icons` sekmelerindeki yeni ayarlar)
6. **[MODIFY] `gnn/inc/page-meta.php`**
7. **[MODIFY] `gnn/template-parts/content-page.php`** & **`gnn/single.php`**
8. **[MODIFY] `gnn/assets/css/main.css`** (Material Symbols ikon uyum CSS kuralları)

---

## 🔒 Audit Notes & Security Hardening (Sentinel Plan Audit)

Plan, projenin güvenlik ve mimari sözleşmelerine (`.specs/boundary-conditions.md` ve `.specs/constitution.md`) göre incelenmiş ve şu güvenlik kontrolleri plana sıkı bir şekilde eklenmiştir:

1. **Numeric Range Sanitization:** `content_top_padding` (0-200px) ve `content_bottom_padding` (0-300px) ayarları `gnn_options_sanitize()` içinde `absint()` ve `min/max` aralık filtresinden geçirilecektir.
2. **Whitelist Sanitization:** `material_icons_style` değeri strictly whitelisted array (`['outlined', 'rounded', 'sharp', 'filled']`) ile doğrulanacaktır.
3. **URL Escaping:** Google Fonts CDN linki `wp_enqueue_style()` çağrısında `esc_url()` ile sarmalanacak, `wp_head` üzerinde `preconnect` etiketleri güvenli basılacaktır.
4. **Elementor Meta Escaping:** `get_post_meta()` değerleri doğrudan okunurken boolean dönüşümüne tabi tutulacak, XSS riski engellenecektir.

---

## 🎯 Kabul Kriterleri & Test Adımları

- [ ] `page-templates/page-contact.php` silinmiş ve admin şablon listesinden kaldırılmış olmalı.
- [ ] Elementor ile düzenlenen sayfalarda başlık gizle ve breadcrumb gizle tercihleri canlı sayfada doğru çalışmalı.
- [ ] Öne çıkarılan görsel header'a 0px yapışık başlamalı, max height 250px olmalı ve Full width / Boxed şablonlarına göre %100 veya 1200px genişlik almalı.
- [ ] Sidebar seçili sayfalarda öne çıkarılan görsel sidebar ve içeriğin en üstünde yer almalı; sidebar dikey olarak içerik ile aynı hizada başlamalı.
- [ ] GNN Panel -> Pages Layout sekmesinden `content_top_padding` ayarı (varsayılan 50px) değiştirildiğinde, öne çıkarılan görsel yoksa header altında, varsa görselin altında tam girilen px kadar boşluk oluşmalı.
- [ ] GNN Panel -> Pages Layout sekmesinden `content_bottom_padding` ayarı değiştirildiğinde, sayfa içeriği ile footer arasındaki alt boşluk tam girilen px kadar güncellenmeli.
- [ ] GNN Panel -> Icons sekmesinden Google Material Symbols/Icons aktif edildiğinde kütüphane sorunsuz yüklenmeli ve ikon sınıfları hizada render edilmeli.
- [ ] `python .scripts/build-zip.py` çalıştırılarak `.build/gnn.zip` paketi hatasız derlenmeli.
