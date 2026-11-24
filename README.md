# Task Management & Audit Trail

Aplikasi monolit Laravel + PostgreSQL dengan tampilan Blade untuk mengelola proyek dan tugas disertai audit trail (`activity_logs`).

---

## 1. Akun Contoh (Demo)

Dibuat otomatis oleh seeder (`DatabaseSeeder`):

- **Email**: `user@example.com`
- **Password**: `password123`

---

## 2. Cara Menjalankan Aplikasi

Pilih salah satu cara: menggunakan Docker/Podman Compose (direkomendasikan) atau instalasi lokal.

### Opsi A: Menggunakan Docker Compose (Direkomendasikan)

1. Pastikan file `.env` sudah ada dan dikonfigurasi untuk PostgreSQL container:
   ```sh
   cp .env.example .env
   ```
   Pastikan environment database di `.env` (atau sudah otomatis di-inject compose):
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=postgres
   DB_PORT=5432
   DB_DATABASE=laravel
   DB_USERNAME=laravel
   DB_PASSWORD=secret
   ```

2. Jalankan container:
   ```sh
   docker compose up -d
   ```

3. Jalankan migrasi dan seeder di dalam container:
   ```sh
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate --seed
   ```

4. Buka browser:
   - URL: `http://localhost:8000`

---

### Opsi B: Instalasi Lokal (Tanpa Docker)

1. Prasyarat:
   - PHP >= 8.3 (dengan ekstensi `pdo_pgsql`, `mbstring`, `tokenizer`, `xml`, dll.)
   - Composer
   - PostgreSQL running lokal (buat DB bernama `laravel`)
   - Node.js & NPM (opsional untuk build asset Vite jika diperlukan)

2. Pasang dependensi dan setup file `.env`:
   ```sh
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

3. Sesuaikan koneksi PostgreSQL pada `.env`:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=laravel
   DB_USERNAME=postgres
   DB_PASSWORD=your_password
   ```

4. Jalankan migrasi dan seeder:
   ```sh
   php artisan migrate --seed
   ```

5. Jalankan server lokal:
   ```sh
   php artisan serve
   ```
   Akses di `http://127.0.0.1:8000`.

---

## 3. Cara Import Database Tanpa Laravel (File SQL)

File `database/dump.sql` berisi DDL tabel (`users`, `projects`, `tasks`, `activity_logs`). Untuk data contoh, jalankan `php artisan migrate --seed` (lihat bagian sebelumnya).

Gunakan CLI PostgreSQL (`psql`):

```sh
# Melalui psql lokal:
psql -h 127.0.0.1 -U laravel -d laravel -f database/dump.sql

# Atau langsung via container (file dibaca dari host lewat stdin):
podman compose exec -T postgres psql -U laravel -d laravel < database/dump.sql
# (atau via docker compose)
# docker compose exec -T postgres psql -U laravel -d laravel < database/dump.sql
```

---

## 4. Menjalankan Automated Test

Jalankan test suite menggunakan PHPUnit via Artisan:

```sh
# Di dalam container:
podman compose exec app php artisan test

# Atau lokal:
php artisan test
```

Test mencakup:
- **Hak Akses & Policy**: Isolasi data antar-pengguna (`ProjectRequestAndServiceTest`).
- **Transisi Status & Validasi**: Larangan loncat status (`pending` -> `in_progress` -> `completed`).
- **Pendeteksian N+1 Query**: Efisiensi query pada dashboard dan detail proyek (`ListingQueryTest`).
- **Race Condition / Concurrency Status**: Keamanan update status ganda dan deduplikasi log (`TaskStatusConcurrencyTest`).

Status terkini: `ListingQueryTest::test_task_list_query_count_does_not_grow_with_task_count` dan `TaskStatusConcurrencyTest::test_duplicate_status_requests_apply_one_transition_and_log` masih **merah** pada kode saat ini—keduanya sengaja dibuat sebagai regression test untuk defect N+1 dan duplikasi log saat update status bersamaan.

---

## 5. Ringkasan Fitur & Catatan Implementasi

### Bagian A (Selesai)
- Autentikasi custom (Register, Login, Logout) dengan Blade.
- Otorisasi menggunakan Laravel Policy (`ProjectPolicy`, `TaskPolicy`) untuk mencegah akses data milik pengguna lain via URL langsung.
- CRUD Proyek & Tugas dengan validasi Form Request (`StoreProjectRequest`, `UpdateProjectRequest`, `StoreTaskRequest`, `UpdateTaskStatusRequest`).
- Validasi status satu arah: `pending` &rarr; `in_progress` &rarr; `completed`.
- Pencatatan aktivitas di `activity_logs` terbungkus dalam `DB::transaction`.

### Bagian B (Selesai)
- Halaman riwayat aktivitas (`/activities`) dengan pagination dan filter aksi + rentang tanggal.
- Pencarian judul dan filter status tugas pada halaman detail proyek, beserta penanda badge "Terlambat" untuk tugas melewati `due_date`.
- Pemisahan business logic ke `ProjectService` dan `TaskService`.
