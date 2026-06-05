=== Kargo Etiketi ===
Contributors:      ahmetyuruk
Tags:              woocommerce, kargo, etiketi, shipping label, cargo, print, tracking
Requires at least: 5.8
Tested up to:      6.7
Requires PHP:      7.4
Stable tag:        1.1.0
License:           GPLv2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
Donate link:       https://wpwix.com

WooCommerce siparişleri için tek tıkla profesyonel kargo etiketi oluşturun, yazdırın ve sipariş takibini kolaylaştırın.

== Description ==

**Kargo Etiketi**, WooCommerce mağazanızdaki siparişler için anında profesyonel kargo etiketi oluşturmanızı ve sipariş yönetimini kolaylaştırmanızı sağlayan ücretsiz bir eklentidir.

Topluluk için geliştirilmiştir. Ücretsiz, reklamsız, açık kaynak.

= Temel Özellikler =

**Yazdırma**
* Sipariş listesinde her satırda 🖨️ yazdır butonu
* Sipariş detayında "Kargo Etiketi" meta kutusu
* Toplu yazdırma: birden fazla siparişi aynı anda yazdır
* A6 / A5 / Termal (100×150 mm) boyut seçeneği

**Etiket İçeriği**
* Gönderici bilgileri (firma adı, adres, telefon)
* Teslimat adresi — eksikse fatura adresi otomatik kullanılır
* Ödeme tipi: Siparişten otomatik çek veya sabit metin
* Sipariş ürünleri ve adetleri (göster/gizle)
* Sipariş notu (göster/gizle)
* Kargo takip numarası (göster/gizle)
* Firma logosu: WordPress Medya Kütüphanesi'nden seç, konumlandır

**Sipariş Entegrasyonu**
* **Yazdırıldı takibi:** Sipariş listesinde hangi etiketlerin yazdırıldığı, tarihi ve kaç kez yazdırıldığı
* **Otomatik durum güncelleme:** Etiket yazdırıldığında siparişi seçtiğiniz duruma otomatik taşı (varsayılan kapalı)
* **Kargo takip numarası:** Sipariş detayında kaydet, etikette göster

**Teknik**
* HPOS (Custom Order Tables) tam destekli
* WooCommerce 6.0+ uyumlu
* Türkçe dil desteği

= Kullanım =

1. **WooCommerce → Kargo Etiketi** menüsünden gönderici bilgilerini ve etiket ayarlarını yapın.
2. Sipariş listesinde 🖨️ ikonuna tıklayın veya sipariş detayındaki "Kargo Etiketi Yazdır" butonunu kullanın.
3. Açılan sayfada **Ctrl+P** ile yazdırın (A6 kağıt seçin).

= Geliştirici =

**Ahmet YÜRÜK** | [wpwix.com](https://wpwix.com) | [GitHub](https://github.com/ahmetyuruk/kargo-etiketi)

Bu eklenti topluluk için geliştirilmiş, ücretsiz ve açık kaynaklıdır.

== Installation ==

1. `kargo-etiketi` klasörünü `/wp-content/plugins/` dizinine yükleyin, veya
2. WordPress yönetici panelinde **Eklentiler → Yeni Ekle → ZIP Yükle** ile yükleyin.
3. **Eklentiler** menüsünden etkinleştirin.
4. **WooCommerce → Kargo Etiketi** menüsünden ayarları yapın.

== Frequently Asked Questions ==

= WooCommerce olmadan çalışır mı? =
Hayır. Bu eklenti WooCommerce'e bağımlıdır.

= Hangi kargo firmalarını destekler? =
Kargo firmasından bağımsız evrensel bir etiket üretir. Aras Kargo, Yurtiçi, MNG, PTT vb. herhangi bir firmada kullanabilirsiniz.

= Otomatik durum güncelleme nasıl çalışır? =
WooCommerce → Kargo Etiketi → Sipariş Entegrasyonu bölümünden etkinleştirip hedef durumu seçin. Her etiket yazdırma işleminde sipariş otomatik güncellenir.

= Kargo takip numarasını müşteriye gönderiyor mu? =
Takip numarası şu an sipariş notuna kaydedilir. İleri sürümlerde müşteri e-postasına otomatik ekleme planlanmaktadır.

= HPOS (Custom Order Tables) uyumlu mu? =
Evet, tam uyumludur.

= Barkod / QR kod desteği var mı? =
Gelecek sürümlerde planlanmaktadır.

== Screenshots ==

1. Sipariş listesi: Kargo Etiketi sütunu ve yazdırıldı/bekliyor badge'leri.
2. Sipariş detayı: Meta kutusu (durum göstergesi, yazdır butonu, takip no).
3. Ayarlar sayfası: Gönderici bilgileri ve canlı etiket önizlemesi.
4. Örnek baskı çıktısı (A6).

== Changelog ==

= 1.1.0 =
* YENİ: Sipariş listesinde "Kargo Etiketi" sütunu (yazdır butonu + yazdırıldı/bekliyor badge).
* YENİ: Etiket yazdırıldı takibi — tarih, saat ve kaç kez yazdırıldığı kaydedilir.
* YENİ: Otomatik sipariş durumu güncelleme (parametrik, varsayılan kapalı).
* YENİ: Kargo takip numarası girişi ve etiket üzerinde gösterimi.
* YENİ: Toplu yazdırma iyileştirmeleri.
* DÜZELTME: Print CSS düzeltmesi — ürünler bölümü A6'da kesiliyordu.
* Geliştirici bilgileri güncellendi: Ahmet YÜRÜK / wpwix.com.

= 1.0.0 =
* İlk yayın.
* Sipariş listesi ve detay sayfası entegrasyonu.
* Toplu yazdırma desteği.
* HPOS uyumluluğu.
* Gönderici bilgileri, logo, ürünler, sipariş notu, etiket boyutu ayarları.
* Canlı etiket önizlemesi.

== Upgrade Notice ==

= 1.1.0 =
Etiket yazdırıldı takibi, otomatik sipariş durumu ve takip numarası özellikleri eklendi.
