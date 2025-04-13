Tamamdır, işte istediğiniz özelliklere sahip, iyi yorumlanmış ve güvenli bir `config.php` dosyası örneği:

```php
<?php

/**
 * Veritabanı bağlantı ayarları.
 *
 * Bu dosya, uygulamanızın veritabanına bağlanmak için gerekli olan
 * bilgileri içerir. PDO (PHP Data Objects) kullanarak güvenli
 * ve esnek bir bağlantı sağlar.
 */

// Veritabanı bağlantı bilgileri
define('DB_HOST', 'localhost');      // Veritabanı sunucu adresi
define('DB_NAME', 'calculator_db');   // Veritabanı adı
define('DB_USER', 'root');           // Veritabanı kullanıcı adı
define('DB_PASS', '');           // Veritabanı şifresi
define('DB_CHARSET', 'utf8mb4');   // Karakter seti (önerilen)

// Hata ayıklama modu (geliştirme ortamında true, üretimde false olmalı)
define('DEBUG_MODE', true);

/**
 * PDO bağlantısı oluşturma fonksiyonu.
 *
 * @return PDO|null  Başarılıysa PDO nesnesi, hata durumunda null döner.
 */
function connectDB(): ?PDO
{
    // Veritabanı bağlantısı için DSN (Data Source Name) oluştur
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

    // PDO ayarları (güvenlik ve performans için)
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Hata durumunda istisna fırlat
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,    // Varsayılan getirme modu: ilişkisel dizi
        PDO::ATTR_EMULATE_PREPARES   => false,               // Gerçek hazırlıklı ifadeler kullan (güvenlik)
    ];

    try {
        // PDO bağlantısını oluştur
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo; // Bağlantı başarılı
    } catch (PDOException $e) {
        // Bağlantı hatası durumunda yapılacak işlemler
        error_log("Veritabanı bağlantı hatası: " . $e->getMessage()); // Hatayı günlük dosyasına kaydet (önerilen)

        if (DEBUG_MODE) {
            // Geliştirme ortamında hatayı ekrana yazdır (UYARI: Üretimde KAPALI OLMALI)
            echo "Veritabanı bağlantısı başarısız: " . $e->getMessage();
        } else {
            // Üretim ortamında kullanıcıya genel bir hata mesajı göster
            echo "Veritabanı bağlantısında bir sorun oluştu. Lütfen daha sonra tekrar deneyiniz.";
        }

        return null; // Bağlantı başarısız
    }
}

// Örnek kullanım (isteğe bağlı):
// $db = connectDB();
// if ($db) {
//     echo "Veritabanına başarıyla bağlanıldı!";
//     // ... Veritabanı işlemlerini burada yapabilirsiniz ...
//     $db = null; // Bağlantıyı kapat (isteğe bağlı, PHP otomatik olarak kapatır)
// } else {
//     // Hata zaten işlendi, burada ek bir işlem yapmaya gerek yoksa boş bırakılabilir.
// }

?>
```

**Açıklamalar:**

*   **`define()` Kullanımı:** Sabit değerleri tanımlamak için `define()` kullanılmıştır. Bu, değerlerin yanlışlıkla değiştirilmesini önler.
*   **PDO (PHP Data Objects):** MySQL bağlantısı için PDO kullanılmıştır. PDO, farklı veritabanı sistemleriyle çalışmayı kolaylaştıran ve güvenlik özelliklerine sahip bir arayüzdür.
*   **Hata Yönetimi:**
    *   `try...catch` blokları ile hatalar yakalanır ve işlenir.
    *   `error_log()` fonksiyonu ile hatalar bir günlük dosyasına kaydedilir (önerilen). Bu, hataları daha sonra incelemenizi sağlar.
    *   `DEBUG_MODE` sabiti ile geliştirme ve üretim ortamları için farklı hata mesajları gösterilebilir. **ÖNEMLİ:** Üretim ortamında `DEBUG_MODE` her zaman `false` olmalıdır, aksi takdirde hassas bilgiler açığa çıkabilir.
*   **Güvenlik:**
    *   `PDO::ATTR_EMULATE_PREPARES => false` ayarı ile gerçek hazırlıklı ifadeler (prepared statements) kullanılır. Bu, SQL injection saldırılarına karşı önemli bir savunmadır.
    *   Veritabanı şifresi doğrudan kodda saklanmamalıdır. Mümkünse ortam değişkenleri veya daha güvenli bir konfigürasyon yöntemi kullanılmalıdır.
*   **Karakter Seti:** `utf8mb4` karakter seti kullanılmıştır. Bu, daha geniş bir karakter yelpazesini (örneğin, emoji'leri) destekler.
*   **Yorumlar:** Kodun her bölümü detaylı bir şekilde yorumlanmıştır.
*   **Fonksiyon:** Veritabanı bağlantısı bir fonksiyon içine alınmıştır (`connectDB()`). Bu, kodun daha düzenli ve tekrar kullanılabilir olmasını sağlar.
*   **Bağlantıyı Kapatma:** PDO bağlantısı, `null` atanarak kapatılabilir. PHP, script sonunda bağlantıları otomatik olarak kapatır, ancak açıkça kapatmak iyi bir uygulamadır.
*   **Örnek Kullanım:** Dosyanın nasıl kullanılacağına dair bir örnek eklenmiştir.

**Nasıl Kullanılır:**

1.  Bu kodu `config.php` adında bir dosyaya kaydedin.
2.  `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` sabitlerini kendi veritabanı bilgilerinizle güncelleyin.
3.  `DEBUG_MODE` sabitini geliştirme ortamındaysanız `true`, üretim ortamındaysanız `false` olarak ayarlayın.
4.  Veritabanına bağlanmak istediğiniz PHP dosyasında `config.php` dosyasını dahil edin (`require_once 'config.php';`).
5.  `connectDB()` fonksiyonunu çağırarak bir PDO nesnesi elde edin.
6.  PDO nesnesini kullanarak veritabanı işlemlerinizi gerçekleştirin.
7.  İşiniz bittiğinde, PDO nesnesini `null` atayarak bağlantıyı kapatın (isteğe bağlı).

**Ek Güvenlik Önerileri:**

*   Veritabanı şifresini doğrudan kodda saklamaktan kaçının. Ortam değişkenleri, güvenli bir konfigürasyon dosyası veya şifreleme yöntemleri kullanın.
*   Kullanıcıdan alınan verileri her zaman sanitize edin ve validate edin. SQL injection, XSS ve diğer güvenlik açıklarına karşı önlem alın.
*   Veritabanı kullanıcı hesabına sadece gerekli izinleri verin.
*   Uygulamanızı düzenli olarak güncelleyin ve güvenlik açıklarına karşı tarayın.
*   HTTPS kullanarak verilerin şifrelenmesini sağlayın.

Bu dosya, veritabanı bağlantısı için sağlam bir temel oluşturur. İhtiyaçlarınıza göre daha fazla özelleştirebilirsiniz. Başarılar!
