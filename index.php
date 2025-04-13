Tamamdır, istediğiniz özelliklere sahip `index.php` dosyasını oluşturuyorum. Kodun temiz ve anlaşılır olmasına özen göstereceğim ve gerekli yerlerde yorumlar ekleyeceğim.

```php
<?php
session_start(); // Oturum başlatılıyor. Bu, oturum değişkenlerini kullanabilmemiz için gerekli.

// Eğer kullanıcı giriş yapmışsa (yani oturumda bir kullanıcı bilgisi varsa)
if (isset($_SESSION["username"])) {
    // Hesaplama sayfasına yönlendir.
    header("Location: calculator.php");
    exit(); // Yönlendirmeden sonra script'in çalışmasını durdur. Önemli!
} else {
    // Kullanıcı giriş yapmamışsa, giriş sayfasına yönlendir.
    header("Location: login.php");
    exit(); // Yönlendirmeden sonra script'in çalışmasını durdur. Önemli!
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Sayfa</title>
    <link rel="stylesheet" href="style.css"> <!-- CSS dosyamıza bağlantı -->
</head>
<body>
    <div class="container">
        <h1>Hoş Geldiniz</h1>
        <p>Lütfen giriş yapın veya hesaplama sayfasına yönlendirileceksiniz.</p>
    </div>
</body>
</html>
```

**Açıklamalar:**

1.  **`<?php session_start(); ?>`**: Bu satır, PHP oturumlarını başlatır.  Oturumlar, kullanıcıların bir web sitesinde gezinirken (örneğin, bir sayfadan diğerine geçerken) bilgileri saklamak için kullanılır.  Kullanıcının giriş yapıp yapmadığını belirlemek için kullanacağız.
2.  **`if (isset($_SESSION["username"])) { ... }`**: Bu koşul, `$_SESSION["username"]` adlı bir oturum değişkeninin tanımlı olup olmadığını kontrol eder.  Bu değişken, kullanıcının giriş yaptığında (örneğin, kullanıcı adı ve şifresini doğru girdiğinde) oluşturulacaktır.  Eğer bu değişken tanımlıysa, bu, kullanıcının giriş yapmış olduğu anlamına gelir.
3.  **`header("Location: calculator.php");`**: Bu satır, kullanıcıyı `calculator.php` sayfasına yönlendirir. `header()` fonksiyonu, HTTP başlıklarını göndermek için kullanılır.  `Location` başlığı, tarayıcıya yeni bir URL'ye gitmesi gerektiğini söyler.
4.  **`exit();`**:  Bu satır, script'in çalışmasını durdurur.  `header()` fonksiyonu sadece bir başlık gönderir; script'in çalışmasını durdurmaz.  Bu nedenle, yönlendirmeden sonra script'in çalışmaya devam etmesini engellemek için `exit()` fonksiyonunu kullanırız. Aksi takdirde, yönlendirme yapılmasına rağmen, HTML kısmı da tarayıcıya gönderilebilir.
5.  **`else { ... }`**: Eğer `$_SESSION["username"]` tanımlı değilse (yani kullanıcı giriş yapmamışsa), bu blok çalışır.
6.  **`header("Location: login.php");`**:  Bu satır, kullanıcıyı `login.php` sayfasına yönlendirir.
7.  **`<!DOCTYPE html> ... </body>`**: Bu bölüm, basit bir HTML yapısıdır.  Kullanıcı giriş yapmadığında kısa bir karşılama mesajı gösterir.
8.  **`<link rel="stylesheet" href="style.css">`**:  Bu satır, `style.css` adlı bir CSS dosyasını sayfaya bağlar.  Bu dosya, sayfanın görünümünü özelleştirmek için kullanılabilir.

**Ek olarak yapmanız gerekenler:**

*   **`login.php` dosyası oluşturun**: Bu dosya, kullanıcıların giriş yapabileceği bir form içermelidir. Giriş başarılı olduğunda, `$_SESSION["username"]` gibi bir oturum değişkeni ayarlayın (örneğin, kullanıcının kullanıcı adını saklayın).
*   **`calculator.php` dosyası oluşturun**: Bu dosya, hesap makinesi işlevselliğini içermelidir.
*   **`style.css` dosyası oluşturun**: Bu dosya, sayfanın stilini (renkler, yazı tipleri, vb.) tanımlar.  Basit bir stil eklemek bile sayfanın daha iyi görünmesini sağlar.

**Örnek `login.php` (çok basit bir örnek):**

```php
<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // *******************************************************************
    // DİKKAT: Bu örnekte güvenlik önlemleri alınmamıştır!
    // Gerçek bir uygulamada, kullanıcı adını ve şifreyi bir veritabanında
    // saklamanız ve şifreleri güvenli bir şekilde (örneğin, hash'leyerek)
    // saklamanız GEREKİR.  Bu sadece bir örnektir!
    // *******************************************************************

    if ($username == "kullanici" && $password == "sifre") {
        $_SESSION["username"] = $username;
        header("Location: index.php");
        exit();
    } else {
        $error = "Yanlış kullanıcı adı veya şifre.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Giriş Yap</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="username">Kullanıcı Adı:</label><br>
            <input type="text" id="username" name="username"><br><br>

            <label for="password">Şifre:</label><br>
            <input type="password" id="password" name="password"><br><br>

            <input type="submit" value="Giriş">
        </form>
    </div>
</body>
</html>
```

**Önemli Güvenlik Notu:**

Yukarıdaki `login.php` örneği, **kesinlikle güvenlik için uygun değildir**. Kullanıcı adı ve şifreleri düz metin olarak saklamamak ve her zaman şifreleri güvenli bir şekilde hash'lemek önemlidir. Ayrıca, SQL injection gibi yaygın saldırılara karşı da önlemler almanız gerekir.  Bu konular, web geliştirme güvenliğinin temelini oluşturur. Bu örnek sadece çalışma prensibini göstermek içindir.

**Örnek `style.css` (basit bir stil):**

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
    text-align: center;
}

.error {
    color: red;
    margin-bottom: 10px;
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
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box; /* padding'in genişliği etkilememesi için */
}

input[type="submit"] {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #45a049;
}
```

Bu kodu kullanarak, temel bir giriş sistemi ve yönlendirme yapabilirsiniz.  Unutmayın, güvenlik önemli bir konudur ve gerçek bir uygulamada daha fazla dikkat gerektirir.  Başarılar!
