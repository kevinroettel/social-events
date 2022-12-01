import { defineStore } from "pinia";

export const useUserStore = defineStore('user', {
    state: () => ({
        user: {
            id: null,
            name: null,
            email: null,
            profilePicture: null,
        }
    }),

    getters: {
        getUserId(state) { return state.user.id },
        getUserName(state) { return state.user.name },
        getUserEmail(state) { return state.user.email },
        getUserPicture(state) { return state.user.profilePicture }
    },

    actions: {
        initializeUser(data) {
            this.user.id = data.id;
            this.user.name = data.username;
            this.user.email = data.email;
            this.user.profilePicture = data.profile_picture;
        }
    }
});