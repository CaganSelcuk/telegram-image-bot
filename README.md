# telegram-image-bot 🇷🇺

**telegram-image-bot** — это Telegram-бот на PHP, позволяющий работать с изображениями в один клик:

-  Кадрирование изображения до **512×512**
-  Преобразование в **черно-белое**
-  Конвертация в форматы **PNG**, **JPG**, **TIFF** *(TIFF создаётся один раз)*

---

##  Функционал

1. Пользователь отправляет изображение боту.
2. Бот сохраняет его во временную папку (`downloads/`) и предлагает кнопки:
   - **Crop 512x512** — кадрирует до фиксированного размера
   - **Convert to black and white** — преобразует в оттенки серого
   - **Save as PNG / JPG / TIFF** — сохраняет в нужном формате
3. Бот отправляет обработанное изображение обратно пользователю.
4. TIFF может быть сгенерирован только один раз для одного изображения (через систему блокировки).

---

##  Установка и запуск

### 1. Клонирование репозитория

```bash
git clone https://github.com/CaganSelcuk/telegram-image-bot.git
cd telegram-image-bot
```
---
### 2. Установите зависимости
Убедитесь, что у вас установлен Composer.
Затем выполните:
```bash
composer install
```
Также включите Imagick в `php.ini`:
```
extension=imagick
```
---
### 3. Запустите локальный сервер
Через XAMPP включите **Apache**. Убедитесь, что путь к проекту:
```
C:\xampp81\htdocs\telegram-image-bot
```
Проверьте работоспособность в браузере:
```
http://localhost/telegram-image-bot/index.php
```
---
### 4. Подключите ngrok

```bash
ngrok http 80
```
Вы получите HTTPS-ссылку вида:
```
https://abc1234.ngrok.io
```
---
### 5. Установите webhook
Перейдите по ссылке в браузере (замените `YOUR_TOKEN` и `YOUR_NGROK_URL`):
```
https://api.telegram.org/botYOUR_TOKEN/setWebhook?url=https://YOUR_NGROK_URL/telegram-image-bot/index.php
```
Telegram: [@EnBuyukCimbom_bot](https://t.me/EnBuyukCimbom_bot)
