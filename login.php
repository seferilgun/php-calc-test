Tamamdır, isteğiniz üzerine `login.php` dosyasını ve ilgili dosyaları (stil dosyası ve hesap makinesi sayfası) içeren bir örnek kod hazırladım. Bu kod, PHP kullanarak güvenli bir giriş sayfası oluşturur, PDO ile veritabanı bağlantısı kurar, kullanıcı adı ve şifre kontrolü yapar, oturum yönetimi sağlar ve hata mesajları gösterir. Başarılı bir giriş durumunda kullanıcıyı `calculator.php` sayfasına yönlendirir.

**Dosya Yapısı:**

```
login.php
style.css
calculator.php
config.php (veritabanı bağlantı bilgileri için)
```

**1. `config.php` (Veritabanı Bağlantı Bilgileri):**

Bu dosya, veritabanı bağlantısı için gerekli olan bilgileri içerir. **Güvenlik nedeniyle, bu dosyayı sunucunuzda web erişimi olmayan bir dizinde saklamanız önerilir.**

```php
<?php

define('DB_HOST', 'localhost'); // Veritabanı sunucu adresi
define('DB_NAME', 'kullanicilar'); // Veritabanı adı
define('DB_USER', 'root'); // Veritabanı kullanıcı adı
define('DB_PASS', ''); // Veritabanı şifresi

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
}

?>
```

**Önemli:** Yukarıdaki bilgileri kendi veritabanı bilgilerinizle değiştirmeyi unutmayın. Ayrıca, veritabanınızda `kullanicilar` adında bir tablo oluşturmanız ve bu tabloda `kullanici_adi` ve `sifre` sütunlarının bulunması gerekmektedir.  Şifreleri veritabanında güvenli bir şekilde saklamak için `password_hash()` fonksiyonunu kullanmanız önemlidir.

**Örnek Veritabanı Tablosu Oluşturma (MySQL):**

```sql
CREATE TABLE kullanicilar (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kullanici_adi VARCHAR(50) NOT NULL UNIQUE,
  sifre VARCHAR(255) NOT NULL
);

-- Örnek kullanıcı ekleme (şifreyi hash'leyerek ekleyin)
INSERT INTO kullanicilar (kullanici_adi, sifre) VALUES ('test', '$2y$10$EXAMPLEHASHEDPASSWORD');
```

**2. `login.php` (Giriş Sayfası):**

```php
<?php
session_start(); // Oturum başlat

// Hata mesajlarını saklamak için bir dizi oluştur
$errors = [];

// Veritabanı bağlantısını dahil et
require_once 'config.php';

// Form gönderilmişse
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kullanıcı adı ve şifre al
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Kullanıcı adı boş mu kontrol et
    if (empty($username)) {
        $errors[] = "Kullanıcı adı boş olamaz.";
    }

    // Şifre boş mu kontrol et
    if (empty($password)) {
        $errors[] = "Şifre boş olamaz.";
    }

    // Hata yoksa
    if (empty($errors)) {
        try {
            // Kullanıcıyı veritabanında ara
            $stmt = $db->prepare("SELECT id, kullanici_adi, sifre FROM kullanicilar WHERE kullanici_adi = ?");
            $stmt->execute([$username]);

            // Kullanıcı varsa
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch();
                $hashed_password = $row["sifre"];

                // Şifreyi doğrula
                if (password_verify($password, $hashed_password)) {
                    // Oturum değişkenlerini ayarla
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $row["id"];
                    $_SESSION["username"] = $username;

                    // Kullanıcıyı hesap makinesi sayfasına yönlendir
                    header("location: calculator.php");
                    exit;
                } else {
                    $errors[] = "Yanlış şifre girdiniz.";
                }
            } else {
                $errors[] = "Böyle bir kullanıcı bulunamadı.";
            }
        } catch (PDOException $e) {
            error_log("Veritabanı hatası: " . $e->getMessage()); // Hata günlüğüne kaydet
            $errors[] = "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Sayfası</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Giriş Yap</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Giriş</button>
            </div>
        </form>
    </div>
</body>
</html>
```

**3. `style.css` (Stil Dosyası):**

```css
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.container {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 300px;
}

h2 {
    text-align: center;
    color: #333;
}

.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-control {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box; /* Padding'in genişliği etkilememesi için */
}

.btn {
    background-color: #4CAF50;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
}

.btn:hover {
    background-color: #3e8e41;
}

.alert {
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 4px;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-danger ul {
    margin: 0;
    padding-left: 20px;
}
```

**4. `calculator.php` (Hesap Makinesi Sayfası - Örnek):**

```php
<?php
session_start();

// Kullanıcı giriş yapmamışsa giriş sayfasına yönlendir
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hesap Makinesi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Hesap Makinesi</h2>
        <p>Hoş geldiniz, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</p>
        <p>Bu basit bir hesap makinesi sayfasıdır. (Örnek)</p>
        <a href="logout.php" class="btn btn-primary">Çıkış Yap</a>
    </div>
</body>
</html>
```

**5. `logout.php` (Çıkış Sayfası):**

```php
<?php
session_start();

// Tüm oturum değişkenlerini temizle
$_SESSION = array();

// Oturumu sonlandır
session_destroy();

// Giriş sayfasına yönlendir
header("location: login.php");
exit;
?>
```

**Açıklamalar:**

*   **`session_start()`:** Oturum başlatır. Her sayfanın başında olmalıdır.
*   **Veritabanı Bağlantısı:** `config.php` dosyası, veritabanı bağlantı bilgilerini saklar. PDO kullanarak güvenli bir bağlantı sağlanır.
*   **Form İşleme:** Form gönderildiğinde, kullanıcı adı ve şifre alınır, boş olup olmadıkları kontrol edilir.
*   **Veritabanı Sorgusu:** PDO ile hazırlanmış bir sorgu, kullanıcıyı veritabanında arar.
*   **Şifre Doğrulama:** `password_verify()` fonksiyonu, girilen şifreyi veritabanında saklanan hash'lenmiş şifre ile karşılaştırır.  **ÖNEMLİ: Veritabanına şifreleri kaydederken `password_hash()` fonksiyonunu kullanın!**
*   **Oturum Yönetimi:** Başarılı giriş durumunda, oturum değişkenleri ayarlanır ve kullanıcı `calculator.php` sayfasına yönlendirilir.
*   **Hata Mesajları:** Hata durumunda, hatalar bir diziye eklenir ve kullanıcıya gösterilir.  `htmlspecialchars()` fonksiyonu, XSS saldırılarını önlemek için kullanılır.
*   **Güvenlik:** Form verileri `trim()` fonksiyonu ile temizlenir.  PDO prepared statements kullanılarak SQL injection saldırıları önlenir.
*   **CSS:** Basit bir stil dosyası ile sayfanın görünümü iyileştirilir.
*   **Hesap Makinesi Sayfası:**  `calculator.php` sayfası, oturumun kontrol edildiği ve kullanıcının giriş yapmış olup olmadığının doğrulandığı bir örnektir.

**Nasıl Çalıştırılır:**

1.  Yukarıdaki dosyaları oluşturun ve aynı dizine kaydedin.
2.  `config.php` dosyasındaki veritabanı bilgilerini kendi veritabanınıza göre düzenleyin.
3.  Veritabanınızda `kullanicilar` adında bir tablo oluşturun ve örnek kullanıcı ekleyin (şifreyi hash'leyerek).
4.  Web sunucunuzu başlatın ve `login.php` sayfasına gidin.

**Önemli Güvenlik Notları:**

*   **Veritabanı Bilgilerini Gizli Tutun:** `config.php` dosyasını web erişimi olmayan bir dizinde saklayın.
*   **Şifreleri Hash'leyin:** Veritabanına kaydederken şifreleri her zaman `password_hash()` fonksiyonu ile hash'leyin.
*   **SQL Injection'ı Önleyin:** PDO prepared statements kullanarak SQL injection saldırılarını önleyin.
*   **XSS'i Önleyin:** Kullanıcıdan gelen verileri (özellikle hata mesajlarını) ekrana yazdırırken `htmlspecialchars()` fonksiyonunu kullanın.
*   **CSRF'yi Önleyin:** Önemli işlemlerde (örneğin, şifre değiştirme) CSRF token'ları kullanın.
*   **HTTPS Kullanın:** Verilerin güvenli bir şekilde iletilmesi için her zaman HTTPS kullanın.

Bu kapsamlı örnek, PHP ile güvenli ve işlevsel bir giriş sayfası oluşturmanıza yardımcı olacaktır.  Kodu kendi ihtiyaçlarınıza göre uyarlayabilir ve geliştirebilirsiniz.  Unutmayın, güvenlik her zaman öncelikli olmalıdır!
