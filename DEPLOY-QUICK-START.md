# 🚀 Quick Deploy Guide - Inventaris Visualisasi

## Frontend (Vercel) ✅ DONE

Sudah berhasil deploy? Skip ke Backend.

## Backend (Railway) ⚠️ FIXING CONNECTION REFUSED

### 🔴 ERROR: SQLSTATE[HY000] [2002] Connection refused

Ini terjadi karena Railway **tidak bisa connect ke database**!

---

## ⚡ Fix dalam 5 Langkah

### Step 1: Pastikan MySQL Service Ada

Railway Dashboard > Your Project:
- ✅ **Ada MySQL service?** → Lanjut Step 2
- ❌ **Tidak ada?** → Klik **New** > **Database** > **Add MySQL**

Tunggu MySQL status = **Active** (≈30 detik)

---

### Step 2: ⚠️ PENTING - Link MySQL ke Backend

**INI PENYEBAB ERROR ANDA!**

1. Railway Dashboard > Klik **backend service** (Laravel)
2. Klik tab **Variables** 
3. Klik **New Variable** dropdown
4. **Toggle "Reference" ON** (ada switch Reference)
5. Select **MySQL** dari dropdown service
6. **✅ Check ALL variables:**
   ```
   ✅ MYSQLHOST
   ✅ MYSQLPORT  
   ✅ MYSQLDATABASE
   ✅ MYSQLUSER
   ✅ MYSQLPASSWORD
   ```
7. Click **Add**

**Screenshot guide:**
```
Backend Service > Variables > New Variable
  ↓
[+ New Variable ▼]
  ├─ Variable Name/Value (default selected)
  └─ Reference (toggle ini ON!) 👈 PENTING
      ↓
    Select a service: [MySQL ▼]  👈 PILIH MYSQL
      ↓
    ✅ MYSQLHOST
    ✅ MYSQLPORT
    ✅ MYSQLDATABASE
    ✅ MYSQLUSER
    ✅ MYSQLPASSWORD
      ↓
    [Add] 👈 KLIK
```

---

### Step 3: Verify Variables Ter-inject

Scroll di **Variables** tab, pastikan ada:

```
MYSQLHOST         = mysql.railway.internal (atau IP)
MYSQLPORT         = 3306
MYSQLDATABASE     = railway  
MYSQLUSER         = root
MYSQLPASSWORD     = (random string panjang)
```

**❌ Jika tidak ada** = Link gagal, ulangi Step 2!

---

### Step 4: Set Required ENV Variables

Masih di **Variables** tab, klik **Raw Editor** (toggle kanan atas).

**Paste ini:**

```bash
APP_NAME=Inventaris Visualisasi
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
LOG_LEVEL=error

CORS_ALLOWED_ORIGINS=https://your-vercel-app.vercel.app
```

**⚠️ JANGAN tambah `DB_HOST=127.0.0.1`!**
(Railway auto-inject dari MySQL Reference)

**Save**

---

### Step 5: Generate APP_KEY

**Windows CMD:**
```cmd
cd c:\inventaris-visualisasi-backup\backend
php artisan key:generate --show
```

**Copy output** (contoh: `base64:abc123...`)

**Railway Dashboard** > Variables > Edit `APP_KEY`:
```
APP_KEY=base64:PASTE_DISINI
```

**Save**

---

### Step 6: Redeploy

Railway Dashboard > Backend > **Deployments** > **Redeploy**

**Atau dari Git:**
```cmd
git commit --allow-empty -m "Trigger redeploy"  
git push origin main
```

Tunggu 2-3 menit...

---

### Step 7: ✅ Verify Success

**Check logs:**
Railway Dashboard > Deployments > **View Logs**

**Look for:**
```
✅ Running migrations...
✅ Migration table created successfully
✅ Migrated: ...
✅ php artisan serve --host=0.0.0.0
```

**Test endpoint:**
```bash
curl https://your-backend.railway.app/api/health
```

**Expected:**
```json
{"status":"ok","timestamp":"...","database":"connected"}
```

✅ **BERHASIL!**

---

## 🆘 Masih Error?

### ❌ Still "Connection refused"?

**Kemungkinan:**

1. **MySQL reference belum di-add**
   - Check Variables tab ada `MYSQLHOST` atau tidak?
   - Tidak ada? Ulangi Step 2!

2. **DB_HOST hardcoded ke 127.0.0.1**
   - Variables tab > Cari `DB_HOST`
   - Ada dan valuenya `127.0.0.1`? **DELETE!**
   - Biarkan Railway inject dari `MYSQLHOST`

3. **Config cache issue**
   - Redeploy sekali lagi (kadang cache stuck)

**Baca troubleshooting lengkap:** `/RAILWAY-FIX-DATABASE.md`

---

## 📋 Checklist Debug

- [ ] MySQL service status = Active
- [ ] Backend Variables ada `MYSQLHOST`, `MYSQLPORT`, dll (5 variables)  
- [ ] `APP_KEY` sudah diset (bukan kosong)
- [ ] `DB_HOST` **TIDAK** ada atau tidak hardcoded
- [ ] Sudah redeploy setelah link MySQL
- [ ] Build logs sukses (no errors)

---

### Update Frontend setelah Backend OK

Vercel Dashboard > Project > Settings > Environment Variables:

```
NEXT_PUBLIC_API_URL=https://your-backend.railway.app/api
```

**Redeploy frontend!**

---

## 📚 Dokumentasi Lengkap

- **Connection Error Fix:** `/RAILWAY-FIX-DATABASE.md` 👈 BACA INI!
- **Full Deploy Guide:** `/DEPLOYMENT.md`
- **Railway Troubleshooting:** `/backend/RAILWAY.md`

Good luck! 🚀
