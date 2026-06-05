# Kargo Etiketi â€“ WooCommerce Eklentisi

[![Version](https://img.shields.io/badge/version-1.1.0-blue)](https://github.com/LionPasha/WP-Kargo-Etiket-Yazdir)
[![License: GPL v2](https://img.shields.io/badge/License-GPLv2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0)
[![WooCommerce](https://img.shields.io/badge/WooCommerce-6.0%2B-96588a)](https://woocommerce.com/)
[![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-21759b)](https://wordpress.org/)

WooCommerce sipariÅŸleri iÃ§in tek tÄ±kla profesyonel kargo etiketi oluÅŸturun, yazdÄ±rÄ±n ve sipariÅŸ takibini kolaylaÅŸtÄ±rÄ±n.

> **Ãœcretsiz Â· AÃ§Ä±k Kaynak Â· Topluluk iÃ§in**

---

## Ã–zellikler

### YazdÄ±rma
| Ã–zellik | Detay |
|---|---|
| SipariÅŸ listesi butonu | Her satÄ±rda ğŸ–¨ï¸ yazdÄ±r ikonu |
| **Kargo Etiketi sÃ¼tunu** | YazdÄ±r butonu + YazdÄ±rÄ±ldÄ±/Bekliyor badge |
| SipariÅŸ detayÄ± meta kutusu | BÃ¼yÃ¼k "YazdÄ±r" butonu + durum gÃ¶stergesi |
| Toplu yazdÄ±rma | SeÃ§ili sipariÅŸleri tek seferde yazdÄ±r |
| Etiket boyutu | A6 / A5 / Termal 100Ã—150mm |

### Etiket Ä°Ã§eriÄŸi
| Ã–zellik | Detay |
|---|---|
| GÃ¶nderici bilgileri | Firma adÄ±, adres, ilÃ§e/il, telefon |
| AlÄ±cÄ± bilgileri | WooCommerce teslimat/fatura adresinden otomatik |
| Ã–deme tipi | SipariÅŸten otomatik (COD = AlÄ±cÄ± Ã–demeli) veya sabit metin |
| SipariÅŸ Ã¼rÃ¼nleri | ÃœrÃ¼n adÄ± + adet (fiyatsÄ±z, gÃ¶ster/gizle) |
| SipariÅŸ notu | MÃ¼ÅŸteri notu (gÃ¶ster/gizle) |
| Kargo takip no | Etikette gÃ¶ster (gÃ¶ster/gizle) |
| Firma logosu | Medya kÃ¼tÃ¼phanesinden seÃ§, sol/orta/saÄŸ konumla |

### SipariÅŸ Entegrasyonu
| Ã–zellik | Detay |
|---|---|
| **YazdÄ±rÄ±ldÄ± takibi** | Tarih, saat, kaÃ§ kez yazdÄ±rÄ±ldÄ±ÄŸÄ± â€” sipariÅŸ listesinde badge |
| **Oto durum gÃ¼ncelleme** | Parametrik, varsayÄ±lan **kapalÄ±** â€” hedef durumu sen seÃ§ersin |
| **Kargo takip numarasÄ±** | SipariÅŸ detayÄ±nda giriÅŸ alanÄ±, sipariÅŸ notuna da kaydedilir |

---

## Kurulum

### ZIP ile (Ã¶nerilen)
1. [Releases](https://github.com/LionPasha/WP-Kargo-Etiket-Yazdir/releases) sayfasÄ±ndan `kargo-etiketi.zip` indir.
2. **WordPress Admin â†’ Eklentiler â†’ Yeni Ekle â†’ ZIP YÃ¼kle**.
3. EtkinleÅŸtir.

### Manuel
```bash
# Repoyu klon'la
git clone https://github.com/LionPasha/WP-Kargo-Etiket-Yazdir.git

# WordPress plugins klasÃ¶rÃ¼ne kopyala
cp -r kargo-etiketi /var/www/html/wp-content/plugins/
```

---

## KullanÄ±m

### 1. AyarlarÄ± YapÄ±landÄ±r
**WooCommerce â†’ Kargo Etiketi** menÃ¼sÃ¼nden:
- **GÃ¶nderici Bilgileri**: firma adÄ±, adres, telefon
- **Etiket AyarlarÄ±**: Ã¶deme tipi modu, Ã¼rÃ¼nler, not, boyut
- **SipariÅŸ Entegrasyonu**: oto-durum, takip no
- **Firma Logosu**: yÃ¼kle, konumlandÄ±r

### 2. Etiket YazdÄ±r
- SipariÅŸ listesindeki ğŸ–¨ï¸ butonuna tÄ±kla
- Veya sipariÅŸ detayÄ±ndaki "Kargo Etiketi YazdÄ±r" butonunu kullan
- Yeni sekmede etiketi gÃ¶rdÃ¼kten sonra **Ctrl+P** â†’ A6 kaÄŸÄ±t seÃ§

### 3. Toplu YazdÄ±r
SipariÅŸ listesinde sipariÅŸleri iÅŸaretle â†’ **Toplu Ä°ÅŸlemler â†’ Kargo Etiketi YazdÄ±r**

---

## Ekran GÃ¶rÃ¼ntÃ¼leri

| SipariÅŸ Listesi | SipariÅŸ DetayÄ± | Ayarlar |
|---|---|---|
| SÃ¼tun + badge | Meta kutusu | CanlÄ± Ã¶nizleme |

---

## GeliÅŸtirici

**Ahmet YÃœRÃœK**
- Web: [wpwix.com](https://wpwix.com)
- GitHub: [@LionPasha](https://github.com/LionPasha)

Bu eklenti topluluk yararÄ±na geliÅŸtirilmiÅŸtir. Ãœcretsiz, reklamsÄ±z, aÃ§Ä±k kaynak.

---

## KatkÄ±

Pull request'ler memnuniyetle karÅŸÄ±lanÄ±r.

1. Fork'la
2. Branch oluÅŸtur: `git checkout -b feature/ozellik-adi`
3. Commit'le: `git commit -m 'feat: yeni Ã¶zellik'`
4. Push'la: `git push origin feature/ozellik-adi`
5. Pull Request aÃ§

---

## Yol HaritasÄ±

- [ ] Barkod / QR kod desteÄŸi
- [ ] MÃ¼ÅŸteriye otomatik "kargoya verildi" e-postasÄ±
- [ ] Kargo firmasÄ± API entegrasyonu (Aras, YurtiÃ§i, MNG, PTT)
- [ ] CSV dÄ±ÅŸa aktarÄ±m
- [ ] Kargo takip eklentisi entegrasyonu

---

## Lisans

[GPL v2 veya Ã¼stÃ¼](https://www.gnu.org/licenses/gpl-2.0.html)

---

## Changelog

### v1.1.0
- SipariÅŸ listesinde "Kargo Etiketi" sÃ¼tunu (yazdÄ±r + badge)
- Etiket yazdÄ±rÄ±ldÄ± takibi (tarih/saat/sayaÃ§)
- Otomatik sipariÅŸ durumu gÃ¼ncelleme (parametrik)
- Kargo takip numarasÄ± giriÅŸi ve etiket Ã¼zerinde gÃ¶sterim
- Print CSS dÃ¼zeltmesi (Ã¼rÃ¼nler bÃ¶lÃ¼mÃ¼ A6'da kesilmiyordu)

### v1.0.0
- Ä°lk yayÄ±n

