# 🔍 Quick Diagnostic - Railway Crash

Sebelum saya bisa bantu lebih lanjut, tolong check ini:

---

## ✅ Checklist Diagnostik

### 1. MySQL Service Status

Railway Dashboard > **MySQL service**

```
Status: [ ] Active  [ ] Crashed  [ ] Deploying
```

**Jika Crashed:** Restart MySQL service dulu!

---

### 2. Backend Service Variables

Railway Dashboard > **Backend service** > **Variables** tab

**Cari variables ini (scroll semua):**

```
[ ] APP_NAME
[ ] APP_ENV  
[ ] APP_KEY (ada isinya? base64:xxx...)
[ ] DB_CONNECTION
[ ] DB_HOST atau MYSQLHOST (salah satu harus ada!)
[ ] DB_PORT atau MYSQLPORT
[ ] DB_DATABASE atau MYSQLDATABASE
[ ] DB_USERNAME atau MYSQLUSER
[ ] DB_PASSWORD atau MYSQLPASSWORD
```

**Berapa yang checked?**
- 0-3: ❌ Variables kurang banyak
- 4-7: ⚠️ Mungkin kurang APP_KEY atau DB credentials
- 8+: ✅ Seharusnya cukup

---

### 3. Root Directory Setting

Railway Dashboard > Backend service > **Settings** tab

```
Root Directory: [ ] (kosong)  [ ] backend  [ ] lainnya: _____
```

**Harus:** `backend`

**Jika kosong atau salah:**
Settings > Root Directory: `backend` > Save > Redeploy

---

### 4. Deploy Logs Error

Railway Dashboard > Deployments > **View Logs**

**Copy EXACT error message:**

```
Paste error disini:




```

**Common errors:**
- `Connection refused` = Database tidak terkoneksi
- `No APP_KEY` = APP_KEY belum diset
- `Class not found` = Composer install failed
- `Migration failed` = Syntax error di migration

---

### 5. Build Logs Status

Railway Dashboard > Deployments > **Build Logs** tab

**Check steps:**

```
[phases.setup]    [ ] Success  [ ] Failed
[phases.install]  [ ] Success  [ ] Failed  
[phases.build]    [ ] Success  [ ] Failed
[start]           [ ] Success  [ ] Failed
```

**Jika ada yang Failed, paste error:**

```
Paste error disini:




```

---

## 🔧 Berdasarkan Hasil Check

### Jika `MYSQLHOST` TIDAK ADA di Variables:

**❌ MySQL Reference belum di-link!**

**Fix:** Ikuti `/RAILWAY-FIX-DATABASE.md` Step 2 (Add Reference)

**ATAU** manual config: `/RAILWAY-MANUAL-FIX.md`

---

### Jika `APP_KEY` kosong atau tidak ada:

**❌ APP_KEY belum di-generate!**

**Fix:**
```cmd
cd c:\inventaris-visualisasi-backup\backend
php artisan key:generate --show
```

Copy output ke Railway Variables: `APP_KEY=base64:xxx...`

---

### Jika Root Directory kosong:

**❌ Railway tidak tahu folder backend!**

**Fix:** Settings > Root Directory: `backend` > Redeploy

---

### Jika MySQL Status = Crashed:

**❌ MySQL service down!**

**Fix:** MySQL service > 3 dots menu > **Restart**

Tunggu status = Active, lalu redeploy backend.

---

### Jika Build phase failed:

**❌ Composer atau PHP error!**

**Fix:** Check `composer.json` syntax atau PHP version requirements.

Railway uses PHP 8.2 (sudah configured di `nixpacks.toml`).

---

## 📤 Jika Masih Stuck

**Please provide:**

1. ✅ Screenshot **Variables tab** (full scroll, hide passwords)
2. ✅ Screenshot **Deploy Logs** error section
3. ✅ Hasil checklist di atas
4. ✅ MySQL service status

**Dengan info ini saya bisa fix!** 🚀
