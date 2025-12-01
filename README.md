# SuryaCMS – Laravel CMS Package

SuryaCMS adalah package CMS modular berbasis Laravel yang dirancang untuk memudahkan pembuatan website dinamis, manajemen konten, dan konfigurasi halaman tanpa membangun fitur CMS dari awal. Mendukung Laravel 11 dan 12 serta terintegrasi dengan Livewire 3.

---

## ✨ Fitur Utama

- Modular & extensible
- Manajemen Post & Page
- Manajemen Kategori
- Gallery & Media Manager
- Menu Builder (drag & drop)
- System Settings lengkap
- Contact Form + Message Storage
- YouTube Embed Scraper
- Backup Module (PDF, images, exports, full storage)
- Livewire 3 UI interaktif
- Multi-theme homepage builder
- CRUD generator-friendly

---

## 📦 Instalasi

### 1. Install package via Composer
```bash
composer require uiaciel/suryacms
```

### 2. Publish aset (opsional)
```bash
php artisan vendor:publish --provider="Uiaciel\SuryaCms\SuryaCmsServiceProvider"
```

### 3. Jalankan migration
```bash
php artisan migrate
```

---

## 📁 Struktur Package

```
suryacms/
├── config/
├── resources/
│   ├── views/
│   ├── lang/
├── routes/
│   ├── web.php
├── src/
│   ├── Http/
│   ├── Models/
│   ├── SuryaCmsServiceProvider.php
│   ├── helpers.php
└── ...
```

---

## 🧩 Kompatibilitas

| Dependency | Versi |
|-----------|--------|
| PHP       | >= 8.1 |
| Laravel   | ^11.0 / ^12.0 |
| Livewire  | ^3.0 |

---

## 📚 Dokumentasi

Dokumentasi lengkap sedang dikembangkan.  
Gunakan struktur dan fitur default package sebagai acuan sementara.

---

## 🤝 Kontribusi

Pull request, issue, atau saran sangat terbuka.  
Silakan fork repository ini, lakukan perubahan, lalu kirimkan PR.

---

## 📄 Lisensi

SuryaCMS dirilis di bawah lisensi MIT.

---

## 👨‍💻 Pengembang

Dibuat oleh **Uiaciel**, developer Laravel, desainer, dan founder *Kaoskeren.id*.

GitHub: https://github.com/uiaciel
