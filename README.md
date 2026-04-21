# Portfolio (Laravel 12 + Filament 4 + Vue 3)

پروژه شخصی معرفی مهارت‌ها، نمونه‌کارها و مقالات با پنل مدیریت حرفه‌ای.

## معرفی
این پروژه یک وب‌سایت پورتفولیو با رابط کاربری مدرن و پنل مدیریت کامل است که با **Laravel 12**، **Filament 4** و **Vue 3** توسعه داده شده است.

در این سیستم می‌توان تمام محتوای سایت را از پنل مدیریت کنترل کرد (نمایه شخصی، درباره من، مهارت‌ها، نمونه‌کارها، مقالات، راه‌های ارتباطی، تنظیمات سایت و رزومه PDF).

## امکانات اصلی

### بخش وب‌سایت
- نمایش پروفایل شخصی و وضعیت فعلی
- نمایش درباره من، مهارت‌ها و سوابق حرفه‌ای
- نمایش نمونه‌کارها (لیست + جزئیات)
- نمایش مقالات (لیست + جزئیات)
- فرم تماس با من
- طراحی RTL و فارسی
- دریافت داده‌ها از API (به‌جای رندر مستقیم داده در Blade)

### بخش پنل مدیریت (Filament)
- مدیریت نمایه شخصی
- مدیریت بخش درباره من و دسته‌بندی مهارت‌ها
- مدیریت مهارت‌ها
- مدیریت پروژه‌ها و دسته‌بندی پروژه‌ها
- مدیریت مقالات
- مدیریت درخواست‌های تماس
- تنظیمات سایت (تم رنگی + تصویر پیش‌نمایش شبکه‌های اجتماعی)
- مدیریت رزومه:
  - تولید رزومه PDF نسخه‌بندی‌شده
  - بارگذاری دستی فایل رزومه
  - انتخاب فایل فعال
  - حذف تکی و گروهی با کنترل ایمن برای فایل فعال
  - پیش‌نمایش PDF داخل مودال در پنل

## سیستم رزومه PDF
- تولید PDF با **Browsershot (Headless Chrome)**
- قالب رزومه سفارشی و فارسی
- پشتیبانی از فونت سفارشی
- نمایش آیکن‌های شبکه اجتماعی در خروجی
- لینک‌های قابل کلیک داخل PDF:
  - شبکه‌های اجتماعی
  - لینک پروژه‌ها (در صورت ثبت)
- نسخه‌بندی فایل‌ها (مثل `cv-v001.pdf`, `cv-v002.pdf`, ...)

## تکنولوژی‌ها

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-4-F59E0B?style=for-the-badge)
![Vue.js](https://img.shields.io/badge/Vue.js-3-42B883?style=for-the-badge&logo=vuedotjs&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-7-646CFF?style=for-the-badge&logo=vite&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Node.js](https://img.shields.io/badge/Node.js-18%2B-339933?style=for-the-badge&logo=nodedotjs&logoColor=white)
![Puppeteer](https://img.shields.io/badge/Puppeteer-Browsershot-40B5A4?style=for-the-badge&logo=puppeteer&logoColor=white)

## پیش‌نیازها
قبل از اجرا مطمئن شوید نصب هستند:
- PHP 8.2+
- Composer
- Node.js 18+ و npm
- MySQL
- Google Chrome (برای تولید PDF با Browsershot)

## نصب و اجرا

### 1) دریافت پروژه
```bash
git clone https://github.com/pooriajahedi/portfolio.git
cd portfolio
```

### 2) نصب وابستگی‌ها
```bash
composer install
npm install
```

### 3) تنظیم فایل محیط
```bash
cp .env.example .env
php artisan key:generate
```

فایل `.env` را با اطلاعات دیتابیس خودتان تنظیم کنید:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4) اجرای مایگریشن‌ها
```bash
php artisan migrate
```

### 5) لینک storage
```bash
php artisan storage:link
```

### 6) بیلد یا اجرای فرانت
حالت توسعه:
```bash
npm run dev
```

حالت production:
```bash
npm run build
```

### 7) اجرای پروژه
```bash
php artisan serve
```
یا در محیط Valet از دامنه local خودتان (مثلا `https://portfolio.test`).

## مسیرهای مهم
- وب‌سایت: `/`
- پنل مدیریت: `/admin`
- پیش‌نمایش قالب رزومه (ادمین): `/admin/cv-preview`

## API عمومی
چند مسیر اصلی API:
- `/api/hero`
- `/api/about`
- `/api/resume-items`
- `/api/portfolio`
- `/api/blog`
- `/api/contact`

(براساس وضعیت پروژه ممکن است نام/خروجی بعضی endpointها تغییر کند.)

## نکات Deployment
- برای تولید PDF روی سرور، Chrome/Chromium باید در دسترس باشد.
- مجوز نوشتن برای `storage/` و `bootstrap/cache/` الزامی است.
- بعد از دیپلوی:
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## توسعه‌دهنده
**Pooria Jahedi**

- GitHub: [github.com/pooriajahedi](https://github.com/pooriajahedi)
- LinkedIn: [linkedin.com/in/pooria-jahedi](https://linkedin.com/in/pooria-jahedi)

## License
This project is open-sourced under the MIT license.
