import { defineStore } from "pinia";

export const useEventStore = defineStore('events', {
    state: () => ({
        events: [],

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
            this.events = data;
        },

        initializeWatchlist(data) {
            this.watchlist = data;

            this.watchlist.forEach((entry, index) => {
                let event = this.removeEventById(entry.event_id);
                this.watchlist[index].event = event[0];
            })
        },

        removeEventById(id) {
            let index = this.events.findIndex(event => {
                return event.id == id;
            });

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
    }
})