import { defineStore } from "pinia";

export const useArtistStore = defineStore('artist', {
    state: () => ({
        artists: []
    }),

    getters: {
        getArtists(state) {
            return state.artists;
        },
    },

    actions: {
        initializeArtists(data) {
            this.artists = data;
        },

        addNewArtist(artist) {
            this.artists.push(artist);
        },

        getArtistById(id) {
            return this.artists.filter(a => a.id == id)[0];
        },

        getArtistsByString(string) {
            return this.artists.filter(artist => artist.name.toLowerCase().includes(string.toLowerCase()));
        }
    },
})