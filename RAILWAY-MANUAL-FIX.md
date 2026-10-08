# 🔴 Railway Manual Fix - Jika Reference Tidak Bekerja

Jika MySQL Reference tidak ter-inject, kita set **manual**.

---

## Step 1: Get MySQL Credentials

Railway Dashboard > **MySQL service** (bukan backend) > **Connect** tab

Copy semua credentials:

```
MYSQLHOST = abcd1234.railway.internal  (atau IP)
MYSQLPORT = 3306
MYSQLDATABASE = railway
MYSQLUSER = root
MYSQLPASSWORD = AbCd1234XyZ...
```

**SCREENSHOT atau CATAT credentials ini!**

---

## Step 2: Generate APP_KEY

**Local terminal:**
```cmd
cd c:\inventaris-visualisasi-backup\backend
php artisan key:generate --show
```

**Copy output:**
```
base64:abcdef123456...
```

---

## Step 3: Set ALL Environment Variables Manual

Railway Dashboard > **Backend Service** > **Variables** tab

**Klik "Raw Editor"** (toggle kanan atas), **DELETE SEMUA**, lalu paste ini:

```bash
# ==========================================
# APP CONFIGURATION
# ==========================================
APP_NAME=Inventaris Visualisasi
APP_ENV=production
APP_KEY=base64:PASTE_HASIL_STEP_2_DISINI
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=${{RAILWAY_PUBLIC_DOMAIN}}

# ==========================================
# DATABASE - PASTE DARI STEP 1
# ==========================================
DB_CONNECTION=mysql
DB_HOST=PASTE_MYSQLHOST_DISINI
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=PASTE_MYSQLPASSWORD_DISINI

# ==========================================
# SESSION & CACHE
# ==========================================
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

CACHE_STORE=database
CACHE_PREFIX=

QUEUE_CONNECTION=database

# ==========================================
# LOGGING
# ==========================================
LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

# ==========================================
# CORS
# ==========================================
CORS_ALLOWED_ORIGINS=https://your-vercel-app.vercel.app

# ==========================================
# STORAGE (Optional - isi nanti)
# ==========================================
FILESYSTEM_DISK=public

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=auto
AWS_BUCKET=inventaris-app
AWS_ENDPOINT=
AWS_URL=
AWS_USE_PATH_STYLE_ENDPOINT=false

SUPABASE_URL=
SUPABASE_KEY=
SUPABASE_BUCKET=photos

# ==========================================
# MAIL (Optional)
# ==========================================
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME=Inventaris Visualisasi
```

**⚠️ PENTING:**
- Ganti `APP_KEY` dengan hasil Step 2
- Ganti `DB_HOST` dengan `MYSQLHOST` dari Step 1
- Ganti `DB_PASSWORD` dengan `MYSQLPASSWORD` dari Step 1
- Ganti `CORS_ALLOWED_ORIGINS` dengan URL Vercel Anda

**Save!**

---

## Step 4: Verify Variables

Scroll di Variables tab, pastikan ada:

```
APP_KEY = base64:xxx... (ada isinya!)
DB_HOST = abcd1234.railway.internal (bukan 127.0.0.1!)
DB_PASSWORD = (string panjang, bukan kosong!)
```

---

## Step 5: Redeploy

**Opsi 1 - Via Dashboard:**
Railway Dashboard > Backend > Deployments > **Redeploy**

**Opsi 2 - Via Git:**
```cmd
git commit --allow-empty -m "Trigger redeploy with manual DB config"
git push origin main
```

Tunggu 2-3 menit...

---

## Step 6: Monitor Logs

Railway Dashboard > Deployments > **View Logs**

**Cari baris:**
```
✅ "Configuration cache cleared successfully"
✅ "Running migrations..."
✅ "Migration table created successfully"
✅ "Migrated: ..."
✅ "php artisan serve --host=0.0.0.0"
```

**Jika masih error:**
```
❌ "SQLSTATE[HY000] [2002] Connection refused"
```

→ DB credentials salah! Cek Step 1 lagi.

---

## Step 7: Test

```bash
curl https://your-backend.railway.app/api/health
```

**Expected:**
```json
{"status":"ok","timestamp":"...","database":"connected"}
```

✅ **BERHASIL!**

---

## 🔍 Troubleshooting

### ❌ Still Connection Refused?

**Check MySQL service:**
1. Railway Dashboard > MySQL service
2. Status = **Active**? (jika tidak, tunggu atau restart)
3. Tab **Connect** > Credentials benar?

**Check Backend Variables:**
1. `DB_HOST` = internal hostname (`.railway.internal`) atau IP
2. `DB_HOST` ≠ `127.0.0.1` (ini SALAH!)
3. `DB_PASSWORD` ada isinya (tidak kosong)

**Test connection manual:**
```bash
# Di Railway Settings > Add Custom Start Command (test only):
php artisan db:show
```

---

### ❌ "No APP_KEY specified"

`APP_KEY` kosong atau format salah.

**Fix:**
```cmd
php artisan key:generate --show
# Copy: base64:abcd1234...

# Railway Variables:
APP_KEY=base64:abcd1234...  (INCLUDE "base64:" prefix!)
```

---

### ❌ "Class ... not found"

Composer install failed.

**Check build logs:**
```
[phases.install] Running 'composer install --no-dev --optimize-autoloader --no-interaction'
```

Jika error, cek `composer.json` atau `composer.lock`.

**Force rebuild:**
Railway > Settings > **Remove Build Cache** > Redeploy

---

### ❌ Migration Errors

**"Syntax error: Specified key was too long"**

Already handled di Laravel 12, tapi jika masih error:

Edit `app/Providers/AppServiceProvider.php`:
```php
use Illuminate\Support\Facades\Schema;

public function boot(): void
{
    Schema::defaultStringLength(191);
}
```

Commit & push.

---

### ❌ CORS Errors di Frontend

**Error:** "No 'Access-Control-Allow-Origin' header"

**Fix:**
Railway Variables:
```
CORS_ALLOWED_ORIGINS=https://your-exact-vercel-url.vercel.app
```

Jangan lupa **redeploy** setelah update!

---

## 📸 Screenshot Checklist

Before asking for help, screenshot:

1. **Railway Variables tab** (full scroll)
2. **Deploy Logs** (error section)
3. **MySQL Connect tab** (credentials - hide password!)
4. **Build Logs** (if failed)

---

## ✅ Success Checklist

- [ ] MySQL service status = Active
- [ ] `APP_KEY` set dengan format `base64:xxx...`
- [ ] `DB_HOST` = MySQL internal hostname (NOT 127.0.0.1)
- [ ] `DB_PASSWORD` = actual password dari MySQL
- [ ] Build logs = success (no errors)
- [ ] Deploy logs = "Migration ... completed successfully"
- [ ] `/api/health` returns 200 OK

---

Jika masih error dengan manual config ini, kemungkinan:
1. **MySQL service down** - check status
2. **Network issue** - Railway internal network problem (rare)
3. **Wrong credentials** - double check Step 1

Share screenshot logs kalau masih stuck! 🆘
