🧩 CSS Grid dan Implementasinya
📌 Abstrak
CSS Grid adalah sistem layout dua dimensi dalam CSS yang memungkinkan pengembang membangun antarmuka web yang kompleks,
fleksibel, dan responsif dengan efisiensi tinggi.
Tidak seperti teknik lama seperti float atau Flexbox (yang bersifat satu dimensi),
CSS Grid memberikan kontrol penuh atasbaris dan kolom secara simultan.
Artikel ini mengupas pengertian CSS Grid, fitur-fiturnya, keunggulan,
cara implementasi, serta contoh penggunaan nyata dalam proyek web modern.

🔍 Pendahuluan
Dalam pengembangan antarmuka web, penataan elemen menjadi tantangan utama. Sebelum adanya CSS Grid,
teknik seperti Flexbox, float, dan positioning sering digunakan,
namun memiliki keterbatasan dalam struktur dua dimensi.
CSS Grid hadir sebagai solusi yang powerful untuk menyusun elemen dalam baris dan kolom dengan lebih sederhana 
dan terstruktur.

⚙ Apa Itu CSS Grid?
CSS Grid Layout adalah modul dalam CSS yang dirancang untuk menangani layout dua dimensi
(baik baris maupun kolom) secara bersamaan.
Elemen grid terdiri dari grid container (elemen induk) dan grid items (anak langsung dari container).

🔑 Fitur Utama
Layout dua dimensi (baris dan kolom)

Penempatan elemen yang fleksibel

Dukungan named grid areas

Responsif dengan media queries

Integrasi mudah dengan Flexbox

✨ Mengapa Menggunakan CSS Grid?
Layout kompleks jadi lebih sederhana

Struktur HTML tetap bersih

Cocok untuk responsif design

Kode lebih ringkas dan mudah dikelola

Mendukung deklarasi grid-area yang deskriptif

📦 Cara Menggunakan CSS Grid
🔹 Contoh Sederhana
css
Copy
Edit
.container {
  display: grid;
  grid-template-columns: 1fr 2fr 1fr;
  gap: 10px;
}
html
Copy
Edit
<div class="container">
  <div>Item 1</div>
  <div>Item 2</div>
  <div>Item 3</div>
</div>
🧾 Struktur Folder
css
Copy
Edit
css-grid-project/
├── index.html
├── style.css
💡 Contoh Implementasi Lengkap
Berikut contoh layout website sederhana menggunakan CSS Grid:

html
Copy
Edit
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contoh CSS Grid</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      margin: 0;
      font-family: sans-serif;
    }
    .grid-container {
      display: grid;
      grid-template-areas: 
        "header header"
        "sidebar main"
        "footer footer";
      grid-template-columns: 1fr 3fr;
      grid-template-rows: auto 1fr auto;
      height: 100vh;
      gap: 10px;
      padding: 10px;
    }
    .header   { grid-area: header; background: #4f46e5; color: white; padding: 1em; }
    .sidebar  { grid-area: sidebar; background: #e0e7ff; padding: 1em; }
    .main     { grid-area: main; background: #f1f5f9; padding: 1em; }
    .footer   { grid-area: footer; background: #c7d2fe; padding: 1em; text-align: center; }
  </style>
</head>
<body>

  <div class="grid-container">
    <div class="header">Header</div>
    <div class="sidebar">Sidebar</div>
    <div class="main">Main Content</div>
    <div class="footer">Footer</div>
  </div>

</body>
</html>
🔁 Responsif dengan Media Query
css
Copy
Edit
@media (max-width: 768px) {
  .grid-container {
    grid-template-areas: 
      "header"
      "main"
      "sidebar"
      "footer";
    grid-template-columns: 1fr;
  }
}
🧾 Penutup
CSS Grid memberi cara baru yang lebih efisien dan intuitif dalam menyusun layout antarmuka. 
Berkat fleksibilitas dan kekuatannya, CSS Grid menjadi pilihan ideal untuk proyek web modern yang membutuhkan kontrol
layout dua dimensi secara penuh. Dengan memadukan Grid dan Flexbox, pengembang dapat mencapai tata letak yang optimal,
responsif, dan mudah di-maintain.

🔗 Referensi
Dokumentasi CSS Grid - MDN Web Docs

CSS Tricks - A Complete Guide to Grid

W3Schools - CSS Grid


