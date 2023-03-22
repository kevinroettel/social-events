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
Unter **/database/migrations** befinden sich Migrations-Dateien, in welchen die Struktur der Models festgelegt werden. Dazu zählen der Typ, der Name und weitere zusätzliche Attribute wie Unique oder Nullable. Ebenfalls können hier vorhandene Fremdbeziehungen mit Forgein-Key-Constraints hinterlegt werden.<br>
Im Falle des Künstlers sieht dies folgendermaßen aus:
````php
Schema::create('artists', function (Blueprint $table) {
    $table->id();
    //      Typ     Name    Zusätze
    $table->string('name')->unique();
    $table->timestamps();
});
````

Die eigentlichen Model-Klassen befinden sich in **/app/Models**. Hier werden nochmals die Relationen in Code-Form aufgeschrieben. Diese setzen sich aus dem Namen der Beziehung, der Art und der Referenzierten Klasse zusammen.<br>
Hier einmal die Beziehung vom Künstler zu Tags:
````php
public function tags() {
    return $this->belongsToMany(Tag::class);
}
````

Bei Änderungen, insbesondere beim Hinzufügen oder Umbennen von Attributen, müssen im Model auch die Attributsnamen aktualisiert werden. Dies geschieht über das ``$fillable``-Array, welches eine Sicherheitsmaßnahme für Mass-Assignment-Operationen ist.

## Datenmanipulation
In **/app/Http/Controllers** befinden sich die Controller der Models mit CRUD Operationen und weiteren Funktionalitäten. Eine CRUD-Operation sollte möglichst immer nach einem bestimmten Ablauf aufgebaut sein. <br>
Zuerst die Validierung von Request-Daten, ggf. Fehlerfälle abarbeiten, dann den Datensatz erstellen oder aktualisieren und zurückgeben.<br>
````php
public function createArtist(Request $request) {
    // Validierung
    $request = $request->validate([
        'name' => 'required|unique:artists|string'
    ]);

    // Erstellung
    $artist = Artist::create([
        'name' => $request['name']
    ]);

    // Zurückgeben
    return $artist;
}
````

## Routen
Unter **/routes/web.php** befinden sich Web-Routen, welche intern vom User genutzt werden. Sie werden genutzt für den Datenaustausch zwischen Frontend und Backend.<br>
Jede Route innerhalb der Gruppen "Auth", kann nur mit einem eingeloggten User genutzt werden. Die Routen über der Gruppe sind die Login und Registrierungs-Routen.<br>
Außerdem besteht auch eine inhaltliche Trennung zu API-Routen, sollten welche existieren.<br>
Eine Web-Route besteht aus einer Route-Methode wie *POST* oder *GET*, der URL und möglichen URL-Parametern, sowie einer auszuführenden Funktion und einem ggf. benötigtem Controller-Verweiß, welcher die Funktion beherbergt.
````php
Route::get('/artists', [ArtistController::class, 'getArtists']);
````

## Einstiegspunkt Frontend
In **/resources/views/app.blade.php** ist die programmatische Startseite zu finden. Hier werden auch das kompilierte JavaScript und CSS eingebunden.<br>
Ebenfalls wird hier überprüft, ob der Nutzer eingelogged ist, und ihm somit entweder das Dashboard oder die Login-Seite zu zeigen.<br>
Die Login-Seite wird über die Template-Engine Blade geladen. Das Dashboard hingegen über den Vue-Component namens *app*. Dieser ist in ./resources/js/components/App.vue zu finden<br>

In diesem Component befindet sich, unter anderen, ein weiterer Component namens ``<router-view>``. Dieser ist Teil von Vue-Router und an sich nur ein Platzhalter für weitere Components, welche abhängig von der URL geladen werden sollen.<br>
Die Zuweisung von URLs und Components ist in **/resources/js/router.js** zu finden. Durch diese Nutzung von Vue-Router lässt sich auch eine Browser-Historie erstellen, damit auch z.B. der Zurück-Button genutzt werden kann.<br>

## Storage
Im App-Component werden außerdem zu Anfang alle benötigten Daten per HTTP-Requests geladen und je nach Zugehörigkeit in einem eigenen Pinia-Store gespeichert. Diese Stores ermöglichen es, Daten über Components hinweg zu verfügbar zu machen, ohne sie mit Events oder Props weitergeben zu müssen.<br>
Innerhalb eines Stores gibt es den *state*, in welchem die Daten gespeichert werden, *getters*-Funktionen über die auf den State zugegriffen werden kann und *actions*-Funktionen welche den State manipulieren.<br>
Zurzeit gibt es vier verschiedene Stores die jeweils zugehörige Daten speichern. 
- ArtistStore für rein die Künstlernamen und -IDs
- EventStore für alle Veranstaltungen (unterteilt in Aktuell und Vergangen) sowie alle Watchlisteinträge
- LocationStore für alle Daten der Venues
- UserStore für die vollständigen Daten des eingeloggten Nutzers, die Freunde und deren Watchlisteinträge, Freundschaftsanfragen und minimale Daten von allen anderen Nutzern

## Components
Vue-Components bestehen aus zwei Haupt-Blöcken. Einmal das Template, welches zur Laufzeit dem Nutzer gerendert wird. Und dann der Script Teil, in welchem Daten abgefragt und bearbeitet werden.

### Template
Das Template eines Components besteht grundsätzlich aus normalen HTML-Tags mit den gängigen Attributen wie z.B. *id, class, type*.
Durch Vue kommen aber Kontrollstrukturen wie Schleifen und Bedingungen hinzu. Somit können (hier werden Posts als Beispiel genutzt) sich wiederholende HTML-Elemente oder ganze Blöcke mithilfe einer Iteration dargestellt werden:
````html
<div  
    v-for="(post, index) in posts"
    :key="index"
    class="accordion-item"
>
    ...
</div>
````