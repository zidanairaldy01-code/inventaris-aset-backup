# 🔴 FIX: SQLSTATE[HY000] [2002] Connection refused

Error ini terjadi karena **Railway tidak bisa connect ke database**.

---

## ⚡ Solusi Cepat (5 Menit)

### Step 1: Cek Apakah MySQL Service Ada

Railway Dashboard > Project > Lihat services:
- ✅ Ada **MySQL** service? → Lanjut ke Step 2
- ❌ Tidak ada? → Lanjut ke Step 1A

#### Step 1A: Buat MySQL Service (jika belum ada)

1. Railway Dashboard > **New** (tombol kanan atas)
2. Pilih **Database** > **Add MySQL**
3. Tunggu ≈ 30 detik sampai status **Active**

---

### Step 2: Link MySQL ke Backend Service

**INI LANGKAH PALING PENTING!**

1. Railway Dashboard > Klik **backend service** (bukan MySQL)
2. Klik tab **Variables**
3. Klik **New Variable** > **Add Reference**
4. Pilih **MySQL** service dari dropdown
5. **CHECK SEMUA** variables ini:
   - ✅ MYSQLHOST
   - ✅ MYSQLPORT
   - ✅ MYSQLDATABASE
   - ✅ MYSQLUSER
   - ✅ MYSQLPASSWORD
6. Klik **Add** atau **Save**

**Screenshot lokasi:**
```
Variables tab > New Variable > Reference (toggle) > Select MySQL service
```

---

### Step 3: Verify Variables

Masih di **Variables** tab backend service, scroll dan pastikan ada:

```
MYSQLHOST = mysql.railway.internal (atau IP)
MYSQLPORT = 3306
MYSQLDATABASE = railway
MYSQLUSER = root
MYSQLPASSWORD = (random string)
```

**Jika tidak ada**, ulangi Step 2!

---

### Step 4: Generate APP_KEY (jika belum)

**Local terminal:**
```cmd
cd c:\inventaris-visualisasi-backup\backend
php artisan key:generate --show
```

**Output contoh:**
```
base64:abcdef1234567890ABCDEFGHIJKLMNOP=
```

Copy output ini.

---

### Step 5: Set Minimal ENV Variables

Railway Dashboard > Backend Service > **Variables** tab

**Klik "Raw Editor"** (toggle di kanan atas), paste ini:

```bash
APP_NAME=Inventaris Visualisasi
APP_ENV=production
APP_KEY=base64:PASTE_HASIL_STEP_4_DISINI
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta

# Database akan otomatis dari MySQL Reference (Step 2)
# Tapi pastikan variables ini TIDAK hardcoded ke 127.0.0.1

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
LOG_LEVEL=error

# CORS - Ganti dengan URL Vercel Anda
CORS_ALLOWED_ORIGINS=https://your-app.vercel.app
```

**PENTING:** 
- Jangan tambah `DB_HOST=127.0.0.1` manual!
- Railway akan auto-inject `MYSQLHOST`, `MYSQLPORT`, dll dari Reference
- Config `database.php` sudah di-setup untuk fallback ke Railway variables

**Save!**

---

### Step 6: Redeploy

1. Railway Dashboard > Backend Service
2. Klik **Deployments** tab
3. Klik **Deploy** button (atau klik 3 dots > Redeploy)

**Atau trigger redeploy dari Git:**
```cmd
git commit --allow-empty -m "Trigger redeploy"
git push origin main
```

Tunggu ≈ 2-3 menit untuk build & deploy.

---

### Step 7: Monitor Logs

Railway Dashboard > Deployments > **View Logs**

**Look for:**
```
✅ "Running migrations..."
✅ "Migration table created successfully"
✅ "php artisan serve --host=0.0.0.0 --port=..."
```

**Jika masih error:**
```
❌ "SQLSTATE[HY000] [2002] Connection refused"
```

→ **Variables belum ter-link!** Ulangi Step 2.

---

### Step 8: Test Health Endpoint

**Setelah deploy sukses:**
```bash
curl https://your-backend.railway.app/api/health
```

**Expected response:**
```json
{
  "status": "ok",
  "timestamp": "2026-10-08T18:00:00+07:00",
  "database": "connected"
}
```

✅ **BERHASIL!**

---

## 🔍 Troubleshooting

### ❌ Masih "Connection refused" setelah link?

**Check:**
1. **Apakah MySQL service status Active?**
   - Railway Dashboard > MySQL service > Check status
   
2. **Apakah backend bisa "see" MySQL?**
   - Backend Variables tab > Pastikan ada `MYSQLHOST`, `MYSQLPORT`, etc.
   - **Jika tidak ada**, link belum berhasil!

3. **Apakah ENV variables benar?**
   ```bash
   # SALAH - Jangan hardcode:
   DB_HOST=127.0.0.1
   
   # BENAR - Biarkan kosong atau hapus:
   # (Railway auto-inject dari Reference)
   ```

4. **Restart deployment:**
   - Deployments > 3 dots > **Restart**

---

### ❌ MySQL service tidak bisa dibuat?

**Railway free tier limits:**
- Max 2 services per project

**Solusi:**
- Hapus service yang tidak perlu, atau
- Upgrade ke Railway Pro ($5/month)

**Alternatif:** Pakai database external:
- PlanetScale (free tier)
- Supabase Postgres (free tier)
- Neon Postgres (free tier)

---

### ❌ "Table doesn't exist" error?

Migration belum jalan. Check:

1. **Logs:** Apakah `php artisan migrate --force` berhasil?
2. **Database:** Railway MySQL > **Data** tab > Check tables

**Manual run migration:**
- Railway Dashboard > Backend Service
- Klik **Settings** > **Deploy Logs**
- Pastikan command migrate berjalan

---

### ❌ Cara manual connect ke Railway MySQL?

**Get credentials:**
Railway Dashboard > MySQL service > **Connect** tab

**Connection string:**
```
mysql://root:PASSWORD@HOST:PORT/railway
```

**Via TablePlus / MySQL Workbench:**
- Host: `(dari MYSQLHOST)`
- Port: `(dari MYSQLPORT)`
- User: `root`
- Password: `(dari MYSQLPASSWORD)`
- Database: `railway`

---

## 🎯 Checklist Debug

Pastikan semua centang:

- [ ] MySQL service exists dan status **Active**
- [ ] Backend service **Variables** tab ada `MYSQLHOST`, `MYSQLPORT`, dll
- [ ] Reference ke MySQL service sudah di-add
- [ ] `APP_KEY` sudah di-set
- [ ] `DB_HOST` **TIDAK** hardcoded ke `127.0.0.1`
- [ ] Sudah redeploy setelah set variables
- [ ] Build logs tidak ada error
- [ ] Deploy logs tidak ada error "Connection refused"

---

## 📸 Visual Guide

**Lokasi Link Variables:**

```
Railway Dashboard
  └─ Your Project
      ├─ MySQL service (database)
      └─ Backend service (Laravel)
          └─ Variables tab  👈 KLIK INI
              └─ New Variable
                  └─ Reference (toggle ON)  👈 PILIH MYSQL
                      └─ Check all:
                          ✅ MYSQLHOST
                          ✅ MYSQLPORT
                          ✅ MYSQLDATABASE
                          ✅ MYSQLUSER
                          ✅ MYSQLPASSWORD
```

---

Setelah semua step ini, backend Anda **HARUS** bisa connect ke database! 🚀

Masih error? Screenshot error logs dan DM/share! 🆘
