# Spor Kuponu Uygulaması

Bu proje, spor kuponlarını yönetmek ve maç sonuçlarını kontrol etmek için geliştirilmiş bir React uygulamasıdır. Uygulama, Firebase'i veritabanı olarak kullanır ve API'den maç verilerini çekerek kuponlar oluşturur ve yönetir.

## Kurulum

### Gerekli Yazılımlar

- Node.js
- npm (Node Package Manager)
- Firebase Hesabı ve Projesi

### Adımlar

1. Projeyi klonlayın:
    ```sh
    git clone https://github.com/kullaniciadi/spor-kuponu-uygulamasi.git
    cd spor-kuponu-uygulamasi
    ```

2. Gerekli paketleri yükleyin:
    ```sh
    npm install
    ```

3. Firebase yapılandırma dosyasını (`Model.js` içinde) güncelleyin. Firebase projeniz için gerekli yapılandırma bilgilerini sağlayın.

4. Uygulamayı başlatın:
    ```sh
    npm start
    ```

## Kullanım

### Uygulama Sayfaları

- **Ana Sayfa (PageHome)**: Tüm kuponları görüntüler ve toplam harcama, kazanç ve karı hesaplar.
- **Maç Getir (PageMatchGet)**: API'den maç verilerini çekerek yeni kuponlar oluşturur.
- **Maç Kontrol Et (PageMatchCheck)**: Kuponlardaki maçların sonuçlarını kontrol eder ve günceller.

### Komutlar

- `npm start`: Uygulamayı başlatır.
- `npm run build`: Uygulamayı üretim için paketler.

## Yapı

- **index.js**: Uygulamanın giriş noktası.
- **PageLayout.js**: Uygulama düzeni.
- **PageHome.js**: Ana sayfa bileşeni.
- **PageMatchGet.js**: Maç verilerini çeken bileşen.
- **PageMatchCheck.js**: Maç sonuçlarını kontrol eden bileşen.
- **Model.js**: Firebase ile etkileşim kuran model sınıfı.

## Lisans

Bu proje MIT lisansı ile lisanslanmıştır. Daha fazla bilgi için `LICENSE` dosyasına bakın.
