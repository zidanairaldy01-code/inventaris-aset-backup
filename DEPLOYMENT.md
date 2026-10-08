# Panduan Deployment

## 🚀 Deploy Frontend ke Vercel

### 1. Persiapan Frontend
Pastikan file `next.config.js` sudah benar:

```javascript
/** @type {import('next').NextConfig} */
const nextConfig = {
  output: 'standalone', // Opsional, untuk optimasi
  env: {
    NEXT_PUBLIC_API_URL: process.env.NEXT_PUBLIC_API_URL,
  },
}

module.exports = nextConfig
```

### 2. Deploy ke Vercel

**Via Vercel Dashboard:**
1. Buka https://vercel.com/new
2. Import repository GitHub Anda
3. Pilih **root directory** atau folder frontend
4. Set Environment Variables:
   ```
   NEXT_PUBLIC_API_URL=https://your-backend-url.railway.app/api
   ```
5. Klik **Deploy**

**Via Vercel CLI:**
```bash
npm i -g vercel
cd c:\inventaris-visualisasi-backup
vercel
```

---

## 🚂 Deploy Backend ke Railway

### 1. Persiapan Backend

Railway sudah memiliki file konfigurasi:
- ✅ `nixpacks.toml` - Konfigurasi build
- ✅ `Procfile` - Start command
- ✅ `.env.example` - Template environment variables

### 2. Setup Database di Railway

1. Buka Railway Dashboard: https://railway.app/
2. Klik **New Project** > **Provision MySQL**
3. Setelah MySQL provisioned, copy connection details:
   - `MYSQLHOST`
   - `MYSQLPORT`
   - `MYSQLDATABASE`
   - `MYSQLUSER`
   - `MYSQLPASSWORD`

### 3. Deploy Laravel Backend

**Via Railway Dashboard:**

1. Klik **New** > **Deploy from GitHub repo**
2. Pilih repository Anda
3. Pilih **backend folder** sebagai root directory
4. Set Environment Variables (PENTING!):

```bash
# App Settings
APP_NAME="Inventaris Visualisasi"
APP_ENV=production
APP_KEY=                          # Generate menggunakan: php artisan key:generate --show
APP_DEBUG=false
APP_URL=https://your-backend-url.railway.app
APP_TIMEZONE=Asia/Jakarta

# Database (dari MySQL service Railway)
DB_CONNECTION=mysql
DB_HOST=${MYSQLHOST}              # Dari Railway MySQL
DB_PORT=${MYSQLPORT}              # Dari Railway MySQL
DB_DATABASE=${MYSQLDATABASE}      # Dari Railway MySQL
DB_USERNAME=${MYSQLUSER}          # Dari Railway MySQL
DB_PASSWORD=${MYSQLPASSWORD}      # Dari Railway MySQL

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Storage (Cloudflare R2)
AWS_ACCESS_KEY_ID=your_r2_access_key_id
AWS_SECRET_ACCESS_KEY=your_r2_secret_access_key
AWS_DEFAULT_REGION=auto
AWS_BUCKET=inventaris-app
AWS_ENDPOINT=https://your-account-id.r2.cloudflarestorage.com
AWS_URL=https://pub-xxxxx.r2.dev
AWS_USE_PATH_STYLE_ENDPOINT=false

# Supabase Storage
SUPABASE_URL=https://your-project.supabase.co
SUPABASE_KEY=your_anon_key_here
SUPABASE_BUCKET=photos

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
```

5. **Generate APP_KEY:**
   - Jalankan di local: `php artisan key:generate --show`
   - Copy output dan paste ke `APP_KEY`

6. Klik **Deploy**

### 4. Setting Root Directory (PENTING!)

Jika Railway tidak otomatis detect folder backend:

1. Di Railway Dashboard > Settings
2. Cari **Root Directory**
3. Set ke: `backend`
4. Save dan redeploy

### 5. Monitoring & Debugging

**Lihat Logs:**
- Railway Dashboard > **View Logs**
- Cari error messages

**Common Issues:**

❌ **"Class 'DOMDocument' not found"**
- Sudah fixed dengan tambah `php82Extensions.dom` di `nixpacks.toml`

❌ **"Key not found"**
- Generate `APP_KEY` dengan `php artisan key:generate --show`

❌ **"Database connection failed"**
- Pastikan ENV variables database benar
- Link MySQL service ke backend service di Railway

❌ **"Migration failed"**
- Check logs, mungkin ada syntax error di migration
- Pastikan database sudah di-link

### 6. Connect Database Service

Di Railway Dashboard:
1. Pilih **backend service**
2. Klik **Variables** tab
3. Klik **Reference** > Pilih MySQL service
4. Select all MySQL variables
5. Save

---

## 🔗 Update Frontend dengan Backend URL

Setelah backend berhasil deploy:

1. Copy URL backend Railway (misal: `https://inventaris-backend.up.railway.app`)
2. Buka Vercel Dashboard > Project Settings > Environment Variables
3. Update `NEXT_PUBLIC_API_URL`:
   ```
   NEXT_PUBLIC_API_URL=https://inventaris-backend.up.railway.app/api
   ```
4. Redeploy frontend

---

## ✅ Verifikasi Deployment

### Test Backend:
```bash
curl https://your-backend-url.railway.app/api/health
```

### Test Frontend:
Buka browser: `https://your-app.vercel.app`

---

## 📝 Troubleshooting Railway

### Jika masih crash setelah deploy:

1. **Check Logs:**
   ```
   Railway Dashboard > Deployments > View Logs
   ```

2. **Common Fixes:**

   **Error: "No application encryption key"**
   ```bash
   # Generate key locally:
   php artisan key:generate --show
   # Copy output ke Railway ENV: APP_KEY=base64:xxx...
   ```

   **Error: "SQLSTATE[HY000] [2002]"**
   - Database tidak terkoneksi
   - Link MySQL service di Railway Dashboard
   - Restart deployment

   **Error: "Class not found"**
   ```bash
   # Pastikan composer install berhasil
   # Check logs untuk "composer install" step
   ```

3. **Force Rebuild:**
   - Railway Dashboard > Settings > **Remove and Redeploy**

4. **Check Build Phase:**
   - Railway akan run `nixpacks.toml` phases
   - Check apakah semua phase berhasil di logs

---

## 🔄 Workflow Update

### Update Frontend:
```bash
git add .
git commit -m "Update frontend"
git push origin main
# Vercel auto-deploy
```

### Update Backend:
```bash
cd backend
git add .
git commit -m "Update backend"
git push origin main
# Railway auto-deploy
```

---

## 🔐 Security Checklist

- [x] `APP_DEBUG=false` di production
- [x] `APP_ENV=production`
- [x] Strong `APP_KEY` generated
- [x] Database credentials di environment variables
- [x] Tidak commit `.env` ke Git
- [x] CORS configured untuk frontend URL
- [x] API rate limiting enabled

---

## 📞 Support

Jika masih ada masalah:
1. Check Railway logs untuk error messages
2. Check Vercel logs untuk frontend issues
3. Verify all environment variables set correctly
4. Ensure database is linked and accessible

Good luck! 🚀
