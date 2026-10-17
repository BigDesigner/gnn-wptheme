**GNN WordPress Theme — Audit raporu, 18 Ekim 2026**

**Remediation scope update:** The user authorized fixes for A01–A10. Track current
implementation and verification in `.tasks/audit-remediation-plan.md` and
`.memory-bank/audits/`. The findings below describe the original 1.6.0 snapshot,
not the current remediation status. WordPress.org publishing and its old ZIP are
excluded by explicit user instruction; the old WP.org artifact is not a release blocker.

İncelenen sürüm: **1.6.0**. Commit: `0ac1688e937f07fe2ff637a8daf9e37bc510ca98`, dal: `main`.

**Karar: Yeni özelliklerden önce hata düzeltme turu öneriyorum.** İncelemede 2 yüksek, 6 orta ve 2 düşük öncelikli bulgu belirlendi. En önemli sorunlar, varsayılan ana sayfada yazıların gösterilmemesi ve sürüm değişiminde Elementor şablonlarının kalıcı olarak yeniden oluşturulması. Bu öncelikler işlev ve veri bütünlüğü içindir; güvenlik açığı şiddet puanı değildir.

**Kapsam ve yöntem**

Klasik PHP tema mimarisi, şablon yönlendirmesi, panel ayarları, nonce/yetki kontrolleri, çıktı kaçışları, GitHub güncelleyicisi, Elementor/WooCommerce entegrasyonları, JavaScript etkileşimleri, paketleme ve release iş akışı incelendi. Gerçek tema fonksiyonları bellek içi WordPress test karşılıklarıyla çalıştırıldı. Paketler geçici proje kopyasında üretildi; mevcut `.build` çıktıları değiştirilmedi.

Bu çalışma canlı WordPress uçtan uca testi değildir. İzole testler fonksiyonların belirli girdilere tepkisini gösterir; gerçek veritabanı, WordPress hook sıralaması ve eklenti sürümleriyle entegrasyonun tamamını doğrulamaz. Kullanıcının sitesine bağlanılmadı ve hiçbir içerik silinmedi.

**Çalıştırılan kontroller**

| Kontrol | Sonuç | Sınır |
|---|---|---|
| PHP sözdizimi | 60/60 dosya başarılı | PHP 8.5.8; ilan edilen minimum PHP 7.4 ayrıca çalıştırılmadı |
| JavaScript sözdizimi | 5/5 dosya başarılı | Node.js 24.14.0; tarayıcı davranış testi değildir |
| Paketlenmiş JavaScript | Her iki pakette 5/5 başarılı | Minify sonrası sözdizimi |
| JSON | `theme.json`, demo ayarları ayrıştırıldı | WordPress şema uyumluluğu testi değildir |
| Demo XML | Ayrıştırıldı, kök `rss` | Gerçek WXR içe aktarımı yapılmadı |
| Türkçe MO | GNU gettext tarafından okundu; 406 katalog girdisi | Çeviri kapsamı ve dil kalitesi puanlanmadı |
| Normal paket | 85 dosya, 256.565 bayt; CRC temiz | Geçici kopyada üretildi |
| WP.org paketi | 83 dosya, 247.133 bayt; CRC temiz; demo dışarıda | WP.org Theme Check/onay testi değildir |
| Sürüm tutarlılığı | CSS, PHP sabiti, readme, POT/PO ve mimari kaydı 1.6.0 | Mevcut eski ZIP için aşağıdaki teslimat notuna bakınız |
| İşlev probları | 4 süreç başarıyla tamamlandı; aşağıdaki hataları gözlemledi | Çıkış kodu 0, temanın hatasız olduğu anlamına gelmez |
| PHPCS / WPCS | Çalıştırılmadı; PHPCS PATH üzerinde bulunamadı | Kod standardı uyumu onaylanmadı |
| Bağımlılık audit'i | Kök `package.json` ve `composer.json` yok | `npm audit` / `composer audit` uygulanabilir değil; eklenti CVE taraması yapılmadı |
| Lighthouse / axe / gerçek tarayıcı | Çalıştırılmadı | Çalışır WordPress sunucusu doğrulanamadı; 9731 portunda dinleyici bulunmadı |

**Bulgular**

| ID | Öncelik | Bulgu | Kanıt türü |
|---|---|---|---|
| A01 | P1 — yüksek | “Son yazılar” ana sayfası yazıları göstermiyor | Gerçek şablon + izole çalıştırma + WP şablon hiyerarşisi |
| A02 | P1 — yüksek | Tema güncellemesi üretilmiş Elementor şablonlarını kalıcı siliyor | Gerçek fonksiyon + bellek içi silme/ekleme kaydı |
| A03 | P2 — orta | GitHub güncelleme entegrasyonu cron bağlamında yüklenmiyor | Yönetim/yönetim dışı bootstrap karşılaştırması |
| A04 | P2 — orta | Kurulum sonrası callback hata sonucunu korumuyor | Hata ve başarısız taşıma probları + WP hook sözleşmesi |
| A05 | P2 — orta | GitHub kaynak ZIP yedeği kurulabilir tema yapısında değil | Git arşiv yapısı + kod incelemesi |
| A06 | P2 — orta | WooCommerce dosya optimizasyonu geçerli ürün içeriklerini kaçırıyor | Kısa kod/blok senaryolarında dequeue kaydı |
| A07 | P2 — orta | Slider klavye odağında ilerliyor; görünmeyen içerik odaklanabilir | Gerçek JS + DOM/timer test karşılıkları + HTML/CSS incelemesi |
| A08 | P2 — orta | Release etiketi ile paket iç sürümü ayrışabiliyor | Workflow veri akışı incelemesi |
| A09 | P3 — düşük | “Tüm ayarları sıfırla” işlemi ortak tema ayarlarını bırakıyor | Depolama ve reset akışı incelemesi |
| A10 | P3 — düşük | CSS minifier seçicinin anlamını değiştiriyor | Gerçek minifier fonksiyonunun çalıştırılması |

**A01 — “Son yazılar” ana sayfasında içerik kaybı**

Konum: [front-page.php:22](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/front-page.php:22).

`show_on_front=posts` olduğunda şablon yalnızca slider parçasını çağırıyor; yazı döngüsüne girmiyor. Testte `loop_checks=0` gözlendi. Slider da boşsa ana içerik bölümü boş kalır. Dosyadaki yorum, yazıların otomatik olarak `home.php` tarafından gösterileceğini varsayıyor; oysa mevcut `front-page.php`, son yazılar ayarında da önceliklidir. [WordPress şablon hiyerarşisi](https://developer.wordpress.org/themes/classic-themes/basics/template-hierarchy/).

Öneri: `posts` durumunda blog şablonuna açıkça yönlendirmek veya aynı yazı listesini burada çizmek. Kabul testi: yeni kurulumda son yazılar, sayfalama, boş blog ve statik ana sayfa ayrı ayrı doğrulanmalı.

**A02 — Elementor şablonları güncellemede siliniyor**

Konum: [elementor-templates.php:1242](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/elementor-templates.php:1242), [sürüm tetikleyicisi:1288](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/elementor-templates.php:1288).

`gnn_elementor_templates_installed` ile `GNN_VERSION` farklı olduğunda `admin_init` üzerinde yeniden kurulum başlıyor. `_gnn_elementor_template` işaretli tüm mevcut kayıtlar `wp_delete_post(..., true)` ile kalıcı siliniyor. Kullanıcı bu kaydedilmiş şablonları düzenlediyse değişiklikler kaybolur; eski ID ile şablon çağıran yerler boşa düşebilir. Daha önce sayfa içine kopyalanmış bağımsız Elementor içeriğinin silindiği iddia edilmiyor.

Test: mevcut ID 42 kalıcı silme çağrısına girdi; 100–114 arası 15 yeni ID üretildi. Bu işlemler yalnızca bellek içi test kayıtlarıydı.

Öneri: şablon kütüphanesi sürümünü tema sürümünden ayırmak; ID koruyarak güncellemek; kullanıcı değiştirmişse içeriği korumak veya yeni bir kopya oluşturmak. Kabul testi: düzenlenmiş şablon ve ID ile gömülmüş kullanım, yama sürümü güncellemesinden sonra korunmalı.

**A03 — Zamanlanmış güncelleme keşfi eksik**

Konum: [functions.php:258](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/functions.php:258).

Updater yalnızca `is_admin()` dalında yükleniyor. Yönetim dışı bootstrap testinde sınıf ve `pre_set_site_transient_update_themes` filtresi yok; yönetim bağlamında ikisi de var. WP-Cron yönetim ekranı değildir; GitHub sorgusunu yapan filtre bu başlangıç akışında kaydolmaz. Bu nedenle güncelleme keşfi yönetim paneli ziyaretlerine bağımlı kalır. Daha önce önbelleğe alınmış paketin hiçbir koşulda otomatik kurulamayacağı gibi daha geniş bir iddia yapılmıyor. [WordPress is_admin davranışı](https://developer.wordpress.org/reference/functions/is_admin/).

Öneri: panel kodunu yönetim bağlamında tutup updater kayıtlarını cron dahil uygun bağlamlarda yüklemek. Kabul testi: yönetim oturumu açılmadan yeni GitHub sürümünün zamanlanmış kontrolle bulunması ve otomatik güncelleme akışı.

**A04 — Upgrader hata sözleşmesi bozuluyor**

Konum: [class-gnn-github-updater.php:226](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/class-gnn-github-updater.php:226).

`after_install()` GNN dışındaki kurulumlarda bile gelen `$response` yerine `$result` döndürüyor. Önceki bir filtre `WP_Error` üretmişse hata kayboluyor. Testte `unrelated_upgrade_error_preserved=false`. GNN dalında dosya taşıma `false` döndüğünde de sonuç sanki başarılıymış gibi yeni hedefe ayarlanıyor. Üstelik bu hook'un dönüşü WordPress'in kendi `$this->result` dizisini değiştirmez; taşıma sonucunu bu biçimde geri vermek core sonucunu güncellemez. [WP_Upgrader::install_package kaynak kodu](https://developer.wordpress.org/reference/classes/wp_upgrader/install_package/).

Öneri: gelen hata/yanıtı korumak, GNN dışı işlemlere dokunmamak ve kaynak dizin düzenlemesini uygun kurulum öncesi aşamada yapmak. Taşıma hatası açıkça `WP_Error` olmalı.

Ek gözlem: güncel sürüm dalı eski `response['gnn']` kaydını temizlemiyor; önceden dolu transient ile testte hem `response` hem `no_update` kaldı. Updater düzeltmesinde iki durum karşılıklı dışlayıcı yapılmalı.

**A05 — Kaynak ZIP fallback'i tema paketi değil**

Konum: [class-gnn-github-updater.php:127](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/class-gnn-github-updater.php:127).

Release içinde ZIP asset yoksa GitHub `zipball_url` kullanılıyor. Repo kökünde tema dosyaları yerine `gnn/` ve proje dizinleri var. Yerel Git kaynak arşivi kontrolünde kök `style.css` yok, `gnn/style.css` var. GitHub'ın dış sarmalayıcı klasörü açıldıktan sonra da bu fazladan `gnn/` katmanı kalır. `after_install()` yalnızca klasörü yeniden adlandırır, içteki tema klasörünü yukarı taşımaz. Paket kontrolü devredeyse kurulum reddedilir; diğer güncelleme yollarında yanlış dizin yapısı riski vardır. Canlı güncelleme çalıştırılmadı. [WordPress tema paketi kontrolü](https://developer.wordpress.org/reference/classes/theme_upgrader/check_package/).

Öneri: doğrulanmış tema asset'i zorunlu olsun; yoksa güncelleme sunulmasın. Alternatif olarak içteki tema klasörü kurulumdan önce güvenli biçimde seçilsin. Kabul testi: asset bulunan ve bulunmayan release'ler.

**A06 — WooCommerce içerik tanıma eksik**

Konum: [options.php:564](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/options.php:564).

Varsayılan açık `woo_scope`, sadece `products` kısa kodunu, `add-to-cart` metnini ve üç blok türünü tanıyor. `[product_category]`, `[add_to_cart]` ve `woocommerce/product-collection` örneklerinde gerekli WooCommerce stil/script handle'ları kaldırıldı; `[products]` kontrol senaryosunda kaldırılmadı. Özellikle `add_to_cart` kısa kodundaki alt çizgi, aranan `add-to-cart` metniyle eşleşmez. Bunlar desteklenen WooCommerce içerik türleridir. [WooCommerce kısa kodları](https://woocommerce.com/document/woocommerce-shortcodes/).

Sonuç: bu içeriklerin bulunduğu normal sayfalarda görünüm ve AJAX davranışı bozulabilir. Bazı eklenti sürümleri dosyaları daha sonra tekrar yükleyebilir; gerçek etkinin kapsamı entegrasyon testi gerektirir.

Öneri: desteklenen kısa kodları ve iç içe WooCommerce bloklarını kapsayan algılama; belirsiz Elementor/widget içeriklerinde dosyaları koruyan yaklaşım. Kabul testi: kısa kodlar, ürün koleksiyonu, widget ve normal blog sayfası.

**A07 — Slider klavye ve erişilebilirlik eksikleri**

Konum: [slider.js:28](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/assets/js/slider.js:28), [hero-slider.php:22](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/template-parts/hero-slider.php:22).

Slider yalnızca yatay transform ve aktif sınıfını değiştiriyor. Ekran dışındaki slaytların bağlantıları odak sırasından çıkarılmıyor. Otomatik ilerleme `mouseenter` ile duruyor fakat klavye odağında durmuyor; kullanıcıya durdur/devam ettir kontrolü sunulmuyor. Testte odak sonrasında timer durdurma sayısı 0 ve sonraki tick ile `translateX(-100%)` gözlendi. `tab` rollü noktalarda karşılık gelen panel ilişkileri ve sekme klavye davranışı da tamamlanmamış. [W3C carousel rehberi](https://www.w3.org/WAI/ARIA/apg/patterns/carousel/).

Öneri: odakta duraklama, açık oynat/durdur düğmesi, aktif olmayan içerikte uygun `inert`/odak yönetimi ve tutarlı ARIA modeli. Kabul testi: yalnızca klavye, ekran okuyucu ve azaltılmış hareket tercihi. Gerçek ekran okuyucu testi henüz yapılmadı.

**A08 — Release sürümü ile kurulan sürüm ayrışabilir**

Konum: [release.yml:29](C:/Users/bigde/.antigravity/gnn-wptheme/.github/workflows/release.yml:29).

`version_override` sadece etiketi ve ZIP dosya adını değiştiriyor; paket içindeki `style.css`/`GNN_VERSION` değişmiyor. Örneğin mevcut kaynakla 1.6.1 override'ı, 1.6.0 içeriğini v1.6.1 diye yayımlar. Updater etiketi karşılaştırdığı için kurulumdan sonra da aynı güncellemeyi sunabilir. Bu workflow çalıştırılmadı; davranış veri akışından doğrulandı.

Öneri: sürümün tek kaynağı tema başlığı olsun veya override ile tüm sürüm alanlarının eşleşmesi zorunlu tutulsun; SemVer doğrulaması eklensin. Ek olarak girdiler doğrudan shell koduna yerleştiriliyor; `env` ile aktarılmalı. Bu, yetkili workflow girdisine ilişkin sağlamlaştırma bulgusudur; dış kullanıcı tarafından istismar doğrulanmadı.

**A09 — Sıfırlama vaadi ile uygulama farklı**

Konum: [admin-panel.php:1089](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/admin-panel.php:1089), [reset handler:1233](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/inc/admin-panel.php:1233).

Arayüz tüm GNN ayarlarını varsayılana döndürmeyi vaat ediyor, handler yalnızca `gnn_options` değerini siliyor. Vurgu rengi, varsayılan koyu/açık mod ve footer yasal metni `theme_mods` içinde kaldığından sıfırlanmıyor. Bağımsız updater tercihi de kalıyor.

Öneri: sıfırlama kapsamını açıkça belirlemek; tema markalama ayarlarını da sıfırlamak veya arayüz metnini gerçek kapsama daraltmak. Siteye ait genel ayarların gelişigüzel silinmesi önerilmiyor.

**A10 — Regex tabanlı CSS minify seçici anlamını değiştiriyor**

Konum: [build-zip.py:84](C:/Users/bigde/.antigravity/gnn-wptheme/.scripts/build-zip.py:84), [editor.css:56](C:/Users/bigde/.antigravity/gnn-wptheme/gnn/assets/css/editor.css:56).

Gerçek dönüştürücü `.editor-styles-wrapper ::selection` ifadesini `.editor-styles-wrapper::selection` haline getiriyor. Aradaki boşluk bir descendant combinator'dür; salt biçimlendirme değildir. Kaynak ve dağıtılan seçiciler artık aynı şeyi ifade etmiyor. Mevcut görsel etkisinin büyüklüğü tarayıcıda ölçülmedi.

Öneri: CSS ayrıştırıcısı kullanan minifier veya seçicilerin anlamlı boşluklarını koruyan sınırlı sıkıştırma. Kabul testi: kaynak/paket seçici eşdeğerliği ve editör seçili metin görünümü.

**Mimari değerlendirme ve olumlu bulgular**

Tema işlevleri `inc/` altında ayrılmış; ortak sayfa renderer'ı ve ayar varsayılanları tekrarları azaltıyor. Yönetim paneli WordPress Settings API kullanıyor, üçüncü parti panel framework'ü yok. İncelenen yönetim işlemlerinde nonce ve yetki kontrolleri, metabox kaydında yetki denetimi, özel script alanlarında `unfiltered_html` sınırı mevcut. İlk slider görselinin yüksek öncelikle yüklenmesi ve azaltılmış hareket tercihinin autoplay'i kapatması olumlu. Bunlar tam güvenlik veya erişilebilirlik sertifikasyonu değildir.

**Teslimat ve bakım notları**

- Mevcut [gnn-wporg.zip](C:/Users/bigde/.antigravity/gnn-wptheme/.build/gnn-wporg.zip) **1.0.0** sürümünü taşıyor; kaynak ve mevcut normal ZIP 1.6.0. WP.org paketi bu haliyle güncel teslimat olarak kullanılmamalı. Audit sırasında yenisi yalnızca geçici kopyada üretildi.
- CI yalnızca elle başlatılan release akışına sahip; PR/push üzerinde PHP lint, WPCS, sürüm tutarlılığı ve işlevsel kontrol kapısı bulunmuyor.
- Readme slider yönetimini hâlâ Customizer üzerinden tarif ediyor; uygulama CPT/panel kullanıyor. Handoff ve aktif oturum notları da son kaynak durumunun gerisinde. Sonraki geliştirmede kaynak kod ve bu audit esas alınmalı.
- WP.org paketi sadece demo klasörünü dışlıyor. Bunun Theme Review gerekliliklerini karşıladığı varsayılmamalı; Theme Check ve manuel dağıtım incelemesi ayrı iş olmalı.
- Performans değerlendirmesi dosya yükleme mantığıyla sınırlı. Gerçek LCP, CLS, INP, sorgu sayısı veya Lighthouse puanı ölçülmedi.

**Önerilen geliştirme sırası**

1. A01 ve A02: varsayılan ana sayfa davranışı ile kullanıcı şablon verisini koruma.
2. A03–A05 ve A08: updater ve release zincirini birlikte düzeltme; cron, yanlış paket ve başarısız kurulum senaryoları.
3. A06–A07: WooCommerce içerik çeşitleri ve slider erişilebilirliği.
4. A09–A10: ayar kapsamı ve paket eşdeğerliği; eski paket/readme temizliği.
5. Yayın öncesi gerçek WordPress ortamında PHP/WP destek matrisi, Elementor Free/Pro, WooCommerce, mobil/masaüstü, RTL, klavye ve performans kontrolleri. Bu probları beklenen doğru davranışı assert eden regresyon testlerine dönüştürme; CI kapısı ekleme.

**Tekrar çalıştırma ve kanıtlar**

Çalışma dizini proje kökü olacak şekilde:

```powershell
python .scripts/audit-scan.py
```

Gerekenler: PATH üzerinde PHP, Node.js, Python ve Git. Betik dış ağ kullanmaz; WordPress'e bağlanmaz. `.temp/gnn-audit-*` altında kopya oluşturup build çalıştırır; yalnızca kendi geçici kopyasını temizler. Çıktısını audit klasörüne yazar. Problar mevcut hatalı davranışları kaydeder; tek başına “tüm işlevler doğru” kalite kapısı değildir.

[Ham başlangıç kanıtları](C:/Users/bigde/.antigravity/gnn-wptheme/.tasks/audits/2026-10-18/evidence.json) · [Güncel statik/paket kontrolleri](C:/Users/bigde/.antigravity/gnn-wptheme/.scripts/verify.py) · [Gerçek WordPress/tarayıcı testleri](C:/Users/bigde/.antigravity/gnn-wptheme/.scripts/test-wordpress.mjs).

The original observation-only WordPress/DOM doubles were retired during remediation.
Their output remains in the baseline evidence JSON. `audit-scan.py` now delegates to
current verification and does not overwrite that baseline evidence. Its old temporary
WP.org build behavior is intentionally retired.

Bu turda tema kodu, sürümü ve mevcut dağıtım paketleri değiştirilmedi. Yalnızca audit araçları, kanıt dosyası ve bu rapor eklendi. Commit, push veya release yapılmadı.
