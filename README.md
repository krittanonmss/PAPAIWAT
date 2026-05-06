# PAPAIWAT

Laravel CMS สำหรับจัดการข้อมูลวัด บทความ Media, Menu และ Page Builder

## Requirements

แนะนำให้ใช้ Docker เพราะโปรเจกต์นี้มี migration แยกหลาย folder

สำหรับ Docker:

- Docker
- Docker Compose

สำหรับติดตั้งแบบ local:

- PHP 8.3+
- Composer
- Node.js 22+
- MySQL 8+

## Quick Start ด้วย Docker

เริ่ม services ทั้งหมด:

```bash
docker compose up -d --build
```

ติดตั้ง database schema และ seed ข้อมูลระบบ:

```bash
docker compose exec app ./docker/migrate-and-seed.sh
```

เปิดเว็บ:

```text
http://localhost:8000
```

เปิด phpMyAdmin:

```text
http://localhost:8080
```

## Default Admin

Seeder จะสร้าง admin เริ่มต้นให้:

```text
Email: admin@example.com
Username: superadmin
Password: 12345678
```

สามารถ override ผ่าน environment variables ได้:

```env
ADMIN_EMAIL=admin@example.com
ADMIN_USERNAME=superadmin
ADMIN_PASSWORD=12345678
```

## Docker Services

```text
app        Laravel app, port 8000
vite       Vite dev server, port 5173
queue      Laravel queue listener
mysql      MySQL 8.4, port 3306
phpmyadmin phpMyAdmin, port 8080
```

## Database Credentials ใน Docker

ค่า default ใน `docker-compose.yml`:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=papaiwat_db
DB_USERNAME=papaiwat
DB_PASSWORD=papaiwat
DB_ROOT_PASSWORD=root
```

ถ้ามี `.env` ในเครื่องอยู่แล้ว Docker Compose อาจอ่านค่า `DB_USERNAME` / `DB_PASSWORD` จากไฟล์นั้นด้วย

## Migration

โปรเจกต์นี้ไม่ได้ใช้ migration path เดียว จึงไม่ควรพึ่ง `php artisan migrate` ธรรมดาอย่างเดียว

ใช้คำสั่งนี้ใน Docker:

```bash
docker compose exec app ./docker/migrate-and-seed.sh
```

หรือรันทีละ path:

```bash
docker compose exec app php artisan migrate --path=database/migrations/system
docker compose exec app php artisan migrate --path=database/migrations/admin
docker compose exec app php artisan migrate --path=database/migrations/content/categories
docker compose exec app php artisan migrate --path=database/migrations/content/media
docker compose exec app php artisan migrate --path=database/migrations/content
docker compose exec app php artisan migrate --path=database/migrations/content/temple
docker compose exec app php artisan migrate --path=database/migrations/content/article
docker compose exec app php artisan migrate --path=database/migrations/content/layout
docker compose exec app php artisan db:seed
```

## Fresh Install Database

ถ้าต้องการล้าง DB และลงใหม่ทั้งหมด:

```bash
docker compose exec app php artisan migrate:fresh \
  --path=database/migrations/system \
  --path=database/migrations/admin \
  --path=database/migrations/content/categories \
  --path=database/migrations/content/media \
  --path=database/migrations/content \
  --path=database/migrations/content/temple \
  --path=database/migrations/content/article \
  --path=database/migrations/content/layout \
  --seed
```

## Seeder

`DatabaseSeeder` จะ seed เฉพาะข้อมูลระบบที่จำเป็น:

- Roles
- Permissions
- Role permissions
- Default admin
- Frontend templates

Seeder จะไม่สร้าง page, menu, article, temple หรือ demo content

รัน seeder อย่างเดียว:

```bash
docker compose exec app php artisan db:seed
```

## Local Setup แบบไม่ใช้ Docker

ติดตั้ง dependency:

```bash
composer install
npm install
```

เตรียม env:

```bash
cp .env.example .env
php artisan key:generate
```

ตั้งค่า database ใน `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=papaiwat_db
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

รัน migration ตาม path:

```bash
php artisan migrate --path=database/migrations/system
php artisan migrate --path=database/migrations/admin
php artisan migrate --path=database/migrations/content/categories
php artisan migrate --path=database/migrations/content/media
php artisan migrate --path=database/migrations/content
php artisan migrate --path=database/migrations/content/temple
php artisan migrate --path=database/migrations/content/article
php artisan migrate --path=database/migrations/content/layout
php artisan db:seed
```

รัน app:

```bash
php artisan serve
npm run dev
```

## Useful Commands

เข้า shell ใน container:

```bash
docker compose exec app bash
```

รัน test:

```bash
docker compose exec app php artisan test
```

Clear cache:

```bash
docker compose exec app php artisan optimize:clear
```

Build frontend assets:

```bash
docker compose exec app npm run build
```

ดู logs:

```bash
docker compose logs -f app
```

ปิด container:

```bash
docker compose down
```

ปิดและลบ database volume:

```bash
docker compose down -v
```

## Project Notes

- หน้า Page Builder ใช้ template `frontend.templates.pages.builder`
- Template list/detail ยังถูก seed ให้พร้อมใช้ แต่ page/content ต้องสร้างเองผ่าน admin
- Media upload ใช้ disk `public`
- หลังติดตั้งควรรัน `php artisan storage:link` ซึ่ง Docker entrypoint ทำให้อัตโนมัติแล้ว

