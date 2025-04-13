Tamamdır, işte PHP, HTML ve CSS kullanarak güvenli, yorumlanmış ve temiz bir kayıt sayfası oluşturma adımları ve kodu:

**1. Veritabanı Bağlantısı (db.php)**

Bu dosya, veritabanı bağlantısı için gerekli bilgileri içerir ve PDO kullanarak bağlantıyı sağlar.

```php
<?php

$host = 'localhost'; // Veritabanı sunucusu
$dbname = 'kullanicilar'; // Veritabanı adı
$username = 'root'; // Veritabanı kullanıcı adı
$password = ''; // Veritabanı şifresi (eğer varsa)

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
}

?>
```

**2. Kayıt Sayfası (register.php)**

Bu dosya, kayıt formunu görüntüler, kullanıcı girdilerini alır, doğrular, şifreyi hash'ler ve veritabanına kaydeder.

```php
<?php
session_start(); // Oturum başlat

require_once 'db.php'; // Veritabanı bağlantısını dahil et

$errors = []; // Hata mesajları için dizi

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form gönderildiğinde yapılacak işlemler

    $username = trim($_POST['username']); // Kullanıcı adı
    $password = trim($_POST['password']); // Şifre

    // Kullanıcı adı doğrulama
    if (empty($username)) {
        $errors[] = "Kullanıcı adı boş olamaz.";
    } elseif (strlen($username) < 3) {
        $errors[] = "Kullanıcı adı en az 3 karakter olmalıdır.";
    }

    // Şifre doğrulama
    if (empty($password)) {
        $errors[] = "Şifre boş olamaz.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Şifre en az 6 karakter olmalıdır.";
    }

    // Hata yoksa kullanıcıyı kaydet
    if (empty($errors)) {
        // Şifreyi hash'le
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Kullanıcıyı veritabanına kaydet
            $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hashedPassword]);

            // Başarılı kayıt mesajı (isteğe bağlı)
            $_SESSION['success_message'] = "Kayıt başarılı! Giriş sayfasına yönlendiriliyorsunuz...";

            // Giriş sayfasına yönlendir
            header("Location: login.php");
            exit();
        } catch (PDOException $e) {
            // Veritabanı hatası
            if ($e->getCode() == 23000) {
                $errors[] = "Bu kullanıcı adı zaten alınmış.";
            } else {
                $errors[] = "Kayıt sırasında bir hata oluştu: " . $e->getMessage();
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Kayıt Ol</h1>

        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="register.php" method="post">
            <div class="form-group">
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Şifre:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Kayıt Ol</button>
        </form>

        <p>Zaten bir hesabın var mı? <a href="login.php">Giriş Yap</a></p>
    </div>
</body>
</html>
```

**3. Giriş Sayfası (login.php)**

Bu sayfa, kayıt başarılı olduğunda yönlendirileceğiniz giriş sayfasıdır. Basit bir "Giriş Yap" başlığı ve bir bağlantı içerebilir. (Giriş fonksiyonelliği burada değil.)

```php
<?php
session_start();

// Başarılı kayıt mesajı varsa göster
if (isset($_SESSION['success_message'])) {
    echo '<div class="success-message">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
    unset($_SESSION['success_message']); // Mesajı temizle
    header("Refresh: 3; url=login.php"); //3 saniye sonra sayfayı yenile
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Giriş Yap</h1>
        <p>Henüz bir hesabın yok mu? <a href="register.php">Kayıt Ol</a></p>
    </div>
</body>
</html>
```

**4. Stil Dosyası (style.css)**

Basit bir stil dosyası örneği:

```css
body {
    font-family: sans-serif;
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
    width: 400px;
    text-align: center;
}

h1 {
    color: #333;
}

.form-group {
    margin-bottom: 15px;
    text-align: left;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

button {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

button:hover {
    background-color: #3e8e41;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

.error-box {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 4px;
    text-align: left;
}

.success-message {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 4px;
    text-align: center;
}

```

**5. Veritabanı Tablosu Oluşturma**

MySQL'de aşağıdaki SQL komutunu kullanarak `users` tablosunu oluşturun:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);
```

**Önemli Notlar ve Güvenlik İpuçları:**

*   **Veritabanı Güvenliği:**  Veritabanı kullanıcı adınızı ve şifrenizi doğrudan kod içinde saklamayın. Ortam değişkenlerini veya daha güvenli yöntemleri kullanın.
*   **SQL Injection:** PDO'nun prepared statements özelliğini kullanarak SQL injection saldırılarına karşı korunun. Verileri doğrudan SQL sorgularına eklemeyin.
*   **XSS (Cross-Site Scripting):**  Kullanıcı girdilerini her zaman `htmlspecialchars()` ile temizleyin. Bu, zararlı HTML veya JavaScript kodunun sayfada çalışmasını engeller.
*   **Şifre Güvenliği:**  `password_hash()` fonksiyonu güvenli şifreleme için tasarlanmıştır. `PASSWORD_DEFAULT` algoritması, PHP'nin en güncel ve güvenli algoritmasını kullanır.
*   **Hata Yönetimi:**  Geliştirme sırasında hataları görmek için `error_reporting(E_ALL);` ve `ini_set('display_errors', 1);` kullanın.  Ancak, üretim ortamında bunları kapatın ve hataları bir günlük dosyasına kaydedin.
*   **Oturum Yönetimi:** Oturumları güvenli bir şekilde yönetmek için `session_start()` kullanın ve oturum verilerini gerektiğinde temizleyin.
*   **CSRF (Cross-Site Request Forgery):**  Önemli formlar için CSRF token'ları ekleyin. Bu, kötü niyetli web sitelerinin sizin adınıza istek göndermesini engeller.
*   **Doğrulama:** Hem istemci tarafında (JavaScript) hem de sunucu tarafında (PHP) doğrulama yapın. İstemci tarafı doğrulama kullanıcı deneyimini iyileştirir, ancak sunucu tarafı doğrulama zorunludur çünkü istemci tarafı doğrulama atlatılabilir.
*   **Sınırlama:** Çok fazla sayıda başarısız giriş denemesini engellemek için IP adresini veya kullanıcı adını temel alan bir sınırlama mekanizması uygulayın.
*   **HTTPS:** Her zaman HTTPS kullanın. Bu, verilerin şifrelenmesini sağlar ve ortadaki adam (man-in-the-middle) saldırılarını önler.

Bu adımları takip ederek, güvenli ve kullanıcı dostu bir kayıt sayfası oluşturabilirsiniz. Unutmayın, güvenlik sürekli bir süreçtir. Uygulamanızı düzenli olarak güncelleyin ve güvenlik açıklarına karşı test edin.
