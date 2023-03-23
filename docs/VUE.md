# Auflistung und Erklärung Components
## /pages/
### Account
- Account Einstellungen
- Daten speichern durch FormData Objekt
- so kann auch einfacher ein Profilbild mitgegeben und gespeichert werden

### Artist
- Artist Page
- immer verbunden mit /kuenstler/:id URL, somit automatisches laden von Artist-Daten bei Page-Refresh
- Tag-System und EventList eingebunden

### Dashboard
- Startseite eingeloggte Benutzer
- Einbindung von verschiedenen Recommender Systemen

### Event
- Detail-Ansicht eines Events
- immer verbunden mit /event/:id URL, somit automatisches laden von Event-Daten bei Page-Refresh
- zwei eingebundene Modals, für weitere Informationen
- Verlinkungen zu Location- und Artist-Pages
- Entfernungsberechnung durch */js/components/helpers/geoCoding.js*
- EventStatusButtons für ändern von Watchlistentry-Status
- Anzahl an Interessierten und Zusagen, mit Update bei Status Änderungen
- Verlinkung zu EventForm für Updates
- Anzeige und Erstellung von Beiträgen und Kommentaren

### EventForm
- Formular für Event-Erstellung und -Update
- Eingebundenes Formular für Location-Erstellung
- Bei Update einbinden von LineUpModal
- v-select für Locations
- v-select für Artists mit Mehrfachauswahl, inklusive Erstellung und Notification bei ähnlichen Namen
- 

### Friends

### Location

### SearchResults


## /modals/
### DistanceInfoModal

### ImageModal

### LineUpModal

### LocationFormModal

## /layouts/
### ArtistTags

### CreatePost

### EventList

### EventPreview

### EventStatusButtons

### EventTeaser

### EventTeaserCarousel

### InterestedList

### LineUpArtist

### Navbar

### PostContent

### Posts

### Toast

### UserBox

## /recommender/
### AllEvents

### CosineSimilarity

### FriendWatchlists

### JaccardIndex

### Watchlist