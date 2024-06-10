# Ultimate Bet Simulator

<img src="src/image-header.webp" style="width: 1920px; height: 300px;object-fit:cover; border-radius: 5px" alt="">

## Proje Genel Bakış

**Ultimate Bet Simulator**, API'den maç verilerini çekerek 4 maçlık kuponlar oluşturur, her kupona 1000 TL yatırır ve
kar/zarar simülasyonu yapar. Bu uygulama, bahis dünyasına dair analiz yapmanıza ve stratejilerinizi test etmenize olanak
tanır.

## Proje URL'si

Canlı sürümü inceleyin: [xsh-react-firebase.firebaseapp.com](https://xsh-react-firebase.firebaseapp.com)

## İletişim

Sorularınız için: [xshmrz@gmail.com](mailto:xshmrz@gmail.com)

## Başlarken

Bu projeyi yerel ortamınızda çalıştırmak için aşağıdaki adımları takip edin.

### Gereksinimler

- Node.js
- npm (Node Package Manager)
- Firebase hesabı

### Kurulum

1. **Depoyu Klonlayın:**

    ```bash
    git clone https://github.com/yourusername/ultimate-bet-simulator.git
    cd ultimate-bet-simulator
    ```

2. **Bağımlılıkları Yükleyin:**

    ```bash
    npm install
    ```

3. **`.env` Dosyasını Oluşturun:**

   Projenin kök dizininde bir `.env` dosyası oluşturun ve aşağıdaki içeriği ekleyin:

    ```plaintext
    REACT_APP_API_URL_MATCH_GET=https://sportsbook.iddaa.com/SportsBook/getPopulerBets?sportId=1&limit=40
    REACT_APP_API_URL_CHECK=https://statistics.iddaa.com/broadage/getEventListCache?SportId=1&SearchDate=

    REACT_APP_FIREBASE_API_KEY=your-firebase-api-key
    REACT_APP_FIREBASE_AUTH_DOMAIN=your-firebase-auth-domain
    REACT_APP_FIREBASE_DATABASE_URL=your-firebase-database-url
    REACT_APP_FIREBASE_PROJECT_ID=your-firebase-project-id
    REACT_APP_FIREBASE_STORAGE_BUCKET=your-firebase-storage-bucket
    REACT_APP_FIREBASE_MESSAGING_SENDER_ID=your-firebase-messaging-sender-id
    REACT_APP_FIREBASE_APP_ID=your-firebase-app-id

    REACT_APP_MATCHES_PER_COUPON=4

    REACT_APP_COLOR_PRIMARY=#007BFF
    REACT_APP_COLOR_SUCCESS=#28A745
    REACT_APP_COLOR_DANGER=#DC3545
    REACT_APP_COLOR_WARNING=#8C6600
    REACT_APP_COLOR_LIGHT=#D9E1EC
    REACT_APP_COLOR_DARK=#343A40
    ```

4. **Uygulamayı Başlatın:**

    ```bash
    npm start
    ```

   Uygulama [http://localhost:3000](http://localhost:3000) adresinde çalışacaktır.

## Kullanım

Uygulama açıldığında, API'den maç verilerini çeker ve 4 maçlık kuponlar oluşturur. Her kupona 1000 TL yatırılır ve
sonuçlar simüle edilerek kar/zarar hesaplanır. Kuponlar ve maç sonuçları uygulama arayüzünde görüntülenir.

## Dağıtım

Firebase kullanarak projeyi dağıtmak için:

```bash
firebase deploy
```

## Proje Yapısı

- **`index.js`**: Uygulamanın ana giriş noktası, yönlendirmeyi ayarlar.
- **`Model.js`**: Firebase veritabanı işlemlerini yönetir.
- **`PageHome.js`**: Kuponları gösterir ve kar/zarar hesaplar.
- **`PageMatchGet.js`**: Maç verilerini çeker ve kuponlar oluşturur.
- **`PageMatchCheck.js`**: Maç sonuçlarını kontrol eder ve veritabanını günceller.
- **`PageLayout.js`**: Sayfalar için düzen bileşeni.

## Simülasyon Detayları

- **Her Kupon:** 4 maç içerir.
- **Bahis Miktarı:** Her kupona 1000 TL.
- **Kar/Zarar Hesaplama:** Her kupondaki maçların oranlarına dayanarak yapılır.

## Lisans

Bu proje MIT Lisansı ile lisanslanmıştır - ayrıntılar için [LICENSE](LICENSE) dosyasına bakın.
