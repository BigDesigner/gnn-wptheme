# Tema takip işleri — 1.6.2

Tarih: 2026-10-24. Kullanıcı WooCommerce dışındaki bütün açık takip konularını
tamamlamayı istedi. Bu kapsam tamamlandı; aktif yol haritasında yalnızca
ertelenmiş WooCommerce site kabulü kaldı. FAZ 1.34.2 kabulü önceki kullanıcı
testiyle kapanmıştı.

## Yapılan düzeltmeler

| Konu | Sonuç |
|---|---|
| Panel güvenliği | Scalar tür kontrolleri, metabox nonce türleri, özel kod yetkisi ve HTTP 403 yanıtları korundu/güçlendirildi. |
| JSON aktarımı | 1 MiB/depth sınırı, tam belge doğrulaması, tanınan alanlar ve güvenli marka/mod değerleri; geçersiz dosyada yazma yok. |
| Kısmi dosyalar | Dosyada olmayan anahtarlar korunur; eksik checkbox artık kapanmaz. Satır sonları ve kod/metin karakterleri korunur. |
| Eski logolar | Theme_mod ID/HTTP(S) URL desteği; panelde gösterim ve yeniden kayıt; JSON aktarımı; açık kaldırma tercihi öncelikli. |
| Logo görünürlüğü | Tek logo her iki renk modunda görünür. Silinmiş görsel seçiliyse boş marka yerine site adı gösterilir. |
| Menü rozetleri | Kanca menü ID'sini öğe ID'si sanıyordu; iki argümanla doğru öğenin metasına kayıt yapılır. |
| Erişilebilirlik | Mobil menü Escape ile kapanır ve odak düğmeye döner; görünür klavye odağı ve arama düğmesi odağı sağlandı. |
| İçerik görünürlüğü | JavaScript yokken içerik görünür; yükleme ekranı varsayılan olarak kapalı ve CSS ile süreli; scroll reveal yalnızca çalışan observer ile etkinleşir. |
| RTL | RTL dosyası açıkça yüklenir; fiziksel menü/form/içerik/ofset kuralları tamamlandı. |
| Çeviriler | 408 kaynak metni, Türkçe katalog, printf değişkenleri ve derlenmiş MO eşleşmesi CI'da kontrol edilir. |
| Kod standartları | Kilitli Composer/WPCS araçları, gerçek WordPress standardı ve CI kapısı; kural dosyasında sniff dışlaması yok. |

Sürüm 1.6.2 olarak CSS/PHP/readme/katalog/mimari kayıtlarında eşitlendi.
WordPress.org paketleme kapsam dışı kalır; normal GitHub/özel ZIP hazırlanır.

## Gerçek doğrulama sonuçları

| Kontrol | Sonuç |
|---|---:|
| WordPress 7.1.3 / PHP 8.2.33; WooCommerce ve Elementor yok | **89/89** |
| WordPress 6.0.17 / PHP 7.4.33; WooCommerce ve Elementor yok | **89/89** |
| Her matriste gerçek WordPress/SQLite entegrasyonu | 61/61 |
| Her matriste Chromium/HTTP/klavye kontrolleri | 28/28 |
| PHP/JS/sürüm/JSON/ZIP/CSS/katalog statik kontrolleri | **90/90** |
| PHPCS WordPress standardı | **0 hata, 0 uyarı** |
| axe: koyu/açık masaüstü, mobil, RTL mobil | **0 otomatik ihlal** |
| Türkçe kaynak/katalog/printf kontrolü | **408 metin, 0 hata** |
| Composer geliştirme bağımlılık bildirimi | **0 güvenlik bildirimi** |

Güvenlik/aktarım kontrolleri gerçek admin oturumu, gerçek multipart dosya
yükleme ve WordPress veritabanı üzerinden çalıştı. Yanıt/redirect ve kalıcı
durum birlikte denetlendi. Hatalı dosya, yanlış uzantı, büyük dosya, eksik nonce,
abone erişimi ve `unfiltered_html` sınırı doğrulandı. WP fonksiyon stub'ı yoktur.

Başlangıçtaki 1.6.1 testi 38/38 idi. Test geliştirme sırasında eksik yönetim
include'u, nonce fixture REQUEST verisi, İngilizce dil paketi isteği, yerelleşmiş
buton metni ve geçiş/konum örnekleme sorunları giderildi. İlk başarısız çıktılar
saklandı; doğrulamalar gevşetilmedi. Yeni matrise ait gerçek Arapça/Türkçe
WordPress dil dosyaları kullanıldı.

## Ölçülen yerel performans

Chromium, SQLite, Gutenberg statik sayfa ve tek metin slaytlı fixture; isteğe
bağlı Google yazı tipleri/ikonları ve eklentiler kapalı. Her matriste üç gezinme:

| Ölçüm | Güncel matris | Alt sınır matrisi |
|---|---:|---:|
| Ortanca LCP | 1.444 sn | 1.280 sn |
| En yüksek CLS | 0.000000 | 0.000283 |
| Tema JavaScript | 10.53 KiB | Aynı varlıklar |
| Tema CSS | 58.09 KiB | Aynı varlıklar |
| Yüklenen yerel WOFF2 | 60.79 KiB | Aynı varlıklar |

Tema JS bütçesi 32 KiB, CSS bütçesi 96 KiB ve ölçülen CLS sınırı 0.1 altında.
Bunlar yerel laboratuvar sonuçlarıdır; üretim LCP/CLS/INP veya Lighthouse puanı
olarak sunulmaz. INP ölçülmedi. Yazı tipi/ofset ve seçenek okuma için gereksiz
tekrarlı birleştirme azaltıldı; üçüncü taraf ağ maliyetleri bu tablodan çıkarılamaz.

## Kanıtlar ve sınırlar

- [Güncel matris](verification-1.6.2/current-no-woo/results-minimum.json).
- [Alt sınır matrisi](verification-1.6.2/minimum-no-woo/results-minimum.json).
- [Güncel ölçümler](verification-1.6.2/current-no-woo/hardening-measurements.json).
- [WPCS çıktısı](verification-1.6.2/wpcs.json).
- [Statik/paket sonucu](verification-1.6.2/static-package.json).
- [Teslim özeti](verification-1.6.2/delivery-summary.json).
- [Güvenlik raporu](audit-9e72354.md) ve [test raporu](testreport-9e72354.md).

axe bazı katmanlı zemin/metin/ikon kontrastlarını `incomplete` olarak bırakır;
hedefler ölçüm JSON'unda saklandı. Bu otomatik sonuç tam WCAG uygunluk belgesi
veya manuel ekran okuyucu testi değildir. Sonraki kullanıcı renkleri/içerikleri,
üretim CDN/önbelleği, MySQL/MariaDB ve Elementor Pro bu testin sonucu değildir.
PHP satır/dal kapsamı ölçülmedi; WASM ortamında kapsam uzantısı yoktur.
Yerel Node 24.14.0 ile çalıştırıldı; paket motor eşiği nedeniyle CI/geliştirme
talimatı Node 24.18.0+ olarak kalır. Yeni CI tanımı bu yerel çalışmadan sonra
GitHub üzerinde henüz yürütülmüş kabul edilmez.

## Paket

`.build/gnn.zip`: **86 dosya, 272324 bayt**.

SHA-256: `d8b1e4657928dab5d0fb96a322ab6020aee565bb108f6f0a3585db0534ad0bf2`.

Çalışma ağacı 1.6.2 olarak hazırdır; bu rapor üretim dağıtımı veya yayımlanmış
GitHub 1.6.2 sürümü bildirimi değildir. İstenen geliştirme ve yerel doğrulama
kapsamı tamamlandı. WooCommerce kabulü kullanıcı kararıyla en sonda kalır.
