# Задачник (Task Manager) на PHP с использованием MySQL

Простое веб-приложение для управления задачами, разработанное на PHP и MySQL.

## Как начать

Следуйте этим инструкциям, чтобы запустить проект на вашей локальной машине.

### Предварительные требования

Прежде чем начать, убедитесь, что у вас установлены следующие компоненты:

- PHP (рекомендуется версия 7.0 и выше)
- MySQL (или другая СУБД на ваш выбор)
- Веб-сервер (например, Apache или Nginx)

### Установка

1. Клонируйте репозиторий:

```bash
git clone https://github.com/dripips/task-manager.git
```

2. Создайте базу данных MySQL:

```SQL
CREATE DATABASE task_manager;
```
3. Создайте таблицу users для хранения пользователей:

```SQL
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);
```
4. Создайте таблицу tasks для хранения задач:

```SQL
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    due_date DATE,
    is_done TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```
5. Настройте подключение к базе данных в файле config/database.php.
6. Запустите веб-сервер и откройте проект в вашем браузере.
7. Вы можете зарегистрироваться и начать использовать задачник!

## Используемые технологии
- PHP
- MySQL
- Bootstrap (для стилей)

## Авторы
DRIP

