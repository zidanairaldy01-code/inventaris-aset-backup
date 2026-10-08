# 🚂 Railway Deployment Guide - Laravel Backend

## Quick Deploy Checklist

### ✅ Before Deploy

- [x] `nixpacks.toml` configured ✓
- [x] `Procfile` configured ✓
- [x] `.env.example` ready ✓
- [x] Health check endpoint at `/api/health` ✓
- [ ] MySQL database provisioned in Railway
- [ ] All environment variables set

---

## 📋 Environment Variables untuk Railway

Copy ini ke Railway Dashboard > Variables:

```bash
# ============================================
# APPLICATION
# ============================================
APP_NAME="Inventaris Visualisasi"
APP_ENV=production
APP_KEY=                          # ⚠️ GENERATE: php artisan key:generate --show
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=                          # ⚠️ AKAN DIISI OTOMATIS OLEH RAILWAY

# ============================================
# DATABASE - LINK MYSQL SERVICE!
# ============================================
DB_CONNECTION=mysql
DB_HOST=${{MYSQLHOST}}
DB_PORT=${{MYSQLPORT}}
DB_DATABASE=${{MYSQLDATABASE}}
DB_USERNAME=${{MYSQLUSER}}
DB_PASSWORD=${{MYSQLPASSWORD}}

# ============================================
# CORS - GANTI DENGAN URL VERCEL ANDA
# ============================================
CORS_ALLOWED_ORIGINS=https://your-app.vercel.app,https://your-app-preview.vercel.app

# ============================================
# SESSION & CACHE
# ============================================
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
LOG_CHANNEL=stack
LOG_LEVEL=error

# ============================================
# STORAGE - CLOUDFLARE R2
# ============================================
AWS_ACCESS_KEY_ID=your_r2_access_key_id
AWS_SECRET_ACCESS_KEY=your_r2_secret_access_key
AWS_DEFAULT_REGION=auto
AWS_BUCKET=inventaris-app
AWS_ENDPOINT=https://your-account-id.r2.cloudflarestorage.com
AWS_URL=https://pub-xxxxx.r2.dev
AWS_USE_PATH_STYLE_ENDPOINT=false

# ============================================
# SUPABASE STORAGE (Optional)
# ============================================
SUPABASE_URL=https://your-project.supabase.co
SUPABASE_KEY=your_anon_key_here
SUPABASE_BUCKET=photos
```

---

## 🔧 Railway Configuration

### 1. Root Directory Setting

**PENTING:** Railway harus tahu folder backend adalah root directory.

**Railway Dashboard:**
```
Settings > Service Settings > Root Directory: backend
```

**Atau edit `railway.toml` (di root project):**
```toml
[build]
builder = "nixpacks"
buildCommand = "composer install --no-dev --optimize-autoloader --no-interaction"

[deploy]
startCommand = "php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=$PORT"
```

### 2. Link MySQL Database

1. Railway Dashboard > **New** > **Database** > **Add MySQL**
2. Setelah MySQL ready, buka **backend service**
3. Klik **Variables** tab
4. Klik **Add Reference** 
5. Select **MySQL service**
6. Check semua MySQL variables:
   - MYSQLHOST
   - MYSQLPORT
   - MYSQLDATABASE
   - MYSQLUSER
   - MYSQLPASSWORD
7. **Save**

### 3. Generate APP_KEY

**Local command:**
```bash
cd backend
php artisan key:generate --show
```

**Output example:**
```
base64:abcdef123456789ABCDEF123456789ABCDEF1234567890=
```

Copy output ke Railway Variable `APP_KEY`.

---

## 🐛 Troubleshooting

### ❌ Build Failed

**Error: "composer: command not found"**

**Fix:** `nixpacks.toml` sudah include `composer`, tapi pastikan:
```toml
[phases.setup]
nixPkgs = [..., "composer"]
```

**Error: "Class 'DOMDocument' not found"**

**Fix:** Sudah fixed di `nixpacks.toml`:
```toml
nixPkgs = [
  ...
  "php82Extensions.dom",
  "php82Extensions.xmlreader",
  "php82Extensions.xmlwriter"
]
```

### ❌ Deployment Crashed

**Error: "No application encryption key has been specified"**

**Fix:**
1. Generate key: `php artisan key:generate --show`
2. Add ke Railway Variables: `APP_KEY=base64:xxx...`
3. Redeploy

**Error: "SQLSTATE[HY000] [2002] Connection refused"**

**Fix:**
1. Pastikan MySQL service running
2. Link MySQL ke backend service (lihat section "Link MySQL Database")
3. Check variables `DB_HOST`, `DB_PORT`, etc sudah benar
4. Redeploy

**Error: "Syntax error or access violation: 1071 Specified key was too long"**

**Fix:** Laravel 12 sudah handle ini, tapi jika masih error:

Edit `app/Providers/AppServiceProvider.php`:
```php
use Illuminate\Support\Facades\Schema;

public function boot(): void
{
    Schema::defaultStringLength(191);
}
```

### ❌ Runtime Errors

**Error 500: Internal Server Error**

**Debug:**
1. Railway Dashboard > **View Logs**
2. Look for:
   - `php artisan migrate` errors
   - Storage permission issues
   - Missing environment variables

**Fix common issues:**
```bash
# Check logs untuk error messages:
Railway Dashboard > Deployments > Latest > Logs

# Common errors:
# - "Class not found" → Check composer autoload
# - "Storage not writable" → Already configured in Laravel
# - "Undefined variable" → Missing ENV variable
```

**API returns 404**

**Fix:**
1. Check route: `curl https://your-app.railway.app/api/health`
2. Should return: `{"status":"ok","timestamp":"...","database":"connected"}`
3. If 404, cache might be stale:
   ```toml
   # In nixpacks.toml, ensure build phase clears cache:
   [phases.build]
   cmds = [
     "php artisan config:clear",
     "php artisan route:clear",
     "php artisan view:clear"
   ]
   ```

### ❌ CORS Errors

**Error: "CORS policy: No 'Access-Control-Allow-Origin' header"**

**Fix:**
1. Add Vercel URL ke Railway ENV:
   ```bash
   CORS_ALLOWED_ORIGINS=https://your-app.vercel.app
   ```
2. File `config/cors.php` sudah configured untuk wildcard Vercel domains
3. Redeploy

---

## 🧪 Test Deployment

### 1. Health Check
```bash
curl https://your-backend.railway.app/api/health
```

**Expected response:**
```json
{
  "status": "ok",
  "timestamp": "2024-01-15T10:30:00+07:00",
  "database": "connected"
}
```

### 2. Database Connection
```bash
curl https://your-backend.railway.app/api/gedungs
```

**If success:** Returns list of buildings (or empty array `[]`)

**If error 500:** Check logs for database connection issues

### 3. API Endpoint
```bash
curl -X POST https://your-backend.railway.app/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

---

## 📊 Monitoring

### View Logs
```
Railway Dashboard > Deployments > View Logs
```

### Check Build Phases
Railway logs will show:
```
[nixpacks] Building with PHP 8.2
[nixpacks] Running setup phase...
[nixpacks] Running install phase...
[nixpacks] Running build phase...
[nixpacks] Running start phase...
```

### Health Metrics
Railway Dashboard > **Metrics** tab menampilkan:
- CPU usage
- Memory usage
- Request count
- Response time

---

## 🔄 Deploy Updates

### Auto Deploy (Recommended)
```bash
git add .
git commit -m "Update backend"
git push origin main
# Railway auto-deploys on push
```

### Manual Redeploy
Railway Dashboard > **Deployments** > **Deploy** button

### Rollback
Railway Dashboard > **Deployments** > Select previous deployment > **Redeploy**

---

## 🔐 Security Best Practices

- ✅ `APP_DEBUG=false` in production
- ✅ Use strong `APP_KEY`
- ✅ Keep `.env` out of Git (in `.gitignore`)
- ✅ Use environment variables for all secrets
- ✅ Enable HTTPS only (Railway default)
- ✅ Configure CORS whitelist
- ✅ Use database connection from Railway (not public IP)

---

## 🆘 Still Having Issues?

1. **Check build logs** - Railway Dashboard > View Logs
2. **Verify ENV variables** - All required variables set?
3. **Test health endpoint** - `curl /api/health`
4. **Check database link** - MySQL service connected?
5. **Review nixpacks.toml** - All PHP extensions included?

**Common mistake:** Forgetting to set **Root Directory** to `backend`!

---

## ✅ Success Indicators

Your backend is working if:
- ✅ Build phase completes without errors
- ✅ Migration runs successfully
- ✅ `/api/health` returns 200 OK
- ✅ API endpoints accessible
- ✅ Frontend can connect (no CORS errors)

Happy deploying! 🚀
