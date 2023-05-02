<template>
    <div class="page" v-if="dataLoaded">
        <Navbar />

        <div class="main-content">
            <!-- includes all main pages. to see every page go to router.js -->
            <router-view></router-view>

            <Toast />
        </div>
    </div>
    <div v-else class="spinner-border spinner-middle" role="status"></div>
</template>
<script setup>
import { reactive, onMounted, inject, computed } from 'vue';
import Navbar from './layouts/Navbar.vue';
import Toast from './layouts/Toast.vue';
import { toast } from "./helpers/toast";

import { useLocationStore } from '../stores/LocationStore.js';
import { useArtistStore } from '../stores/ArtistStore.js';
import { useUserStore } from '../stores/UserStore.js';
import { useEventStore } from '../stores/EventStore.js';
import { getLongAndLat } from './helpers/geoCoding';
const locationStore = useLocationStore();
const artistStore = useArtistStore();
const userStore = useUserStore();
const eventStore = useEventStore();

const axios = inject('axios');

const props = defineProps({
    currentuser: {
        required: true,
        type: String,
        default: null
    },
});

const dataDone = reactive({
    events: false,
    watchlist: false,
    locations: false,
    artists: false,
    users: false,
    friends: false
});

const dataLoaded = computed(() => {
    return dataDone.events && 
        dataDone.watchlist && 
        dataDone.locations && 
        dataDone.artists && 
        dataDone.users && 
        dataDone.friends;
})

const getUserCoordinates = () => {
    getLongAndLat(
        null,
        userStore.getUserCity,
        userStore.getUserAddress
    ).then((response) => {
        if (response != null) {
            userStore.setCoordinates(response.latitude, response.longitude);
        }
    }).catch((error) => {
        console.log(error)
    })
}

const getEvents = () => {
    axios.get(
        '/events'
    ).then((response) => {
        eventStore.initializeEvents(response.data);
        dataDone.events = true;
        getWatchlistEntries();
    }).catch((error) => {
        toast(error.message, 'error');
    });
}

const getWatchlistEntries = () => {
    axios.get(
        `/users/${userStore.getUserId}/watchlists`
    ).then((response) => {
        eventStore.initializeWatchlist(response.data);
        dataDone.watchlist = true
    }).catch((error) => {
        toast(error.message, 'error');
    })
}

const getLocations = () => {
    axios.get(
        '/locations'
    ).then((response) => {
        locationStore.initializeLocations(response.data);
        dataDone.locations = true
    }).catch((error) => {
        toast(error.message, 'error');
    })
}

const getArtists = () => {
    axios.get(
        '/artists'
    ).then((response) => {
        artistStore.initializeArtists(response.data);
        dataDone.artists = true
    }).catch((error) => {
        toast(error.message, 'error');
    })
}

const getUsers = () => {
    axios.get(
        '/users'
    ).then((response) => {
        userStore.initializeUsers(response.data);
        dataDone.users = true
    }).catch((error) => {
        toast(error.message, 'error');
    })
}

const getFriends = () => {
    axios.get(
        `/users/${userStore.getUserId}/friends`
    ).then((response) => {
        userStore.initializeFriends(response.data);
        dataDone.friends = true
    }).catch((error) => {
        toast(error.message, 'error');
    })
}

onMounted(() => {
    let user = JSON.parse(props.currentuser);
    userStore.initializeUser(user)

    getUserCoordinates();
    getEvents();
    getArtists();
    getLocations();
    getUsers();
    getFriends();
})
</script>
