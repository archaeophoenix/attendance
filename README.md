# Dokumentasi REST API - Sistem Presensi Pegawai

Repositori ini menyediakan layanan REST API untuk sistem presensi karyawan, manajemen data pegawai, hari libur, serta rekapitulasi laporan bulanan.

## 📋 Fitur Utama
* **Manajemen Pegawai (CRUD + Paging Dinamis)**
* **Manajemen Hari Libur (CRUD + Paging Dinamis)**
* **Sistem Presensi (Check-in, Check-out, & Pengajuan Izin/Sakit)**
* **Laporan Bulanan (Format Ringkasan Massal & Detail Individu)**

---

## 🚀 Daftar Endpoint API

Semua endpoint ditambahkan dengan prefix `/api`.

### 1. Autentikasi & Presensi (`/api/attendance`)

| Method | Endpoint | Fungsi | Payload / Query Parameter |
| :--- | :--- | :--- | :--- |
| **POST** | `/attendance/check-in` | Absen masuk kerja hari ini | `{"employee_id": 1}` |
| **POST** | `/attendance/check-out` | Absen pulang kerja hari ini | `{"employee_id": 1}` |
| **POST** | `/attendance/absence` | Mencatat izin, sakit, atau cuti | `{"employee_id": 1, "date": "2026-08-04", "status": "Sick", "note": "Demam"}` |

### 2. Manajemen Karyawan (`/api/employees`)

Endpoint ini dilengkapi dengan fitur pencarian dan *pagination* dinamis.

| Method | Endpoint | Fungsi | Query Parameter (Opsional) |
| :--- | :--- | :--- | :--- |
| **GET** | `/employees` | Ambil daftar pegawai | `page`, `per_page`, `search`, `status`, `position` |
| **POST** | `/employees` | Tambah pegawai baru | `{ "name": "Jane Doe", "email": "jane@example.com", "phone": "08129876543", "position": "Staff" / "Admin", "status": "Active" / "Inactive", "join_date": "2026-06-19" }` |
| **GET** | `/employees/{id}` | Detail info satu pegawai | *None* |
| **PUT** | `/employees/{id}` | Perbarui data pegawai | `{ "name": "Jane Doe", "email": "jane@example.com", "phone": "08129876543", "position": "Staff" / "Admin", "status": "Active" / "Inactive", "join_date": "2026-06-19" }` |
| **DELETE**| `/employees/{id}` | Hapus pegawai (Soft Delete) | *None* |

> 💡 **Contoh Paging Dinamis:** `/api/employees?page=1&per_page=25&search=John&status=Active`

### 3. Manajemen Hari Libur (`/api/holidays`)

| Method | Endpoint | Fungsi | Query Parameter / Payload |
| :--- | :--- | :--- | :--- |
| **GET** | `/holidays` | Ambil daftar hari libur | `page`, `per_page`, `search`, `year` |
| **POST** | `/holidays` | Tambah hari libur baru | `{"date": "2026-08-17", "name": "HUT RI"}` |
| **GET** | `/holidays/{id}` | Detail hari libur | *None* |
| **PUT** | `/holidays/{id}` | Perbarui hari libur | `{"date": "2026-08-17", "name": "Hari Kemerdekaan"}` |
| **DELETE**| `/holidays/{id}` | Hapus hari libur | *None* |

### 4. Laporan Bulanan (`/api/reports`)

Endpoint fleksibel untuk menyajikan data rekapitulasi bulanan dalam format JSON.

* **Laporan Ringkasan Semua Karyawan** `GET /api/reports/monthly?month=2026-06`  
    *Fungsi: Menampilkan total ringkasan jumlah Present, Sick, Leave, Permission, Absent dari seluruh pegawai aktif.*

* **Laporan Detail Individu Karyawan** `GET /api/reports/monthly?month=2026-06&employee_id=2`  
    *Fungsi: Menampilkan total ringkasan bulan tersebut disertai log detail tanggal demi tanggal dari pegawai yang bersangkutan.*

> 🌐 **Contoh URL Pengujian Lokal:** > `GET /api/reports/monthly?month=2026-06&employee_id=2`

---

## 🛠️ Cara Menjalankan Seeder Data Pegawai

Untuk mengisi database dengan data dummy awal (30+ pegawai acak), jalankan perintah berikut pada terminal:

```bash
# Untuk migrasi ulang dari awal beserta seeder-nya
php artisan migrate:fresh --seed

# Atau jika hanya ingin mengisi data tanpa menghapus DB
php artisan db:seed
