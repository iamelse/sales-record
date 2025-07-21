# Aplikasi Pencatatan Penjualan - Laravel

Aplikasi pencatatan penjualan berbasis web yang dibuat menggunakan Laravel. Aplikasi ini mencakup berbagai fitur seperti manajemen penjualan, pembayaran, dan master data untuk pengguna dan item.

---

## 📊 Fitur Utama

### 1. Dashboard
Menampilkan ringkasan data penjualan dalam bentuk widget dan chart interaktif.

#### 🔍 Fitur Dashboard:
- Filter **date range** yang mempengaruhi data widget dan chart.
- Widget:
    - Jumlah Transaksi
    - Jumlah Penjualan (Rupiah)
    - Jumlah Item Terjual (Qty)
- Chart:
    - Penjualan per Bulan (Rupiah)
    - Penjualan per Item (Qty)

---

### 2. Modul Penjualan

#### 🧾 List Penjualan
- Tabel list penjualan terbaru berada di paling atas.
- Filter tanggal penjualan yang langsung memfilter isi tabel.

#### ➕ Tambah Penjualan
- Kode penjualan **otomatis digenerate**.
- Satu penjualan dapat terdiri dari **lebih dari satu item**.
- Setiap baris terdiri dari: `Qty`, `Harga`, dan `Total Harga`.
- Penjualan baru memiliki status **"Belum Dibayar"**.

#### 🔍 Lihat Detail Penjualan
- Melihat detail dari penjualan yang telah dibuat.

#### ✏️ Edit Penjualan
- Mengubah data penjualan dan item terkait.
- **Tidak bisa di-edit jika status penjualan adalah "Sudah Dibayar"**.

#### ❌ Delete Penjualan
- **Tidak bisa dihapus jika status penjualan adalah "Sudah Dibayar"**.

---

### 3. Modul Pembayaran

#### 🧾 List Pembayaran
- Tabel list pembayaran terbaru berada di paling atas.
- Filter tanggal pembayaran yang langsung memfilter isi tabel.

#### ➕ Tambah Pembayaran
- Kode pembayaran **otomatis digenerate**.
- Pembayaran ditautkan ke satu penjualan.
- Setelah submit, status penjualan berubah menjadi **"Sudah Dibayar"**.

#### 🔍 Lihat Detail Pembayaran
- Melihat detail dari pembayaran yang telah dilakukan.

#### ✏️ Edit Pembayaran
- Mengubah informasi pembayaran.
- **Tidak bisa mengubah penjualan yang dibayar**.

#### ❌ Delete Pembayaran
- Jika dihapus, status penjualan akan berubah menjadi **"Belum Dibayar"**.

---

### 4. Modul Master

#### 👤 User
- **List User**.
- **Tambah User**: Nama, Email, Password.
- **Edit User**: Nama, Email, Password.
- **Delete User**.
- 🔒 Nilai Tambahan:
    - Role dan Permission menggunakan **Laravel Spatie Permission**.

#### 📦 Item
- **List Item**.
- **Tambah Item**: Kode, Nama, Gambar (upload), Harga.
- **Edit Item**: Kode, Nama, Gambar (upload), Harga.
- **Delete Item**.

---

## 🚀 Teknologi yang Digunakan
- Laravel 12 (Backend Framework)
- Laravel Spatie Permission (Role & Permission)
- Chart.js (untuk visualisasi data)
- Tailwind (untuk styling UI)

---

## 🛠 Instalasi

1. Clone repository:
   ```bash
   git clone https://github.com/iamelse/sales-record.git
   cd sales-record
   ```

2. Install dependensi:
    ```bash
   composer install
   npm install && npm run dev
   ```

3. Setup file environment dan database:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   ```bash
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your-db
   DB_USERNAME=your-db-username
   DB_PASSWORD=your-db-password
   ```
   
4. Jalankan migrasi dan seeder
   ```bash
   php artisan migrate --seed
   ```
   
5. Jalankan aplikasi
   ```bash 
   php artisan serve
   ```

## 🔐 Role & Permission

Aplikasi ini menggunakan sistem Role & Permission yang didefinisikan melalui App\Enums\RoleEnum dan PermissionEnum. Setiap role memiliki hak akses tertentu terhadap fitur-fitur aplikasi.

### 🎭 Daftar Role
1. Admin

Memiliki akses penuh ke seluruh fitur dan manajemen data.

Permission:

    Dashboard (baca)

    User (buat, lihat, ubah, hapus)

    Role & Permission (buat, lihat, ubah, hapus, atur hak akses)

    Penjualan (buat, lihat, ubah, hapus)

    Pembayaran (buat, lihat, hapus)

    Item (buat, lihat, ubah, hapus)

2. Cashier

Bertanggung jawab atas proses transaksi penjualan dan pembayaran.

Permission:

    Dashboard (baca)

    Penjualan (buat, lihat, ubah, hapus)

    Pembayaran (buat, lihat, hapus)

    Item (hanya baca)

3. Manager

Memiliki akses monitoring terhadap aktivitas penjualan, pembayaran, dan produk.

Permission:

    Dashboard (baca)

    Penjualan (baca)

    Pembayaran (baca)

    Item (baca)

## 👤 Akun Login Default

Berikut adalah akun pengguna yang bisa digunakan untuk login awal pada sistem:
```bash
Role	Email	Password
Admin	admin@example.com	password
Cashier	cashier@example.com	password
Manager	manager@example.com	password
```

## ✅ Catatan Tambahan
* Konfigurasi database di .env sebelum menjalankan migrasi.
* Alasan saya tidak menggunakan DataTables bukan karena tidak bisa, melainkan karena integrasinya dengan Tailwind CSS masih membutuhkan styling manual, yang saat ini belum saya prioritaskan.
