# social-events
Bei diesem Projekt handelt es sich um eine Single-Page-Anwendung zur Verwaltung von Veranstaltungen.
Nutzer können sich Informationen zu Veranstaltungen holen, sowie Neue erstellen.
Dieses Projekt versucht eine Alternative zu Veranstaltungen auf Facebook zu bieten.

- Events, Künstler und Venues Erstellen und Bearbeiten
- Tag-System für Künstler
- Entfernungsrechnung von Nutzer zu Venue
- Freunde-System
- Vorschlagssystem

## Change this Screenshot
![](https://raw.githubusercontent.com/cfpb/open-source-project-template/main/screenshot.png)

## Installation & Configuration

clone project on server

```
composer install
```

create mysql database and database user

copy .env.example into new .env
edit mysql data values in .env (host, port, database, username, password)
```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=social_events
DB_USERNAME=root
DB_PASSWORD=
```

Datenbank-Struktur erstellen
```
php artisan migrate:fresh
```

Verlinkung von Storage und Public-Ordner
```
php artisan storage:link
```

Zurücksetzen vom Config Cache
```
php artisan config:clear
```

Erstellung eines App-Keys
```
php artisan key:generate
```

## Usage
