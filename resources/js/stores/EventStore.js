import { defineStore } from "pinia";

export const useEventStore = defineStore('events', {
    state: () => ({
        events: [],

        oldEvents: [],

        watchlist: [],

    }),

    getters: {
        getAllEvents(state) {
            return state.events;
        },

        getWatchlist(state) {
            return state.watchlist;
        },

        isWatchlistEmpty(state) {
            return state.watchlist.length == 0;
        }
    },

    actions: {
        initializeEvents(data) {
            let today = new Date();
            today.setHours(0, 0, 0, 0);

            data.forEach((event) => {
                let eventDate = new Date(event.date);
                if (eventDate < today) this.oldEvents.push(event);
                else this.events.push(event);
            })
        },

        initializeWatchlist(data) {
            this.watchlist = data;

            for (let index = 0; index < this.watchlist.length; index++) {
                let event = this.removeEventById(this.watchlist[index].event_id);

                if (event == null) this.watchlist.splice(index--, 1);
                else this.watchlist[index].event = event[0];
            }
        },

        removeEventById(id) {
            let index = this.events.findIndex(event => {
                return event.id == id;
            });

            if (index == -1) return null;
            return this.events.splice(index, 1);
        },

        addNewEvent(event) {
            this.events.push(event);
        },

        getEventById(id) {
            let event = this.events.filter(e => e.id == id);

            if (event.length == 0) {
                event = this.watchlist.filter(e => e.event_id == id);
            }

            return event[0];
        },

        updateEventData(eventData) {
            this.events.forEach((event, index) => {
                if (event.id == eventData.id) {
                    let location = event.location;
                    let watchlistEntries = event.watchlists;
                    this.events[index] = eventData;
                    this.events[index].location = location;
                    this.events[index].watchlists = watchlistEntries;
                }
            });

            this.watchlist.forEach((entry, index) => {
                if (entry.event.id == eventData.id) {
                    let location = entry.event.location;
                    let watchlistEntries = entry.event.watchlists;
                    this.watchlist[index].event = eventData
                    this.watchlist[index].event.location = location;
                    this.watchlist[index].event.watchlists = watchlistEntries;
                }
            })
        },

        updateWatchlist(updatedEntry) {
            let isNewEntry = true;
            
            this.watchlist.forEach((entry, index) => {
                if (entry.id == updatedEntry.id) {
                    this.watchlist[index].status = updatedEntry.status;
                    isNewEntry = false;
                }
            })

            if (isNewEntry) {
                let event = this.removeEventById(updatedEntry.event_id);
                updatedEntry.event = event[0];
                this.watchlist.push(updatedEntry);
            }
        },

        removeFromWatchlist(eventId) {
            let index = this.watchlist.findIndex(entry => {
                return entry.event_id == eventId;
            })

            let event = this.watchlist[index].event;
            this.events.push(event);
            this.watchlist.splice(index, 1);
        },

        getEventsWithArtist(artistId) {
            let eventsWithArtist = [];

            this.events.forEach(event => {
                event.artists.forEach(artist => {
                    if (Number.isInteger(artist) && artist == artistId) {
                        eventsWithArtist.push(event);
                    } else {
                        if (artist.id == artistId) {
                            eventsWithArtist.push(event);
                        }
                    }
                });
            });
            
            this.watchlist.forEach(entry => {
                entry.event.artists.forEach(artist => {
                    if ((Number.isInteger(artist) && artist == artistId) || artist.id == artistId) 
                        eventsWithArtist.push(entry.event)
                });
            });

            return eventsWithArtist;
        },

        getPastEventsWithArtist(artistId) {
            return this.oldEvents.filter(event => event.artists.includes(artistId));
        },

        getEventsByString(string) {
            let eventsWithString = this.events.filter(event => event.name.toLowerCase().includes(string.toLowerCase()));
            let oldEventsWithString = this.oldEvents.filter(event => event.name.toLowerCase().includes(string.toLowerCase()));
            let watchlistWithString = this.watchlist.filter(entry => entry.event.name.toLowerCase().includes(string.toLowerCase()));

            return eventsWithString.concat(oldEventsWithString, watchlistWithString);
        },

        getEventsInLocation(locationId) {
            let eventsInLocation = [];

            this.events.forEach(event => {
                if (event.location_id == locationId) eventsInLocation.push(event);
            });
            
            this.watchlist.forEach(entry => {
                if (entry.event.location_id == locationId) eventsInLocation.push(entry.event);
            });

            return eventsInLocation;
        },

        getPastEventsInLocation(locationId) {
            return this.oldEvents.filter(event => event.location_id == locationId);
        }
    }
})