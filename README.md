# ☁️ Self-Hosted Multi-Device Backup Hub (Nextcloud + S3 R2/B2)

Proyek ini adalah arsitektur *self-hosted* pengganti Google Drive yang mengintegrasikan Nextcloud dengan Object Storage berskala besar (Cloudflare R2 / Backblaze B2). 

---

## 1. DAFTAR AKUN & PRASYARAT YANG DIPERLUKAN

* **GitHub**: Untuk menyimpan repositori ini.
* **Cloudflare (R2) / Backblaze (B2)**: Sebagai Object Storage. Catat *Access Key*, *Secret Key*, serta *Endpoint URL*.
* **Cloudflare (Zero Trust Tunnel) / Ngrok**: Untuk mengekspos *localhost* secara aman menggunakan HTTPS.
* **VPS Provider**: OS Ubuntu 22.04+ untuk *production* 24/7 (Opsional jika hanya ingin coba di Colab).

---

## 2. EKOSISTEM APLIKASI TAMBAHAN YANG DIDUKUNG

| Platform | Aplikasi | Fungsi Utama |
| :--- | :--- | :--- |
| **Windows** | VS Code, Git, Nextcloud Desktop | Manajemen *remote* dan sinkronisasi lokal. |
| **Ubuntu/Linux**| Docker, Docker Compose, Rclone | Menjalankan *container* dan migrasi data server. |
| **Android** | FolderSync, Swift Backup (Root) | Sinkronisasi folder spesifik & *backup system apps*. |
| | Termux, Nextcloud App | *Remote server* (SSH) & akses *file manager*. |

---

## 3. PANDUAN INSTALASI & SKEMA PENGGUNAAN MULTI-DEVICE

### A. Deploy di Google Colab (Eksperimen / Uji Coba)
Buka file `colab_tester.ipynb` di Google Colab. Jalankan *cell* satu per satu. Sistem akan membuat server sementara menggunakan Ngrok.

### B. Deploy di VPS Ubuntu Asli (Produksi 24/7)
1. Akses VPS via SSH.
2. Instal prasyarat: `sudo apt update && sudo apt install docker.io docker-compose git -y`.
3. Jalankan: `git clone <URL_REPO_ANDA> && cd nextcloud-backup-master`.
4. Salin template lingkungan: `cp .env.example .env` dan edit isinya.
5. Eksekusi: `docker-compose up -d`.

### C. Skema Alur Penggunaan
**HP Android (FolderSync/Swift Backup)** -> *Upload via WebDAV* -> **Nextcloud (VPS/Colab)** -> *Direct Stream* -> **S3 (Cloudflare R2/B2)**. Data tidak menetap dan memenuhi disk VPS.

---

## 4. TROUBLESHOOTING & MANAJEMEN ERROR

1. **Typo / Salah Ketik pada Konfigurasi:** Jalankan `docker compose config` sebelum mengeksekusi sistem untuk memvalidasi sintaks dan menghindari *crash*.
2. **Error "Connection Refused":** Jika di Colab, sesi Anda mungkin mati dan harus diulang. Jika di VPS, cek log dengan `docker logs nextcloud_tunnel`.
3. **Error "S3 Bucket Not Found":** Buka file `.env`. Pastikan **Endpoint URL** tidak menyertakan `https://` (karena sudah di-handle oleh *config.php*) dan cek penulisan `S3_REGION` (Gunakan `auto` untuk Cloudflare R2).

## 5. 🌐 ALTERNATIF S3 OBJECT STORAGE (PENYEDIA UTAMA)
Sistem ini menggunakan arsitektur S3-Compatible. Anda bebas memilih salah satu dari layanan berikut sebagai pusat penyimpanan data Anda. Berikut adalah perbandingan dan format data yang dibutuhkan:

| Penyedia | Karakteristik & Biaya | Format `S3_HOSTNAME` (Endpoint) | Format `S3_REGION` |
| :--- | :--- | :--- | :--- |
| **Cloudflare R2** | **Rekomendasi (Gratis 10GB/Bulan).** Tanpa biaya *egress* (penarikan data). | `<ACCOUNT_ID>.r2.cloudflarestorage.com` | `auto` |
| **Backblaze B2** | **Gratis 10GB.** Sangat murah untuk penyimpanan skala TB. | `s3.<REGION>.backblazeb2.com` (misal: `s3.us-west-004...`) | Sesuai konsol (misal: `us-west-004`) |
| **Wasabi** | **Hot Storage Tercepat.** $6.99/TB/Bulan, tanpa biaya *egress*. Cocok untuk *backup* aktif. | `s3.<REGION>.wasabisys.com` (misal: `s3.ap-northeast-1...`) | Sesuai lokasi (misal: `ap-northeast-1`) |
| **DigitalOcean Spaces** | UI sangat ramah pemula. Flat $5/bulan untuk 250GB. | `<REGION>.digitaloceanspaces.com` (misal: `sgp1.digitaloceanspaces.com`) | Sesuai server (misal: `sgp1`) |
| **Amazon S3 (AWS)** | *Industry standard*. Fitur paling lengkap, tapi perhatikan biaya *bandwidth*. | `s3.<REGION>.amazonaws.com` | Sesuai server (misal: `ap-southeast-1`) |

### 🔑 DATA KREDENSIAL YANG WAJIB DISIAPKAN (S3)
Apa pun penyedia yang Anda pilih dari tabel di atas, Anda **wajib** membuat *Bucket* di *dashboard* mereka dan mencatat 5 data berikut untuk dimasukkan ke dalam file `.env` (atau saat ditanya oleh skrip Google Colab):

5.1. **S3 Bucket Name:** Nama wadah yang Anda buat (misal: `my-nextcloud-backup-123`). Nama ini harus unik.
5.2. **S3 Access Key:** Kombinasi huruf/angka publik (berfungsi seperti *Username* API).
5.3. **S3 Secret Key:** Kombinasi huruf/angka rahasia yang panjang (berfungsi seperti *Password* API).
5.4. **S3 Hostname:** Alamat *Endpoint* API untuk penyedia tersebut (lihat tabel di atas. **PENTING:** Jangan masukkan awalan `https://`).
5.5. **S3 Region:** Kode wilayah *server* tempat *bucket* Anda berada.
