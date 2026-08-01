# GNN Theme — Gelişmiş Özellikler Gereksinim ve Spesifikasyon Dokümanı (PRD)

> **Amaç:** Bu doküman, GNN WordPress temasına eklenecek yeni dinamik özellikler ve özelleştirme taleplerinin **AI Agent (Claude Code) tarafından en net ve doğru şekilde anlaşılabilmesi için** hazırlanmış teknik gereksinim spesifikasyonudur.
>
> 🔄 **Proje Geçmişi ve Devir Notu (Claude Code İçin Önemli Bağlam):**
> - Claude Code kullanıcısının mesaj limiti dolduğunda devredilen **Gutenberg Block Patterns fazı Antigravity AI tarafından eksiksiz tamamlanmıştır**.
> - Tüm pattern'ler modüler PHP dosyalarına ayrıştırılmış (`gnn/inc/pattern-parts/*.php`), `gnn/inc/patterns.php` dosyasına kaydedilmiş, harici görseller yerel SVG (`assets/img/placeholder-cover.svg`) ile değiştirilmiş ve kullanıcının isteği üzerine ek olarak **"Media & text (image right)"** (`media-text-right.php`) pattern'i de eklenmiştir. Paket `python .scripts/build-zip.py` ile başarıyla derlenmiştir.
> - Ardından kullanıcı ve Antigravity AI birlikte çalışarak 19 maddelik bu kapsamlı gereksinim planını (`.task/FEATURE-PLAN.md`) hazırlamış ve proje dizinini düzenlemiştir.
>
> 📁 **Proje Dizin Yapısı Bilgilendirmesi (Claude Code İçin):**
> Proje dizini geliştirme kolaylığı ve temiz Git yapısı için yeniden organize edilmiştir:
> - **Tema Kaynak Kodları:** `gnn/` klasöründedir (`functions.php`, `style.css`, `inc/`, `assets/`, `theme.json` vb. tüm aktif tema kodları buradadır).
> - **Derleme Betikleri:** `.scripts/` klasöründedir. Paketleme yapmak için `python .scripts/build-zip.py` komutu çalıştırılır; bu betik `gnn/` klasörünü derleyip `.build/gnn.zip` paketini üretir.
> - **Zip Çıktıları:** `.build/` klasöründedir.
> - **Geçici/İlk Taslak Kılavuzları:** `.temp/` klasöründedir.
> - **PRD Spesifikasyon Dokümanı:** `.task/FEATURE-PLAN.md` (Bu dosya).
>
> **İş Akışı Prensibi:** Bu doküman neyin yapılması gerektiğini (Gereksinimler & Kabul Kriterleri) ve temanın mevcut durumunu açıklar. Kodu nasıl yazacağına, hangi fonksiyonel mimariyi kullanacağına ve dosya düzenlemelerine AI Agent (Claude Code) kendi otonom karar verir.
>
> 🎛️ **Tam Panel Kontrolü ve Yönetilebilirlik Garantisi (KRİTİK UYARI):**
> Dokümanda tanımlanan **tüm yeni özellikler, efektler ve davranışlar (mümkün olan tüm fonksiyonlar) GNN Tema Yönetim Paneli üzerinden açılıp kapatılabilir (toggle/enable/disable) ve özelleştirilebilir olmalıdır**. Örneğin, Çapa Bağlantıları için Yumuşak Kaydırma (Smooth Scroll) varsayılan olarak açık gelebilir; ancak kullanıcı isterse GNN Panelinden tek tıkla bunu kapatabilmelidir. Bu yönetim kuralı istisnasız tüm maddeler için geçerlidir.
>
> 🛡️ **Mevcut Özellikleri Koruma Garantisi (Geriye Dönük Uyumluluk):**
> AI Agent bu spesifikasyonları uygularken temada önceden var olan **`logo_light`**, **`logo_light_2x`** (Retina 2x Açık Mod Logosu), **`logo_dark`**, **`logo_dark_2x`** (Retina 2x Koyu Mod Logosu) seçeneklerini, **`gnn_the_logo()`** ve **`gnn_logo_img()`** fonksiyonlarını, accent rengi, `show_toggle`, `remember_mode` ve diğer mevcut çalışan altyapıyı **KESİNLİKLE SİLMEYECEK VEYA BOZMAYACAKTIR**. Tüm yeni özellikler mevcut yapının üzerine geriye dönük uyumlu olarak eklenecektir.

---

## 📋 Kullanıcı Talepleri & Teknik Gereksinimler

---

### 1. Header Logo Maksimum Yükseklik Kontrolleri (Desktop & Mobile)
- **Mevcut Durum:** Temada Retina 1x/2x Light ve Dark mode logoları aktif çalışmaktadır; ancak logonun maksimum yüksekliği için tek bir sabit değer mevcuttur.
- **Kullanıcı Talebi & Gereksinim:**
  - Masaüstü (Desktop) ve Mobil cihazlar için 2 ayrı logo maksimum yükseklik ayarı sunulmalıdır.
  - Masaüstü logosu ayrı yükseklikte, mobil logosu ayrı yükseklikte ayarlanabilmeli ve ekran boyutuna göre otomatik uyum sağlamalıdır.
  - Existing 1x/2x Light/Dark mode logo yükleme altyapısı eksiksiz korunmalıdır.

---

### 2. Header ve Footer Menü Hizalaması (Left / Right Alignment)
- **Mevcut Durum:** Header ve Footer menüleri kendi alanlarında varsayılan düzende hizalanmaktadır.
- **Kullanıcı Talebi & Gereksinim:**
  - Header ve Footer menüleri için bağımsız sola/sağa yaslama (align left / align right) seçenekleri olmalıdır.
  - Bu ayar logo konumundan tamamen bağımsız çalışmalı; menü öğelerinin sadece kendi konteyner alanı içerisindeki esnek hizalamasını kontrol etmelidir.

---

### 3. Ortalı Header Düzeni ve Bölünmüş Menü (Split Header Menu)
- **Mevcut Durum:** Temada standart header düzeni (Logo solda, menü sağda) ve tek bir birincil menü konumu bulunmaktadır.
- **Kullanıcı Talebi & Gereksinim:**
  - Header için "Ortalanmış Düzen" (Centered Layout) seçeneği eklenmelidir.
  - Ortalı düzen seçildiğinde logo tam ortada yer almalı; logonun solunda (Header Left Menu) ve sağında (Header Right Menu) çıkacak iki ayrı menü alanı WordPress Menüler ekranından yönetilebilmelidir.
  - Standart düzen seçeneği de mevcudiyetini korumalıdır.

---

### 4. Düzeltilebilir & Dinamik Footer Marka / Logo Alanı
- **Mevcut Durum:** Footer sol altındaki marka alanında site ismi ve varsayılan tanım basılmaktadır; özel metin logosu, logo görseli veya boyut ayarı bulunmamaktadır.
- **Kullanıcı Talebi & Gereksinim:**
  - Footer marka alanı yönetim panelinden tamamen özelleştirilebilir olmalıdır.
  - Marka türü olarak Metin Logo, Görsel Logo, İkisi Birlikte veya Gizle seçenekleri sunulmalıdır.
  - Footer'a eklenen görsel veya metin logo için genişlik/yükseklik boyut kontrolleri ve slogan (tagline) düzenleme imkanı sağlanmalıdır.

---

### 5. Header Arama Butonu (Metinden İkona Dönüşüm)
- **Mevcut Durum:** Header alanındaki arama tetikleyicisi "Search" / "Ara" şeklinde metin olarak görüntülenmektedir.
- **Kullanıcı Talebi & Gereksinim:**
  - Arama düğmesindeki metin kaldırılmalı, yerine dilden ve çeviriden bağımsız evrensel bir mercek (magnifying glass) ikonu yerleştirilmelidir.
  - Arama butonunun gösterilip gösterilmemesi paneldeki `show_search` ayarıyla yönetilmeye devam etmelidir.

---

### 6. Tam Dinamik Temalandırma (Hardcoded Kod Temizliği)
- **Mevcut Durum:** Temadaki bazı şablonlarda sabit (hardcoded) metinler veya panelle ezilemeyen yapılar bulunmaktadır.
- **Kullanıcı Talebi & Gereksinim:**
  - Temada hiçbir hardcoded içerik veya marka bilgisi kalmamalıdır.
  - Temanın her noktası, her metni ve ayarı yönetim panelinden veya WordPress standart ayarlarından düzenlenebilir ve kapatılabilir hale getirilmelidir.

---

### 7. Bloklar ve Görseller İçin Sayfa İçi Kaydırma Animasyonları (Scrolling Effects)
- **Mevcut Durum:** Temada sayfa aşağı kaydırıldıkça çalışan scroll giriş animasyonu altyapısı bulunmamaktadır.
- **Kullanıcı Talebi & Gereksinim:**
  - Sayfa içerisindeki **her bir görsele veya her bir bloğa ayrı ayrı** giriş animasyonu (Fade, Slide Left/Right, Scale vb.) eklenebilmelidir.
  - **Yönlendirme & Tercih:** Bu işlem Elementor Free ile daha kolay ve esnek yapılıyorsa (Elementor Free zaten widget/kolon bazlı dahili "Entrance Animations" özelliğine sahiptir) süreç Elementor Free ile veya hafif CSS utility sınıfları üzerinden çözülmelidir.
  - **Panel Kontrolü:** Tema genelindeki scroll animasyonu altyapısı GNN Panel üzerinden tek tıkla kapatılabilmelidir.

---

### 8. Custom Post Type (CPT) Tabanlı Gelişmiş Hero Slider (`gnn_slide`)
- **Mevcut Durum:** Slider basit bir option dizisi olarak çalışmakta, WordPress Admin'de ayrı bir içerik tipi olarak yönetilememektedir.
- **Kullanıcı Talebi & Gereksinim:**
  - Slider yapısı baştan geliştirilerek her bir slide'ın WordPress Admin panelinde Yazılar/Sayfalar gibi ayrı bir içerik (Custom Post Type) olarak yönetilmesi sağlanmalıdır.
  - Her slide içerisine görsel, başlık, küçük üst başlık (kicker), açıklama, butonlar ve görsel hizalama/sığdırma (`cover`, `contain`, `top`, `bottom`, `centered`) ayarları eklenmelidir.
  - Yönetim panelinden açılıp kapatılabilen **Full Height Slider** seçeneği sunulmalıdır (Sayfa açıldığında header altında ekranı %100 kaplayan ve kaydırıldığında hemen altından içeriğin başladığı slider modu).

---

### 9. Cihaz Büyüklüğüne Göre Gizleme Özelliği (Hide on Device)
- **Mevcut Durum:** WordPress çekirdek editöründe varsayılan olarak responsive cihaz gizleme seçenekleri yoktur.
- **Kullanıcı Talebi & Gereksinim:**
  - Ögelerin/blokların Masaüstü (≥1281px), Laptop (1025-1280px), Tablet (601-1024px) ve Mobil (≤600px) cihazlarda gizlenebilmesi gerekmektedir.
  - **Yönlendirme & Tercih:** Bu işlem Elementor Free (Elementor dahili "Responsive Visibility / Hide on Mobile-Desktop-Tablet" ayarlarına sahiptir) veya basit CSS utility sınıfları (`.hide-on-mobile`, `.hide-on-desktop` vb.) üzerinden daha kolay çözülüyorsa Gutenberg çekirdeğini zorlamak yerine en pratik ve esnek yöntem tercih edilmelidir.

---

### 10. GNN Tema Yönetim Paneli Gruplandırması ve Gelişmiş Ayarlar (Advanced Tools)
- **Mevcut Durum:** Yönetim paneli 6 sekmeden oluşmakta, ayarları sıfırlama veya dışa/içeri aktarma araçları bulunmamaktadır.
- **Kullanıcı Talebi & Gereksinim:**
  - Yönetim paneli sekme yapısı mantıksal olarak şu **9 grupta** yeniden organize edilmelidir (Mevcut logo ve marka ayarları korunarak!):
    1. **Global** (Tema modları, favicon, genel ayarlar, maintenance mode, smooth scroll toggle)
    2. **Header** (Logolar [1x/2x Light/Dark korunarak!], yeni logo yükseklikleri, düzen, menü hizalama, arama/sepet, mobile dock toggle, top bar toggle)
    3. **Footer** (Marka türü, metin/görsel logo, boyutlar, slogan, menü hizalama, telif metinleri)
    4. **Pages Layout** (Sayfa düzenleri, sidebar konumları, 404 sayfası ayarları)
    5. **Colors** (Accent rengi ve renk paletleri)
    6. **Typography** (Font aileleri ve boyutlar)
    7. **Icons** (SVG ikon yönetimi)
    8. **Özel Kod (Custom Code)** (Custom CSS, Custom JS, GA4/GTM)
    9. **Advanced (Gelişmiş)**:
       - **Tema Ayarlarını Sıfırla (Reset Settings)**
       - **Tema Ayarlarını Dışa Aktar / Yedekle (Export JSON)**
       - **Tema Ayarlarını İçeri Aktar (Import JSON)**

---

### 11. Mobil Alt Navigasyon Çubuğu (Mobile Bottom App Dock)
- **Mevcut Durum:** Temada mobil cihazlar için alt sabit navigasyon çubuğu bulunmamaktadır.
- **Kullanıcı Talebi & Gereksinim:**
  - Mobil cihazlarda (≤600px) ekranın alt kısmına sabitlenen, tıpkı bir mobil uygulama gibi çalışan alt navigasyon çubuğu (App Dock) eklenmelidir.
  - İçeriğinde `[Ana Sayfa]`, `[Arama]`, `[Dark/Light Mod]` ve `[Sepet / İletişim]` hızlı erişim butonları yer almalıdır.
  - GNN Tema Paneli -> Header / Mobile sekmesinden tek tıkla açılıp kapatılabilmelidir (Toggle).

---

### 12. Header Üst Bilgi Çubuğu (Top Bar / Announcement Bar)
- **Mevcut Durum:** Temada ana header'ın üzerinde ek bilgi/duyuru alanı bulunmamaktadır.
- **Kullanıcı Talebi & Gereksinim:**
  - Ana header'ın üzerinde konumlanan, isteğe bağlı aktifleştirilebilen **Top Bar** eklenmelidir.
  - E-posta adresi, telefon numarası, duyuru metni ve mini sosyal medya ikonları ekleme seçenekleri sunulmalıdır.
  - GNN Tema Paneli üzerinden anahtarla (toggle) açılıp kapatılabilmeli ve kolayca yönetilebilmelidir.

---

### 13. "Yukarı Çık" (Scroll to Top) Butonu
- **Mevcut Durum:** Temada uzun sayfalarda en üste dönmeyi sağlayan buton altyapısı yoktur.
- **Kullanıcı Talebi & Gereksinim:**
  - Kullanıcı sayfayı aşağı kaydırdığında (örn. 300px geçildiğinde) ekranın sağ alt köşesinde yumuşak bir geçişle beliren "Yukarı Çık" butonu eklenmelidir.
  - Tasarımı temanın minimalist ve modern yapısına tam uyumlu olmalı, GNN Tema Paneli üzerinden açılıp kapatılabilmelidir (Toggle).

---

### 14. Menü Öğelerine ve WooCommerce'e Ortak Rozet Desteği (Unified Badges)
- **Mevcut Durum:** Menü öğelerine özel rozet ekleme imkanı yoktur; WooCommerce indirim/stok rozetleri varsayılan Woo CSS'i ile render edilmektedir.
- **Kullanıcı Talebi & Gereksinim:**
  - `Görünüm → Menüler` ekranından istenilen menü elemanına özel rozetler (`YENİ`, `HOT`, `İNDİRİM` vb.) eklenebilmelidir.
  - **WooCommerce Uyumu & Stil Birliği:** WooCommerce içindeki sabit indirim (`onsale`), stok durumu vb. tüm rozetlerin CSS sınıfları ve görsel tasarımları temanın rozet tasarımıyla (`.gnn-badge`) birebir aynı hale getirilmelidir. Sitedeki tüm rozetler tam görsel uyum içerisinde görünmelidir.

---

### 15. Dahili Bakım / Çok Yakında Modu (Built-in Maintenance Mode)
- **Mevcut Durum:** Temada dahili bakım modu bulunmamaktadır; site güncellenirken harici eklenti ihtiyacı doğmaktadır.
- **Kullanıcı Talebi & Gereksinim:**
  - GNN Tema Paneli -> Global sekmesinden tek tıkla açılıp kapatılabilen (Toggle) **Dahili Bakım Modu (Maintenance / Coming Soon Mode)** eklenmelidir.
  - Aktifleştirildiğinde oturum açmamış ziyaretçilere site logosu ve özelleştirilebilir bakım mesajı gösterilmeli; yöneticiler siteyi normal görmeye devam etmelidir. Extra eklenti ihtiyacı ortadan kaldırılmalıdır.

---

### 16. Özel 404 Sayfası Yönetimi (Custom 404 Page Options)
- **Mevcut Durum:** Temada 404 sayfası (`404.php`) sabit varsayılan metinlerle gelmektedir; panelden başlık veya açıklama değiştirilememektedir.
- **Kullanıcı Talebi & Gereksinim:**
  - GNN Tema Paneli -> Pages Layout sekmesi altına **404 Sayfası Ayarları** eklenmelidir.
  - 404 sayfasının başlığı, açıklama metni, 404 arama çubuğu göster/gizle seçeneği, özel 404 görseli ve "Ana Sayfaya Dön" buton metni panelden özelleştirilebilir ve yönetilebilir hale getirilmelidir.

---

### 17. Sayfa Yüklenme İlerleme Çubuğu ve Yükleme Ekranı (Minimalist Top Preloader & Loading Screen)
- **Mevcut Durum:** Temada sayfa geçişleri esnasında yüklenme durumunu gösteren bir animasyon veya preloader bulunmamaktadır.
- **Kullanıcı Talebi & Gereksinim:**
  - Sayfalar arası geçişlerde veya sayfa yüklenirken ekranın en üstünde beliren minimalist accent renkli **Sayfa Yüklenme İlerleme Çubuğu (Top Preloader Bar)** eklenmelidir.
  - Yavaş internet bağlantıları veya geç yüklenen sayfalar durumunda sayfa ortasında belirip yumuşakça kaybolan hafif bir **Yükleme Efekti / Loading Screen** seçeneği sunulmalı, GNN Panel üzerinden tek tıkla (Toggle) açılıp kapatılabilmelidir.

---

### 18. Çapa Bağlantıları İçin Yumuşak Kaydırma (Smooth Scroll for Anchor Links)
- **Mevcut Durum:** Sayfa içi `#bolum-id` çapa (anchor) bağlantılarına tıklandığında tarayıcı küt diye ilgili bölüme zıplamaktadır.
- **Kullanıcı Talebi & Gereksinim:**
  - Sayfa içi çapa bağlantılarına tıklandığında hedefe akıcı ve yumuşakça kayarak (Smooth Scroll) gidilmesi sağlanmalıdır.
  - **Panel Kontrolü (Toggle):** Yumuşak kaydırma özelliği varsayılan olarak açık gelebilir; fakat GNN Tema Paneli üzerinden (örn. Global sekmesinde) istenildiği zaman tek tıkla tamamen kapatılabilmelidir.

---

### 19. Elementor Builder Uyum ve Entegrasyon Kontrolü
- **Mevcut Durum:** Elementor şablonları ve lokasyon kayıtları temada mevcuttur.
- **Kullanıcı Talebi & Gereksinim:**
  - Elementor Builder ile sayfaların tam genişlikte (Full Width ve Canvas) sorunsuz düzenlenebildiği doğrulanmalı, tema tipografisi ve renklerinin Elementor ile tam uyumu garanti altına alınmalıdır.
  - Madde 7 (Animasyonlar) ve Madde 9'da (Cihaz Gizleme) belirtilen özelliklerin Elementor dahili araçlarıyla sorunsuz çalıştığı teyit edilmelidir.

---

## 🎯 Kabul Kriterleri ve Başarı Kriterleri (AI Agent İçin)

- [ ] Claude Code `gnn/` klasöründeki tema dosyaları ile `.scripts/build-zip.py` derleme betiğinin yeni klasör düzenindeki yerlerini anlar ve doğru çalıştırır.
- [ ] Claude Code yarım kalan Gutenberg Pattern fazının Antigravity AI tarafından başarıyla tamamlandığını (14 pattern parçası) idrak eder ve sıfırdan pattern yazmaya çalışmaz.
- [ ] Tüm yeni eklenen özellikler, animasyonlar, butonlar ve davranışlar GNN Tema Paneli üzerinden açılıp kapatılabilir (toggle/enable/disable) ve yönetilebilir durumdadır.
- [ ] Tüm yeni eklenen özellikler geriye dönük uyumludur; önceden tanımlanmış logo ve mod ayarlarını bozmaz.
- [ ] Temanın hiçbir şablon dosyasında hardcoded metin/marka bilgisi kalmaz.
- [ ] Sitedeki tüm menü ve WooCommerce rozetleri (badges) aynı tema CSS tasarım dilini paylaşır.
- [ ] Bütün yeni ayarlar saf WordPress standartları ve Elementor Free entegrasyonu ile performanslı şekilde uygulanır.
- [ ] İşlem tamamlandıktan sonra `python .scripts/build-zip.py` çalıştırılarak `.build/gnn.zip` paketi hatasız derlenir.
