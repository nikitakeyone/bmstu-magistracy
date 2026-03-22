#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Скрипт для экспорта проекта новостного сайта в Markdown-документацию
"""

import os
import json
from pathlib import Path
from datetime import datetime

# Структура проекта
PROJECT_STRUCTURE = {
    'docker-compose.yml': '',
    'app/.env': '',
    'app/composer.json': '',
    'docker/php/Dockerfile': '',
    'docker/nginx/default.conf': '',
    'app/public/index.php': '',
    'app/migrations/001_initial.sql': '',
    'app/src/Config/Database.php': '',
    'app/src/Core/Router.php': '',
    'app/src/Core/Request.php': '',
    'app/src/Core/Response.php': '',
    'app/src/Models/News.php': '',
    'app/src/Models/User.php': '',
    'app/src/Models/Category.php': '',
    'app/src/Repositories/Postgres/NewsMetaRepository.php': '',
    'app/src/Repositories/Postgres/UserRepository.php': '',
    'app/src/Repositories/Postgres/CategoryRepository.php': '',
    'app/src/Repositories/Mongo/NewsContentRepository.php': '',
    'app/src/Services/NewsService.php': '',
    'app/src/Controllers/HomeController.php': '',
    'app/src/Controllers/NewsController.php': '',
    'app/src/Controllers/AdminController.php': '',
    'app/src/Views/layouts/main.php': '',
    'app/src/Views/home/index.php': '',
    'app/src/Views/news/index.php': '',
    'app/src/Views/news/show.php': '',
    'app/src/Views/admin/login.php': '',
    'app/src/Views/admin/dashboard.php': '',
    'app/src/Views/admin/news-form.php': '',
    'app/src/Views/errors/404.php': '',
}


def read_file(filepath):
    """Чтение содержимого файла"""
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            return f.read()
    except FileNotFoundError:
        return None
    except Exception as e:
        return f"⚠️ Ошибка чтения: {str(e)}"


def create_code_block(content, language):
    """Создание Markdown-блока кода"""
    return f"```{language}\n{content}\n```"


def export_file_content(readme_content):
    """Добавление содержимого файлов в документацию"""

    files_section = "\n\n## 📁 Содержимое файлов\n\n"

    for filepath in PROJECT_STRUCTURE.keys():
        content = read_file(filepath)
        if content:
            # Определение языка для подсветки
            ext = Path(filepath).suffix.lower()
            language_map = {
                '.php': 'php',
                '.sql': 'sql',
                '.json': 'json',
                '.yml': 'yaml',
                '.yaml': 'yaml',
                '.conf': 'nginx',
                '.env': 'bash',
                '.dockerfile': 'dockerfile',
            }
            language = language_map.get(ext, 'text')

            # Для Dockerfile без расширения
            if 'Dockerfile' in filepath:
                language = 'dockerfile'

            files_section += f"### `{filepath}`\n\n"
            files_section += create_code_block(content, language)
            files_section += "\n\n---\n\n"

    return readme_content + files_section


print("📄 Чтение файлов проекта...")
readme = export_file_content("")

output_file = 'PROJECT_DOCUMENTATION.md'
with open(output_file, 'w', encoding='utf-8') as f:
    f.write(readme)

print(f"✅ Документация сохранена в {output_file}")
print(f"📊 Размер файла: {os.path.getsize(output_file) / 1024:.2f} KB")

print("\n📦 Создание структуры проекта...")

structure_file = 'project_structure.json'
structure = {
    'generated_at': datetime.now().isoformat(),
    'files': {}
}

for filepath in PROJECT_STRUCTURE.keys():
    content = read_file(filepath)
    if content:
        structure['files'][filepath] = {
            'size': len(content),
            'lines': len(content.splitlines())
        }

with open(structure_file, 'w', encoding='utf-8') as f:
    json.dump(structure, f, indent=2, ensure_ascii=False)

print(f"✅ Структура сохранена в {structure_file}")

total_files = len([f for f in PROJECT_STRUCTURE.keys() if read_file(f)])
total_lines = sum(
    len(read_file(f).splitlines())
    for f in PROJECT_STRUCTURE.keys()
    if read_file(f)
)

print("\n📊 Статистика проекта:")
print(f"   Файлов: {total_files}")
print(f"   Строк кода: {total_lines}")
print(f"   Документация: {output_file}")