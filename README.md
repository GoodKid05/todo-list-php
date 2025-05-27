# Проект: ToDo API
## 🚀 Быстрый старт

1. Склонируйте репозиторий:
```bash
git clone https://github.com/GoodKid05/todo-list-php
cd todo-list-php
```
2. Сделайте скрипт миграций исполняемым:
```bash
chmod +x scripts/migrate.sh
```
3. Запустите проект:
```bash
docker-compose up --build -d
```
4. Примените миграции. Зайдите в контейнер и выполните команду:
```bash
docker exec -it php_app bash -c "./scripts/migrate.sh"
```
5. Откройте в браузере:
http://localhost:8000

## 📌 Описание
**To-do List API** — это RESTful backend-приложение, написанное на чистом PHP с использованием архитектуры MVC и принципов разделения ответственности. Проект реализует полноценную систему управления задачами (CRUD), авторизацию по токенам (access/refresh) и хранение данных в PostgreSQL. В проекте используется библиотека Phinx для миграции таблиц.

### 🔐 Авторизация
API использует авторизацию через access/refresh токены.
Access токен: короткоживущий (15 минут)
Refresh токен: долговечный (7 дней)

### ✅ Эндпоинты пользователей и токенов (Users / Tokens)
POST /api/auth/register — регистрация пользователя\
POST /api/auth/login — получение токенов\
POST /api/auth/refresh — обновление access токена\
POST /api/auth/logout — инвалидирует refresh токен

### ✅ Эндпоинты задач (Tasks)
Все эндпоинты требуют access токен в Authorization: Bearer \<token>.\
GET	/api/tasks/list	Получить список задач пользователя\
POST	/api/tasks	Создать новую задачу\
GET	/api/tasks/{id}	Получить одну задачу по ID\
PUT	/api/tasks/{id}	Обновить задачу\
DELETE	/api/tasks/{id}	Удалить задачу

### 📌 Возможности проекта
✅ Регистрация / логин с токенами\
✅ CRUD-операции задач\
✅ Привязка задач к пользователю\
✅ Защита от SQL-инъекций через подготовленные выражения\
✅ Самописный DI-контейнер\
✅ Миграции таблиц\
✅ Самописный Router\
✅ Юнит тесты\
✅ Поддержка Docker
