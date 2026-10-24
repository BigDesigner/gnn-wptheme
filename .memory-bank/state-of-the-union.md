# State of the Union: GNN WordPress Theme

## Tech Stack & Architecture

| Alan | Durum |
|---|---|
| Teknoloji | PHP 7.4+, WordPress 6.0+, klasik tema, yerel Settings API; Elementor/WooCommerce isteğe bağlı |
| Mimari | `gnn_options` + uyumlu theme_mod değerleri; GitHub güncelleyici; Python ZIP; Composer/WPCS ve Playwright QA |
| Sürüm | **1.6.2 yayımlandı**; `main` ve `v1.6.2`: `fda9916` |

## Critical Boundaries

- Nonce/yetki ve bağlama uygun kaçış zorunlu; özel kod değişikliği `unfiltered_html` gerektirir.
- Eski logo ayarlarını ve kullanıcı Elementor düzenlemelerini koru; JSON dosyasını yazmadan önce tamamen doğrula.
- Dağıtım yalnızca GitHub/özel ZIP; WordPress.org kapsam dışı. Sürüm ve paket doğrulaması zorunlu.

## Active Session Context

- **Güncel odak:** Tüm WooCommerce dışı işler ve FAZ 1.34.2 kabulü kapalı; tam hafıza güncellendi.
- **Son işlem:** [CI](https://github.com/BigDesigner/gnn-wptheme/actions/runs/40045204839) 119/119 + 90/90 başarılı; [yayın](https://github.com/BigDesigner/gnn-wptheme/actions/runs/40046157813) başarılı. Yerel eklentisiz iki matris 89/89, WPCS 0/0.
- **Sonraki adım:** WooCommerce kullanıcı sitesinde hazır olduğunda ertelenen görsel/ödeme akışı kabulünü yap; kullanıcı bunu sona bıraktı.
- **Sınır:** GitHub yayını üretime kurulum teyidi değildir. Üretim Web Vitals/INP ve manuel ekran okuyucu testi yapılmadı.
