# telegram-image-bot

telegram-image-bot позволяет в один клик:

- **Кадрировать** изображение до фиксированного размера **512×512** пикселей.
- Переводить фотографию в **чёрно‑белый** вариант.
- Конвертировать изображение в форматы **PNG**, **JPG**, **TIFF** (TIFF — через Imagick).

- ## Функционал

1. Пользователь отправляет изображение боту.
2. Бот сохраняет изображение во временную папку и предлагает меню:
   - **Кадрирование** → автоматически обрезает изображение до размера **512×512**.
   - **Ч/Б** → преобразует изображение в черно-белый (оттенки серого).
   - **Конвертация** → позволяет сохранить изображение в форматах **PNG**, **JPG** или **TIFF**.
3. Результат обработки сразу отправляется обратно пользователю.


## Установка и запуск

### 1. Клонируйте репозиторий

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
C:\xampp\htdocs\telegram-image-bot
```
Проверьте работоспособность в браузере:
```
http://localhost/telegram-image-bot/bot.php
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
https://api.telegram.org/botYOUR_TOKEN/setWebhook?url=https://YOUR_NGROK_URL/telegram-image-bot/bot.php
```
Telegram: [@EnBuyukCimbom_bot](https://t.me/EnBuyukCimbom_bot)
