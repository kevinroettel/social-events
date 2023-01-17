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
                    if (artist.id == artistId) eventsWithArtist.push(entry.event)
                });
            });

            return eventsWithArtist;
        },

        getPastEventsWithArtist(artistId) {
            return this.oldEvents.filter(event => event.artists.includes(artistId));
        },
    }
})