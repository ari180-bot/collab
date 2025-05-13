  1. Pengertian Git dan GitHub
     
  🔹 Apa itu Git?
  Git adalah sistem kontrol versi terdistribusi yang digunakan untuk melacak perubahan dalam kode program. Dengan Git, kamu bisa:
  
  Mengembalikan kode ke versi sebelumnya.
  
  Mencatat setiap perubahan.
  
  Bekerja bersama tim tanpa konflik.
  
  🔹 Apa itu GitHub?
  GitHub adalah layanan hosting untuk menyimpan repository Git secara online. GitHub memudahkan:
  
  Kolaborasi tim.
  
  Review kode (pull request).
  
  Dokumentasi proyek.
  
  Deploy website (GitHub Pages).
  
  2. Manfaat Menggunakan Git dan GitHub
  Menghindari kehilangan data saat ada kesalahan.
  
  Melihat dan mengelola riwayat perubahan kode.
  
  Kolaborasi dengan tim secara efisien.
  
  Menyimpan proyek secara online (backup).
  
  Mempermudah kontribusi open source.


  3. Istilah Penting dalam Git

  | Istilah               | Penjelasan                                           |
  | --------------------- | ---------------------------------------------------- |
  | **Repository (repo)** | Tempat penyimpanan proyek Git.                       |
  | **Commit**            | Menyimpan snapshot (rekaman) perubahan.              |
  | **Branch**            | Cabang pengembangan kode paralel.                    |
  | **Merge**             | Menggabungkan perubahan dari satu branch ke lainnya. |
  | **Pull**              | Mengambil update dari remote repository.             |
  | **Push**              | Mengirim commit ke remote repository.                |
  | **Clone**             | Menyalin repository dari GitHub ke lokal.            |


  4. Alur Kerja Dasar Git

    
    1. git init                     → Buat repository lokal
    2. git add .                    → Tambahkan file ke staging
    3. git commit -m "pesan"        → Simpan perubahan ke repository
    4. git remote add origin <url>  → Hubungkan ke GitHub
    5. git push -u origin main      → Upload ke GitHub
    6. git pull                     → Ambil perubahan terbaru dari GitHub

5. Perintah Dasar Git
   
| Perintah                | Fungsi                           |
| ----------------------- | -------------------------------- |
| `git init`              | Membuat repo Git di folder lokal |
| `git clone <url>`       | Menyalin repo dari GitHub        |
| `git status`            | Melihat status file              |
| `git add <file>` / `.`  | Menambahkan file ke staging      |
| `git commit -m "pesan"` | Menyimpan perubahan              |
| `git push`              | Upload ke GitHub                 |
| `git pull`              | Download update dari GitHub      |
| `git branch`            | Lihat atau buat cabang           |
| `git checkout <nama>`   | Pindah branch                    |
| `git merge <nama>`      | Gabung cabang ke cabang aktif    |
| `git log`               | Lihat riwayat commit             |


6. Fitur Penting GitHub
   
| Fitur                 | Keterangan                               |
| --------------------- | ---------------------------------------- |
| **Repository**        | Tempat proyek disimpan.                  |
| **Fork**              | Menyalin repo orang lain.                |
| **Pull Request (PR)** | Ajukan perubahan ke proyek utama.        |
| **Issues**            | Lapor bug atau diskusikan fitur.         |
| **GitHub Pages**      | Hosting gratis untuk website statis.     |
| **Actions**           | Otomatisasi seperti testing atau deploy. |


     
