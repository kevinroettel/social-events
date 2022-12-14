import { defineStore } from "pinia";

export const useUserStore = defineStore('user', {
    state: () => ({
        user: {
            id: null,
            username: null,
            email: null,
            profile_picture: null,
        },

        friends: [],

        users: [],

        requested: [],
    }),

    getters: {
        getUserId(state) { return state.user.id },
        getUserName(state) { return state.user.username },
        getUserEmail(state) { return state.user.email },
        getUserPicture(state) { return state.user.profile_picture },
        
        getFriends(state) { return state.friends },
        getFriendRequests(state) { return state.requested },
    },

    actions: {
        initializeUser(data) {
            this.user.id = data.id;
            this.user.username = data.username;
            this.user.email = data.email;
            this.user.profile_picture = data.profile_picture;
        },

        initializeFriends(data) {
            this.friends = data
        },

        initializeUsers(data) {
            this.users = data
        },

        initializeRequests(data) {
            this.requested = data;
        },

        getUsersALike(query) {
            return this.users.filter(user => 
                user.username.toLowerCase().indexOf(query.toLowerCase()) >= 0
            );
        },

        moveUserToRequested(userId) {
            this.users = this.users.filter(user => user.id != userId);
        },

        removeFriend(userId) {
            let friend = this.friends.filter(user => user.id == userId);
            this.users.push(friend[0]);
            this.friends = this.friends.filter(user => user.id != userId)
        },

        removeFriendRequest(userId) {
            this.requested = this.requested.filter(user => user.id != userId);
        },

        acceptFriend(userId) {
            let friend = this.requested.filter(user => user.id == userId);
            this.friends.push(friend[0]);
            this.requested = this.requested.filter(user => user.id != userId);
        }
    }
});