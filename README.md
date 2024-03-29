# social-events
Bei diesem Projekt handelt es sich um eine Single-Page-Anwendung zur Verwaltung von Veranstaltungen.<br>
Nutzer können sich Informationen zu Veranstaltungen holen, sowie Neue erstellen.<br>
Dieses Projekt versucht eine Alternative zu Veranstaltungen auf Facebook zu bieten.<br>

- Events, Künstler und Venues Erstellen und Bearbeiten
- Tag-System für Künstler
- Entfernungsrechnung von Nutzer zu Venue
- Freunde-System
- Vorschlagssystem

![](./docs/img/dashboard.png)

## Dependencies
- Composer 2.4.4
- PHP 8.1.6
- MySQL

## Installation & Konfiguration
1. Projekt auf den Server Clonen
2. Composer Installation ausführen:
```
composer install
```
3. MySQL Datenbank und Nutzer erstellen
4. ".env.example" im Root-Verzeichnis duplizieren und in ".env" umbenennen
5. MySQL Daten in ".env" ergänzen:
```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=datenbankname
DB_USERNAME=datenbanknutzer
DB_PASSWORD=datenbankpassword
```
6. Datenbank-Struktur erstellen
```
php artisan migrate:fresh
```
7. Verlinkung von Storage und Public-Ordner
```
php artisan storage:link
```
8. Zurücksetzen vom Config Cache
```
php artisan config:clear
```
9. Erstellung eines App-Keys
```
php artisan key:generate
```

## Nutzung
Für die Nutzung der Applikation gibt es eine eigene [Datei](./docs/USAGE.md).<br>
Diese richtet sich primär an die tatsächliche Nutzung der Seite.

## Code
Für den Fall, dass du bei dem Projekt mithelfen möchtest, gibt es einen minimalen Crashcourse für Laravel und Vue Funktionalitäten.<br>
[Crashcourse](./docs/CODE.md)

Sollten diese Inhalte und Funktionalitäten bereits bekannt sein, gibt es auch eine Einführung die die jeweiligen Vue-Components.
[Vue-Components](./docs/VUE.md)

## License
[MIT](LICENSE.md)