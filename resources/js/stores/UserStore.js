import { defineStore } from "pinia";

export const useUserStore = defineStore('user', {
    state: () => ({
        user: {
            id: null,
            username: null,
            email: null,
            profile_picture: null,
            address: null,
            city: null,
            latitude: null,
            longitude: null
        },

        friends: [],

        users: [],

        requested: [],
    }),

    getters: {
        getUserId(state) { return state.user.id },
        getUserName(state) { return state.user.username },
        getUserEmail(state) { return state.user.email },
        getUserAddress(state) { return state.user.address },
        getUserCity(state) { return state.user.city },
        getUserPicture(state) { return state.user.profile_picture },
        getUserCoordinates(state) { return {'latitude': state.user.latitude, 'longitude': state.user.longitude} },

        getFriends(state) { return state.friends },
        getFriendRequests(state) { return state.requested },

        getFriendWatchlistEntries(state) {
            let entries = [];

            state.friends.forEach(friend => {
                entries.push(...friend.watchlist)
            });

            return entries;
        },

        getAllUsersWithWatchlistEntries(state) {
            return state.friends.concat(state.users);
        }
    },

    actions: {
        initializeUser(data) {
            this.user.id = data.id;
            this.user.username = data.username;
            this.user.email = data.email;
            this.user.profile_picture = data.profile_picture;
            this.user.address = data.address;
            this.user.city = data.city;
        },

        setCoordinates(latitude, longitude) {
            this.user.latitude = latitude;
            this.user.longitude = longitude;
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

        getUserById(id) {
            let user = this.users.filter(user => user.id == id);
            if (user.length != 0) {
                return {
                    username: user[0].username,
                    profile_picture: user[0].profile_picture,
                    isFriend: false,
                }
            }

            let friend = this.friends.filter(friend => friend.id == id);
            if (friend.length != 0) {
                return {
                    username: friend[0].username,
                    profile_picture: friend[0].profile_picture,
                    isFriend: true,
                }
            }

            return {
                username: this.user.username,
                profile_picture: this.user.profile_picture,
                isFriend: false
            }
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