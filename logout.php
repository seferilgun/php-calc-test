İşte PHP'de basit, güvenli ve yorumlanmış bir çıkış yapma sayfası (logout.php) örneği:

```php
<?php
/**
 * logout.php
 *
 * Kullanıcının oturumunu sonlandırır ve giriş sayfasına yönlendirir.
 */

// Oturumu başlat (eğer zaten başlamadıysa)
session_start();

// Oturum değişkenlerini temizle
$_SESSION = array();

// Oturum çerezini sil (eğer varsa)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Oturumu yok et
session_destroy();

// Giriş sayfasına yönlendir
header("Location: login.php");
exit;
?>
```

**Açıklamalar:**

* **`<?php`**: PHP bloğunun başlangıcı.
* **`/** ... */`**: Kodun ne yaptığını açıklayan yorum bloğu.
* **`session_start();`**: Oturumu başlatır. Eğer oturum zaten başlatılmışsa bir şey yapmaz.  Bu fonksiyon *mutlaka* herhangi bir çıktı göndermeden önce çağrılmalıdır (HTML, boşluk, vs.).
* **`$_SESSION = array();`**:  `$_SESSION` dizisini boşaltır, yani tüm oturum değişkenlerini siler.
* **`if (ini_get("session.use_cookies")) { ... }`**:  Oturum için çerezler kullanılıyorsa, bu blok çerezi siler.
    * **`ini_get("session.use_cookies")`**: PHP yapılandırma ayarını kontrol eder (session.use_cookies). Eğer `1` (etkin) ise, çerezler kullanılıyordur.
    * **`session_get_cookie_params()`**: Oturum çereziyle ilgili parametreleri (path, domain, secure, httponly) alır.
    * **`setcookie(session_name(), '', time() - 42000, ...);`**:  Oturum çerezini geçersiz kılar. `time() - 42000` ile çerezin süresini geçmişe ayarlayarak tarayıcıdan silinmesini sağlar.
* **`session_destroy();`**: Sunucudaki oturum verilerini yok eder. Bu, oturum kimliğini geçersiz kılar.
* **`header("Location: login.php");`**: Kullanıcıyı `login.php` sayfasına yönlendirir.  `header()` fonksiyonu *mutlaka* herhangi bir çıktı göndermeden önce çağrılmalıdır.
* **`exit;`**: Komut dosyasının yürütülmesini durdurur.  `header()` ile yönlendirmeden sonra `exit;` kullanmak, kodun gereksiz yere çalışmasını engeller ve yönlendirmenin doğru şekilde gerçekleşmesini sağlar.
* **`?>`**: PHP bloğunun sonu.

**Güvenlik Önerileri:**

* **HTTPS Kullanımı:** Web sitenizin HTTPS kullandığından emin olun. Bu, oturum kimliği ve diğer hassas verilerin şifrelenerek iletilmesini sağlar.
* **Oturum Sabitleme Saldırılarına Karşı Koruma:**  Kullanıcının oturum açması veya yetki düzeyinin değişmesi durumunda oturum kimliğini yenileyin (regenerate). Bunu `session_regenerate_id(true);` kullanarak yapabilirsiniz.  Bunu login.php'de başarılı bir girişten sonra yapmanız önerilir.
* **Çerez Güvenliği:** `session.cookie_secure` ve `session.cookie_httponly` yapılandırma ayarlarının etkin olduğundan emin olun. Bunlar, çerezlerin yalnızca HTTPS üzerinden iletilmesini ve JavaScript tarafından erişilememesini sağlar, böylece XSS saldırılarına karşı koruma sağlanır.  Bunları `php.ini` dosyanızda veya `.htaccess` dosyanızda ayarlayabilirsiniz.
* **Oturum Süre Aşımı:** Oturumların belirli bir süre sonra otomatik olarak sona ermesini sağlayın. Bu, yetkisiz erişimi azaltır. Bunu `session.gc_maxlifetime` yapılandırma ayarı ile veya oturuma bir zaman damgası ekleyip periyodik olarak kontrol ederek yapabilirsiniz.
* **Giriş Sayfasına Erişim Kısıtlaması:**  Çıkış yaptıktan sonra kullanıcı giriş sayfasına yönlendirilir.  Giriş sayfasında, kullanıcı zaten oturum açmışsa (yani `$_SESSION` değişkenleri tanımlıysa) başka bir sayfaya (örneğin ana sayfaya) yönlendirilmesi gerekir.

**Nasıl Kullanılır:**

1. Bu kodu `logout.php` adında bir dosyaya kaydedin.
2. `login.php` dosyanızın web sitenizin kök dizininde olduğundan emin olun veya `header("Location: login.php");` satırındaki yolu uygun şekilde güncelleyin.
3. Çıkış yapma bağlantısını (örneğin `<a href="logout.php">Çıkış Yap</a>`) web sitenizin uygun sayfalarına ekleyin.

Bu kod basit, güvenli ve anlaşılırdır.  Umarım bu yardımcı olur!
