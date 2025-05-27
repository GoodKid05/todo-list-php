# Проект: ToDo API
## 🚀 Быстрый старт

1. Склонируйте репозиторий:
```bash
git clone https://github.com/you/project.git
cd project
```
2. Сделайте скрипт миграций исполняемым:

chmod +x scripts/migrate.sh

3. Запустите проект:
docker-compose up --build -d

4. Примените миграции:
docker exec -it php_app bash -c "./scripts/migrate.sh"

5. Откройте в браузере:
http://localhost:8000