Tamamdır, işte belirtilen özelliklere sahip bir PHP hesap makinesi sayfası örneği:

**1. Veritabanı Bağlantısı (db_config.php):**

Bu dosya, veritabanı bağlantı bilgilerini içerir. Güvenlik nedeniyle, bu bilgileri doğrudan koda gömmek yerine ayrı bir dosyada saklamak ve `.gitignore` ile izlenmemesini sağlamak önemlidir.

```php
<?php
$servername = "localhost"; // Veritabanı sunucu adı
$username = "kullaniciadi"; // Veritabanı kullanıcı adı
$password = "parola"; // Veritabanı parolası
$dbname = "veritabanıadı"; // Veritabanı adı

// Veritabanı bağlantısını oluştur
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}
?>
```

**2. Oturum Yönetimi (session.php):**

Bu dosya, oturum başlatma ve kullanıcı girişini kontrol etme işlemlerini yapar.

```php
<?php
session_start();

// Kullanıcının giriş yapıp yapmadığını kontrol et
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Kullanıcı girişi yapmamışsa yönlendir
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php"); // Giriş sayfasına yönlendir
        exit();
    }
}

// Kullanıcıyı oturumdan çıkar
function logout() {
    session_destroy();
    header("Location: login.php"); // Giriş sayfasına yönlendir
    exit();
}
?>
```

**3. Hesap Makinesi Sayfası (calculator.php):**

```php
<?php
require_once 'session.php'; // Oturum kontrolünü dahil et
require_once 'db_config.php'; // Veritabanı bağlantısını dahil et

requireLogin(); // Giriş yapılmamışsa yönlendir

// İşlem sonucunu saklamak için değişken
$result = "";

// Form gönderilmişse
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Güvenlik için girişleri temizle ve doğrula
    $number1 = filter_input(INPUT_POST, 'number1', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $number2 = filter_input(INPUT_POST, 'number2', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $operation = filter_input(INPUT_POST, 'operation', FILTER_SANITIZE_STRING);

    // Girişlerin sayısal olup olmadığını kontrol et
    if (is_numeric($number1) && is_numeric($number2)) {
        // İşleme göre hesaplama yap
        switch ($operation) {
            case "add":
                $result = $number1 + $number2;
                break;
            case "subtract":
                $result = $number1 - $number2;
                break;
            case "multiply":
                $result = $number1 * $number2;
                break;
            case "divide":
                // Sıfıra bölme kontrolü
                if ($number2 != 0) {
                    $result = $number1 / $number2;
                } else {
                    $result = "Sıfıra bölme hatası!";
                }
                break;
            default:
                $result = "Geçersiz işlem!";
        }
    } else {
        $result = "Lütfen geçerli sayılar girin!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hesap Makinesi</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Hesap Makinesi</h1>

        <form method="post">
            <input type="text" name="number1" placeholder="Sayı 1" required><br><br>
            <input type="text" name="number2" placeholder="Sayı 2" required><br><br>

            <select name="operation">
                <option value="add">Toplama (+)</option>
                <option value="subtract">Çıkarma (-)</option>
                <option value="multiply">Çarpma (*)</option>
                <option value="divide">Bölme (/)</option>
            </select><br><br>

            <button type="submit">Hesapla</button>
        </form>

        <?php if ($result != "") { ?>
            <div class="result">
                Sonuç: <?php echo htmlspecialchars($result); ?>
            </div>
        <?php } ?>

        <p><a href="logout.php">Çıkış Yap</a></p>
    </div>
</body>
</html>
```

**4. Stil Dosyası (style.css):**

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
    width: 300px;
    text-align: center;
}

h1 {
    color: #333;
}

input[type="text"], select, button {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
}

button {
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #3e8e41;
}

.result {
    margin-top: 20px;
    padding: 10px;
    background-color: #e9e9e9;
    border-radius: 4px;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
```

**5. Giriş Sayfası (login.php):**

```php
<?php
require_once 'db_config.php'; // Veritabanı bağlantısını dahil et
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password']; // Parolayı olduğu gibi al (hash'lenmiş parolayla karşılaştırılacak)

    // Veritabanında kullanıcıyı ara
    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Parolayı doğrula (password_verify() kullanılarak)
        if (password_verify($password, $row["password"])) {
            // Oturum değişkenlerini ayarla
            $_SESSION['user_id'] = $row["id"];
            $_SESSION['username'] = $row["username"];

            // Hesap makinesi sayfasına yönlendir
            header("Location: calculator.php");
            exit();
        } else {
            $error = "Yanlış parola!";
        }
    } else {
        $error = "Kullanıcı bulunamadı!";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Giriş</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Giriş</h1>

        <?php if ($error != "") { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>

        <form method="post">
            <input type="text" name="username" placeholder="Kullanıcı Adı" required><br><br>
            <input type="password" name="password" placeholder="Parola" required><br><br>

            <button type="submit">Giriş Yap</button>
        </form>
    </div>
</body>
</html>
```

**6. Çıkış Sayfası (logout.php):**

```php
<?php
require_once 'session.php';
logout(); // Oturumu sonlandır ve giriş sayfasına yönlendir
?>
```

**7. Kullanıcı Tablosu Oluşturma:**

Veritabanında `users` adında bir tablo oluşturun.  Bu tabloda `id`, `username`, ve `password` alanları bulunmalıdır.  `password` alanı, parolaların güvenli bir şekilde saklanması için `VARCHAR(255)` gibi yeterince uzun bir metin alanı olmalıdır.

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);
```

**8. İlk Kullanıcıyı Ekleme (Örnek):**

Güvenlik nedeniyle, parolaları düz metin olarak saklamayın. Bunun yerine, `password_hash()` fonksiyonunu kullanarak parolaları şifreleyin.

```php
<?php
require_once 'db_config.php';

$username = "testkullanici";
$password = "gizliparola"; // Gerçekte güvenli bir parola kullanın
$hashed_password = password_hash($password, PASSWORD_DEFAULT); // Parolayı şifrele

$sql = "INSERT INTO users (username, password) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $hashed_password);

if ($stmt->execute()) {
    echo "Kullanıcı başarıyla eklendi.";
} else {
    echo "Hata: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
```

**Önemli Güvenlik Notları:**

*   **Parola Güvenliği:**  Kullanıcı parolalarını asla düz metin olarak saklamayın.  `password_hash()` fonksiyonunu kullanarak parolaları şifreleyin ve `password_verify()` ile doğrulayın.
*   **SQL Enjeksiyonu:**  Kullanıcı girişlerini her zaman temizleyin ve doğrulayın.  Prepared statements kullanarak SQL enjeksiyonu saldırılarını önleyin.
*   **XSS (Cross-Site Scripting):**  Kullanıcıdan gelen verileri (özellikle sonucu görüntülerken)  `htmlspecialchars()` fonksiyonu ile filtreleyerek XSS saldırılarını önleyin.
*   **Oturum Güvenliği:**  Oturumları güvenli bir şekilde yönetin.  Oturum kimliklerini çalınmaya karşı koruyun (HTTPS kullanın, oturum çerezlerini güvenli ve HTTPOnly olarak işaretleyin).
*   **Hata Raporlama:**  Üretim ortamında hata raporlamayı kapatın veya dikkatli bir şekilde yapılandırın.  Hata mesajları hassas bilgileri açığa çıkarabilir.
*   **.gitignore:** `.gitignore` dosyasına, veritabanı bağlantı bilgilerini içeren `db_config.php` dosyasını eklemeyi unutmayın. Bu, hassas bilgilerin yanlışlıkla bir Git deposuna yüklenmesini önler.

**Ek İyileştirmeler:**

*   **Hata İşleme:** Daha kapsamlı hata işleme mekanizmaları ekleyin.  Örneğin, kullanıcıya daha açıklayıcı hata mesajları gösterin ve hataları bir günlük dosyasına kaydedin.
*   **Doğrulama:**  İstemci tarafında (JavaScript ile) ve sunucu tarafında (PHP ile) girdi doğrulaması yapın.
*   **CSS Framework'ü:**  Daha iyi bir görünüm için Bootstrap veya Tailwind CSS gibi bir CSS framework'ü kullanabilirsiniz.
*   **Kullanıcı Arayüzü:**  Kullanıcı deneyimini iyileştirmek için daha gelişmiş bir arayüz tasarlayın (örneğin, JavaScript kullanarak gerçek zamanlı hesaplama).

Bu kapsamlı örnek, güvenli ve işlevsel bir PHP hesap makinesi oluşturmanıza yardımcı olacaktır.  Güvenlik notlarına dikkat ettiğinizden ve kodu ihtiyaçlarınıza göre uyarladığınızdan emin olun.
