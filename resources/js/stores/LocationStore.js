import { defineStore } from "pinia";

export const useLocationStore = defineStore('location', {
    state: () => ({
        locations: []
    }),

    getters: {
        getLocations(state) {
            return state.locations;
        },
    },

    actions: {
        initializeLocations(data) {
            this.locations = data;
        },

        addNewLocation(location) {
            this.locations.push(location);
        },

        getLocationById(id) {
            return this.locations.filter(l => l.id == id)[0];
        },

        getLocationsByString(string) {
            return this.locations.filter(location => location.name.toLowerCase().includes(string.toLowerCase()));
        }
    },
})