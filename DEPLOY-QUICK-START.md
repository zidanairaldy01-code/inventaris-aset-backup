# 🚀 Quick Deploy Guide - Inventaris Visualisasi

## Frontend (Vercel) ✅ DONE

Sudah berhasil deploy? Skip ke Backend.

## Backend (Railway) ⚠️ FIXING CRASH

### Step 1: Setup MySQL di Railway

1. Buka https://railway.app/
2. Klik **New Project** > **Provision MySQL**
3. Tunggu MySQL ready (≈ 30 detik)

### Step 2: Deploy Backend

1. **New** > **Deploy from GitHub repo**
2. Pilih repository ini
3. ⚠️ **PENTING:** Set **Root Directory** = `backend`
4. Klik **Deploy** (biarkan dulu, akan crash)

### Step 3: Link Database

1. Klik backend service
2. Tab **Variables**
3. Klik **Add Reference** > Pilih MySQL service
4. Check all MySQL variables ✓
5. **Save**

### Step 4: Generate APP_KEY

**Windows:**
```cmd
cd backend
php artisan key:generate --show
```

**Atau double-click:**
```
backend/generate-key.bat
```

Copy output (contoh: `base64:abc123...`)

### Step 5: Set Environment Variables

Railway Dashboard > Backend Service > **Variables** > **Raw Editor**

**Paste ini, ganti yang perlu:**

```bash
APP_NAME=Inventaris Visualisasi
APP_ENV=production
APP_KEY=base64:PASTE_OUTPUT_DARI_STEP_4
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=${{MYSQLHOST}}
DB_PORT=${{MYSQLPORT}}
DB_DATABASE=${{MYSQLDATABASE}}
DB_USERNAME=${{MYSQLUSER}}
DB_PASSWORD=${{MYSQLPASSWORD}}

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
LOG_LEVEL=error

CORS_ALLOWED_ORIGINS=https://your-vercel-app.vercel.app

# Cloudflare R2 (opsional, bisa diisi nanti)
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_BUCKET=inventaris-app
AWS_ENDPOINT=https://xxxxx.r2.cloudflarestorage.com
AWS_URL=https://pub-xxxxx.r2.dev

# Supabase (opsional)
SUPABASE_URL=https://xxxxx.supabase.co
SUPABASE_KEY=your_key
SUPABASE_BUCKET=photos
```

**Save** > **Redeploy**

### Step 6: Verify

Tunggu deploy selesai (≈ 2-3 menit), lalu test:

```bash
curl https://your-backend.railway.app/api/health
```

**Expected:**
```json
{"status":"ok","timestamp":"...","database":"connected"}
```

✅ **Berhasil!** Backend sudah online.

### Step 7: Update Frontend ENV

1. Vercel Dashboard > Project > **Settings** > **Environment Variables**
2. Edit `NEXT_PUBLIC_API_URL`:
   ```
   https://your-backend.railway.app/api
   ```
3. **Save** > **Redeploy**

---

## 🔍 Troubleshooting

### Build Failed?
**Check:** Railway logs untuk error messages
**Common:** PHP extensions kurang → Sudah fixed di `nixpacks.toml`

### Deployment Crashed?
**Check:**
1. ✅ Root Directory = `backend`
2. ✅ MySQL service di-link
3. ✅ `APP_KEY` sudah diset
4. ✅ Database variables benar (`DB_HOST`, dll)

**View Logs:**
Railway Dashboard > Deployments > View Logs

### Still Error?

Baca dokumentasi lengkap:
- **Backend deploy:** `/backend/RAILWAY.md`
- **Full guide:** `/DEPLOYMENT.md`

---

## 📚 Files yang Sudah Disiapkan

✅ `backend/nixpacks.toml` - Railway build config
✅ `backend/Procfile` - Start command
✅ `backend/.env.example` - ENV template
✅ `backend/build.sh` - Build script
✅ `backend/generate-key.bat` - Generate APP_KEY helper
✅ Health check endpoint: `/api/health`
✅ CORS configured untuk Vercel

---

## ⚡ Common Issues & Quick Fixes

| Error | Fix |
|-------|-----|
| "No APP_KEY" | Generate dengan `php artisan key:generate --show` |
| "DB Connection refused" | Link MySQL service di Railway |
| "404 on /api/..." | Set Root Directory = `backend` |
| "CORS error" | Add Vercel URL ke `CORS_ALLOWED_ORIGINS` |
| "Class not found" | Check build logs, composer install failed? |

---

Butuh bantuan? Check `/backend/RAILWAY.md` untuk troubleshooting lengkap! 🆘
