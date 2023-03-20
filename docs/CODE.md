# Code
Dieses Dokument soll eine kurze Einführung in den Source Code geben, damit mögliche Beitragende nicht erst alle an Frameworks lernen müssen.

Hier dennoch die Verlinkungen zu den verschiedenen Dokumentationen:<br>
[Laravel](https://laravel.com/docs/9.x)<br>
[Vue](https://vuejs.org/guide/introduction.html)<br>
[Vue Pinia](https://pinia.vuejs.org/core-concepts/)<br>
[Vue Router](https://router.vuejs.org/guide/)<br>
[Bootstrap](https://getbootstrap.com/docs/5.3/getting-started/introduction/)<br>

## Domain Model
![](./img/domainmodel.png)

## Datenstruktur
Unter ./database/migrations befinden sich Migrations-Dateien, in welchen die Struktur der Daten inklusive Typen festgelegt werden.<br>
Die Model-Klassen zu den jeweiligen Tabellen befinden sich in ./app/Models. Hier werden alle Model-Relationen festgelegt.<br>
In ./app/Http/Controllers befinden sich die Controller der Models mit CRUD Operationen und weiteren Funktionalitäten.