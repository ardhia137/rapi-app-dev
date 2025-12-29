# Install Laravel Rapi App

Project ini dibangun menggunakan **Laravel 12 + TailwindCSS**.

## Cara Install Project Rapi App

1. Clone repository:
    ```bash
    git clone https://github.com/zildanfaisal/rapi-app.git
    ```
2. Install depedency PHP dengan Composer
    ```bash
    composer install
    ```
3. Install depedency JS dengan NPM
    ```bash
    npm install
    ```
4. Buat database dengan nama rapi-database

5. Copy file .env.example menjadi .env
    ```bash
    cp .env.example .env
    ```
    Setelahnya isi .env sesuaikan dengan nama database
6. Setelah sudah sesuai nama databasenya di .env, generate app key
    ```bash
    php artisan key:generate
    ```
7. Migrasi ke database
    ```bash
    php artisan migrate
    ```
8. Tes jalankan project secara lokal
    - Jalankan server Laravel
        ```bash
        php artisan serve
        ```
    - Jalankan build asset (TailwindCSS, JS, dll)
        ```bash
        npm run dev
        ```
9. Masukkan data ke database dengan Seeder (optional)
    ```bash
    php artisan db:seed
    ```  
10. Selesai, selamat anda telah berhasil install project Rapi App.
