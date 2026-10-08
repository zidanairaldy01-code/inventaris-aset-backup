# 📦 Supabase Storage Setup Guide

## ✅ Keuntungan Supabase Storage

- ✅ **GRATIS 1GB storage** (permanent, bukan trial)
- ✅ **2GB bandwidth/bulan gratis**
- ✅ **Tidak perlu kartu kredit**
- ✅ **CDN global** (cepat di mana saja)
- ✅ **Public URL langsung**
- ✅ **Compatible dengan Railway + Vercel**

---

## 🚀 Setup Supabase Storage

### Step 1: Buat Akun Supabase

1. Buka https://supabase.com
2. Klik **"Start your project"**
3. Sign in dengan **GitHub** atau email
4. **GRATIS** - tidak perlu kartu kredit!

### Step 2: Buat Project

1. Klik **"New Project"**
2. Pilih **Organization** (atau buat baru)
3. Isi form:
   - **Project Name**: `inventaris-app`
   - **Database Password**: (generate atau buat sendiri - **simpan ini**)
   - **Region**: **Southeast Asia (Singapore)** - terdekat dengan Indonesia
   - **Pricing Plan**: **Free** (sudah ter-select)
4. Klik **"Create new project"**
5. Tunggu ~2 menit sampai setup selesai

### Step 3: Buat Storage Bucket

1. Di sidebar kiri, klik **"Storage"** (icon folder)
2. Klik **"Create a new bucket"**
3. Isi form:
   - **Name**: `photos`
   - **Public bucket**: ✅ **CENTANG INI** (penting agar foto bisa diakses publik)
   - **File size limit**: Biarkan default (50MB)
   - **Allowed MIME types**: Biarkan kosong (allow all)
4. Klik **"Create bucket"**

### Step 4: Set Bucket Policies (Agar Public)

1. Klik bucket `photos` yang baru dibuat
2. Klik tab **"Policies"**
3. Klik **"New Policy"**
4. Pilih **"For full customization"**
5. Isi:
   - **Policy name**: `Public Access`
   - **Policy definition**:
     ```sql
     CREATE POLICY "Public Access"
     ON storage.objects FOR SELECT
     USING ( bucket_id = 'photos' );
     ```
6. Atau lebih mudah, klik **"Add policy from template"** → **"Anyone can select"**

### Step 5: Get API Credentials

1. Di sidebar, klik **"Settings"** (icon gear di bawah)
2. Klik **"API"**
3. **Copy dan simpan**:
   - ✅ **Project URL**: `https://xxxxxx.supabase.co`
   - ✅ **anon public key**: String panjang mulai `eyJh...`

**PENTING**: Jangan share Secret Key! Yang dipakai cukup **anon/public key**.

---

## ⚙️ Konfigurasi Laravel

### 1. Update `.env`

Ganti ini di file `.env`:

```env
FILESYSTEM_DISK=supabase

SUPABASE_URL=https://your-project-id.supabase.co
SUPABASE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.ey...
SUPABASE_BUCKET=photos
```

**Ganti dengan**:
- `your-project-id`: ID project Anda (lihat di URL dashboard)
- Key panjang: anon/public key dari step 5

### 2. Test Upload

Jalankan di terminal:

```bash
php artisan tinker
```

Kemudian test:

```php
$file = new Illuminate\Http\UploadedFile(
    storage_path('app/test.jpg'), // buat file dummy dulu
    'test.jpg',
    'image/jpeg',
    null,
    true
);

$path = \App\Helpers\StorageHelper::store($file, 'sarana-prasarana');
$url = \App\Helpers\StorageHelper::url($path);
echo $url;
```

Jika berhasil, akan print URL seperti:
```
https://xxxxxx.supabase.co/storage/v1/object/public/photos/sarana-prasarana/123456_abc.jpg
```

Buka URL di browser, jika foto muncul = **BERHASIL!** ✅

---

## 🚢 Deploy ke Railway

### Environment Variables di Railway:

```env
APP_URL=https://your-backend.railway.app
FILESYSTEM_DISK=supabase
SUPABASE_URL=https://your-project.supabase.co
SUPABASE_KEY=your_anon_key
SUPABASE_BUCKET=photos
```

---

## 🌐 Deploy Frontend ke Vercel

Tidak perlu konfigurasi khusus di Vercel! Frontend sudah otomatis terima URL foto dari backend API.

Environment variables Vercel:

```env
NEXT_PUBLIC_API_URL=https://your-backend.railway.app
```

---

## 📊 Monitoring Usage

Cek usage di:
1. Dashboard Supabase
2. Sidebar kiri → **"Storage"**
3. Tab **"Usage"**

Akan terlihat:
- Storage used (max 1GB free)
- Bandwidth used (max 2GB/month free)

---

## 🔄 Switching Storage (Local ↔ Supabase)

### Local Development (pakai storage lokal):

```env
FILESYSTEM_DISK=public
```

### Production (pakai Supabase):

```env
FILESYSTEM_DISK=supabase
```

Helper class `StorageHelper` otomatis handle switching!

---

## 💡 Tips & Tricks

### 1. Optimize Foto Sebelum Upload

Di frontend (Next.js), compress foto sebelum upload:

```bash
npm install browser-image-compression
```

### 2. Lazy Loading Foto

Gunakan Next.js Image component:

```tsx
import Image from 'next/image'

<Image 
  src={foto.url_foto} 
  alt="Foto" 
  width={300} 
  height={300}
  loading="lazy"
/>
```

### 3. Thumbnail Generation

Supabase punya fitur image transformation (berbayar). Alternatif: generate thumbnail di backend saat upload.

---

## ❓ Troubleshooting

### Error: "Failed to upload to Supabase"

**Penyebab**: Bucket tidak public atau policy belum di-set.

**Solusi**:
1. Buka bucket di dashboard
2. Tab "Policies"
3. Add policy "Anyone can select"

### Error: "Network error" / Timeout

**Penyebab**: URL atau Key salah.

**Solusi**: Double check `SUPABASE_URL` dan `SUPABASE_KEY` di `.env`.

### Foto tidak muncul di browser

**Penyebab**: Bucket belum public atau path salah.

**Solusi**:
1. Test URL langsung di browser
2. Pastikan format URL: `https://xxx.supabase.co/storage/v1/object/public/photos/...`
3. Cek di dashboard > Storage > photos > pastikan file ada

### Error: "Storage quota exceeded"

**Penyebab**: Sudah pakai >1GB.

**Solusi**:
1. Hapus foto lama yang tidak terpakai
2. Atau upgrade ke Pro plan ($25/month untuk 100GB)

---

## 📈 Upgrade Path (Jika Perlu)

Jika aplikasi berkembang dan perlu >1GB:

| Plan | Storage | Bandwidth | Harga |
|------|---------|-----------|-------|
| Free | 1GB | 2GB/month | $0 |
| Pro | 100GB | 200GB/month | $25/month |

Untuk sekolah dengan 1000+ foto, mungkin perlu upgrade ke Pro di tahun ke-2 atau ke-3.

---

## 🎯 Kesimpulan

✅ Setup Supabase: ~10 menit  
✅ Gratis 1GB permanent  
✅ Compatible dengan Railway + Vercel  
✅ CDN global (cepat)  
✅ Tidak perlu kartu kredit  

**Perfect untuk aplikasi inventaris sekolah!** 🚀

---

## 📚 Referensi

- [Supabase Storage Docs](https://supabase.com/docs/guides/storage)
- [Supabase Pricing](https://supabase.com/pricing)
- [Storage API Reference](https://supabase.com/docs/reference/javascript/storage-from-upload)
