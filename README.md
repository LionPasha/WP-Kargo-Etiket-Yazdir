# WP Kargo Etiketi – WooCommerce Eklentisi

[![Version](https://img.shields.io/badge/version-1.1.0-blue)](https://github.com/LionPasha/WP-Kargo-Etiket-Yazdir)
[![License: GPL v2](https://img.shields.io/badge/License-GPLv2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0)
[![WooCommerce](https://img.shields.io/badge/WooCommerce-6.0%2B-96588a)](https://woocommerce.com/)
[![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-21759b)](https://wordpress.org/)

WooCommerce siparişleri için tek tıkla profesyonel kargo etiketi oluşturun, yazdırın ve sipariş takibini kolaylaştırın.

> **Ücretsiz · Açık Kaynak · Topluluk için**

---

## Özellikler

### Yazdırma
| Özellik | Detay |
|---|---|
| Sipariş listesi butonu | Her satırda yazdır ikonu |
| **Kargo Etiketi sütunu** | Yazdır butonu + Yazdırıldı/Bekliyor badge |
| Sipariş detayı meta kutusu | Büyük "Yazdır" butonu + durum göstergesi |
| Toplu yazdırma | Seçili siparişleri tek seferde yazdır |
| Etiket boyutu | A6 / A5 / Termal 100x150mm |

### Etiket İçeriği
| Özellik | Detay |
|---|---|
| Gönderici bilgileri | Firma adı, adres, ilçe/il, telefon |
| Alıcı bilgileri | WooCommerce teslimat/fatura adresinden otomatik |
| Ödeme tipi | Siparişten otomatik (COD = Alıcı Ödemeli) veya sabit metin |
| Sipariş ürünleri | Ürün adı + adet (fiyatsız, göster/gizle) |
| Sipariş notu | Müşteri notu (göster/gizle) |
| Kargo takip no | Etikette göster (göster/gizle) |
| Firma logosu | Medya kütüphanesinden seç, sol/orta/sağ konumla |

### Sipariş Entegrasyonu
| Özellik | Detay |
|---|---|
| **Yazdırıldı takibi** | Tarih, saat, kaç kez yazdırıldığı — sipariş listesinde badge |
| **Oto durum güncelleme** | Parametrik, varsayılan **kapalı** — hedef durumu sen seçersin |
| **Kargo takip numarası** | Sipariş detayında giriş alanı, sipariş notuna da kaydedilir |

---

## Kurulum

### ZIP ile (önerilen)
1. [Releases](https://github.com/LionPasha/WP-Kargo-Etiket-Yazdir/releases) sayfasından `kargo-etiketi.zip` indir.
2. **WordPress Admin → Eklentiler → Yeni Ekle → ZIP Yükle**
3. Etkinleştir.

### Manuel
```bash
git clone https://github.com/LionPasha/WP-Kargo-Etiket-Yazdir.git
cp -r kargo-etiketi /var/www/html/wp-content/plugins/
```

---

## Kullanım

### 1. Ayarları Yapılandır
**WooCommerce → Kargo Etiketi** menüsünden:
- **Gönderici Bilgileri**: firma adı, adres, telefon
- **Etiket Ayarları**: ödeme tipi modu, ürünler, not, boyut
- **Sipariş Entegrasyonu**: oto-durum, takip no
- **Firma Logosu**: yükle, konumlandır

### 2. Etiket Yazdır
- Sipariş listesindeki yazdır butonuna tıkla
- Veya sipariş detayındaki "Kargo Etiketi Yazdır" butonunu kullan
- Yeni sekmede **Ctrl+P** → A6 kağıt seç

### 3. Toplu Yazdır
Sipariş listesinde siparişleri işaretle → **Toplu İşlemler → Kargo Etiketi Yazdır**

---

## Ekran Görüntüleri

| Sipariş Listesi | Sipariş Detayı | Ayarlar |
|---|---|---|
| Sütun + badge | Meta kutusu | Canlı önizleme |

---

## Geliştirici

**Ahmet YÜRÜK**
- Web: [wpwix.com](https://wpwix.com)
- GitHub: [@LionPasha](https://github.com/LionPasha)

Bu eklenti topluluk yararına geliştirilmiştir. Ücretsiz, reklamsız, açık kaynak.

---

## Katkı

Pull request'ler memnuniyetle karşılanır.

1. Fork'la
2. Branch oluştur: `git checkout -b feature/ozellik-adi`
3. Commit'le: `git commit -m 'feat: yeni özellik'`
4. Push'la: `git push origin feature/ozellik-adi`
5. Pull Request aç

---

## Yol Haritası

- [ ] Barkod / QR kod desteği
- [ ] Müşteriye otomatik "kargoya verildi" e-postası
- [ ] Kargo firması API entegrasyonu (Aras, Yurtiçi, MNG, PTT)
- [ ] CSV dışa aktarım

---

## Lisans

[GPL v2 veya üstü](https://www.gnu.org/licenses/gpl-2.0.html)

---

## Changelog

### v1.1.0
- Sipariş listesinde "Kargo Etiketi" sütunu (yazdır + badge)
- Etiket yazdırıldı takibi (tarih/saat/sayaç)
- Otomatik sipariş durumu güncelleme (parametrik)
- Kargo takip numarası girişi ve etiket üzerinde gösterim
- Print CSS düzeltmesi (ürünler bölümü A6'da kesiliyordu)

### v1.0.0
- İlk yayın
