=== بهینه‌ساز تصویر موبایل خرسندی ===
Contributors: mobilekhorsandi
Tags: webp, images, performance, responsive
Requires at least: 6.5
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

تبدیل خودکار JPEG/PNG به WebP هنگام آپلود، سایزهای واکنش‌گرا و ابزار تبدیل گروهی.

== Description ==

* آپلودهای جدید JPEG/PNG به‌صورت خودکار WebP می‌شوند (API رسمی وردپرس ۶.۵ به بعد؛ نسخه اصلی به‌عنوان پشتیبان نگه داشته می‌شود).
* کیفیت WebP از صفحه تنظیمات (رسانه ← بهینه‌ساز تصویر) قابل تنظیم است.
* دو سایز واکنش‌گرا (`mobilekh-card` و `mobilekh-wide`) ثبت می‌شود تا مرورگر بر اساس نمایشگر از `srcset` انتخاب کند؛ با فیلتر `mobilekh_webp_sizes` قابل تغییر است.
* ابزار تبدیل گروهی، رسانه‌های قدیمی را بچ‌بچ (۱۰تایی) و بدون تایم‌اوت تبدیل می‌کند.
* GIF (متحرک) عمداً نادیده گرفته می‌شود.
* پیش‌نیاز سرور: GD یا Imagick با پشتیبانی WebP؛ در غیر این صورت هشدار نمایش داده می‌شود و تبدیلی انجام نمی‌شود.

== Installation ==

1. پوشه `mobilekh-webp` را در `wp-content/plugins/` کپی کنید.
2. از پیشخوان ← افزونه‌ها فعال کنید.
3. تنظیمات در رسانه ← بهینه‌ساز تصویر.

== Uninstall ==

فقط گزینه‌های افزونه پاک می‌شوند؛ تصاویر WebP تولیدشده معتبر و قابل استفاده باقی می‌مانند.

== Changelog ==

= 1.0.0 =
* انتشار اولیه.
