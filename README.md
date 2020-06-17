# amoCrmAndGoogleSheets
Интеграция

#### **amoCRMClientForApiV4 ещё в стадии разработки.**

1. Вам нужно создать сервисный акаунт в https://console.cloud.google.com/
скачать json фай поместит на корневом директории и переминовать его в credentials.json.

2. Для того чтобы установить все зависимости для подключение к Google Sheets Api.Выполните команду в консоль
 `$composer install`

3. Настройте Webhooks в amoCRM для Сделок

4. В файле config.php вставте свои данные для amoCRM

5. Для того чтобы отправлять данные обратно из Google Sheets в amoCrm в Google Sheets установите дополнение Trigger 
& Send и настройте его.