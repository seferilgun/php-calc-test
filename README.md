```markdown
# php-calc-test: PHP Hesap Makinesi Projesi

Bu proje, PHP ile geliştirilmiş basit bir hesap makinesi uygulamasıdır. Kullanıcıların hesap yapabilmesinin yanı sıra, temel kullanıcı girişi/kayıt özelliklerini de içermektedir. Bu proje, PHP öğrenenler için temel bir uygulama örneği teşkil etmeyi amaçlamaktadır.

## Kurulum Talimatları

Bu uygulamayı çalıştırmak için aşağıdaki adımları takip edin:

1.  **Gereksinimler:**
    *   PHP 7.2 veya üzeri
    *   MySQL 5.6 veya üzeri

2.  **PHP ve MySQL Kurulumu:**
    *   Eğer sisteminizde PHP ve MySQL kurulu değilse, öncelikle bu yazılımları kurmanız gerekmektedir.  XAMPP, WAMP veya MAMP gibi hazır paketleri kullanarak bu kurulumu kolayca gerçekleştirebilirsiniz.

3.  **Veritabanı Oluşturma:**
    *   MySQL sunucunuza bağlanın ve `php_calc_test` adında bir veritabanı oluşturun. Örneğin:
        ```sql
        CREATE DATABASE php_calc_test;
        ```

4.  **Veritabanı Yapısını Yükleme:**
    *   Proje klasöründeki `database.sql` dosyasını oluşturduğunuz `php_calc_test` veritabanına aktarın. Bu, gerekli tabloları (örneğin, kullanıcılar tablosu) ve verileri oluşturacaktır.  MySQL Workbench veya benzeri bir araç kullanarak `database.sql` dosyasını veritabanınıza aktarabilirsiniz.

5.  **Dosya Yapılandırması:**
    *   Proje klasöründeki `config.php` veya benzeri bir yapılandırma dosyasını bulun.
    *   Bu dosyayı açın ve aşağıdaki bilgileri güncelleyin:
        *   Veritabanı sunucu adı (genellikle `localhost`)
        *   Veritabanı kullanıcı adı (genellikle `root`)
        *   Veritabanı parolası
        *   Veritabanı adı (`php_calc_test`)

6.  **Dosyaları Sunucuya Yükleme:**
    *   Proje klasörünü, web sunucunuzun kök dizinine (örneğin, XAMPP için `htdocs` klasörü) yükleyin.

7.  **Uygulamayı Çalıştırma:**
    *   Web tarayıcınızda `http://localhost/php-calc-test/` (veya proje klasörünüzün adıyla değiştirin) adresine gidin.

## Kullanım Örneği

1.  **Kayıt Olma:**
    *   Uygulamanın ana sayfasında, "Kayıt Ol" bağlantısına tıklayın.
    *   Gerekli bilgileri (kullanıcı adı, e-posta adresi, parola vb.) doldurun ve kayıt işlemini tamamlayın.

2.  **Giriş Yapma:**
    *   Kayıt olduktan sonra, ana sayfadaki "Giriş Yap" bağlantısına tıklayın.
    *   Kullanıcı adınızı ve parolanızı girerek giriş yapın.

3.  **Hesap Makinesini Kullanma:**
    *   Giriş yaptıktan sonra, hesap makinesi arayüzüne yönlendirileceksiniz.
    *   Sayi değerlerini ve istediğiniz işlemi (toplama, çıkarma, çarpma, bölme) seçin.
    *   "Hesapla" düğmesine tıklayın.
    *   Sonuç ekranda görüntülenecektir.

## Bağımlılıklar

*   **PHP:** 7.2 veya üzeri
*   **MySQL:** 5.6 veya üzeri

## Katkıda Bulunma

Bu projeye katkıda bulunmak isterseniz, lütfen aşağıdaki adımları izleyin:

1.  Projeyi fork edin.
2.  Kendi branch'inizi oluşturun (`git checkout -b feature/yeni-ozellik`).
3.  Değişikliklerinizi commit edin (`git commit -am 'Yeni özellik eklendi'`).
4.  Branch'inizi push edin (`git push origin feature/yeni-ozellik`).
5.  Pull request oluşturun.

## Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Daha fazla bilgi için `LICENSE` dosyasına bakın.
```