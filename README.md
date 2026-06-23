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
