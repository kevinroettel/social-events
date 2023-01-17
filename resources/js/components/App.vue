<template>
    <div class="page" v-if="dataLoaded">
        <Navbar
            :currentPage="currentPage"
            @page-change="changePage($event)"
        />

        <div class="main-content">

            <Dashboard
                v-if="currentPage == 'home'"
                @show-event-page="showEventPage($event)"
            />

            <Event
                v-else-if="currentPage == 'event'"
                :eventId="showEvent"
                @show-artist-page="showArtistPage($event)"
            />

            <EventForm
                v-else-if="currentPage == 'eventform'"
                @event-created="addEvent($event)"
            />

            <Artist
                v-else-if="currentPage == 'artist'"
                :artistId="showArtist"
            />

            <Locations
                v-else-if="currentPage == 'locations'"
            />

            <Friends
                v-else-if="currentPage == 'friends'"
            />

            <Account
                v-else-if="currentPage == 'account'"
            />

            <Toast />
        </div>
    </div>
    <div v-else class="spinner-border spinner-middle" role="status"></div>
</template>
<script setup>
import { reactive, onMounted, ref, inject, computed } from 'vue';
import Navbar from './layouts/Navbar.vue';
import Toast from './layouts/Toast.vue';
import Dashboard from './pages/Dashboard.vue';
import EventForm from './pages/EventForm.vue';
import Event from './pages/Event.vue';
import Artist from './pages/Artist.vue';
import Locations from './pages/Locations.vue';
import Friends from './pages/Friends.vue';
import Account from './pages/Account.vue';

import { useLocationStore } from '../stores/LocationStore.js';
import { useArtistStore } from '../stores/ArtistStore.js';
import { useUserStore } from '../stores/UserStore.js';
import { useEventStore } from '../stores/EventStore.js';
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

const currentPage = ref("home");

const showEvent = ref(null);
const showArtist = ref(null);

const dataDone = reactive({
    events: false,
    watchlist: false,
    locations: false,
    artists: false,
    users: false,
    friends: false
});

const changePage = (page) => {
    currentPage.value = page;
}

const showEventPage = (eventId) => {
    currentPage.value = "event";
    showEvent.value = eventId;
}

const showArtistPage = (artistId) => {
    currentPage.value = "artist";
    showArtist.value = artistId;
}

const dataLoaded = computed(() => {
    return dataDone.events && 
        dataDone.watchlist && 
        dataDone.locations && 
        dataDone.artists && 
        dataDone.users && 
        dataDone.friends;
})

const getEvents = () => {
    axios.get(
        '/events'
    ).then((response) => {
        eventStore.initializeEvents(response.data);
        dataDone.events = true;
        getWatchlistEntries();
    }).catch((error) => {
        console.log(error);
    });
}

const addEvent = (event) => {
    currentPage.value = "home";
    event.location = locationStore.getLocationById(event.location_id);
    eventStore.addNewEvent(event)
}

const getWatchlistEntries = () => {
    axios.get(
        `/users/${userStore.getUserId}/watchlists`
    ).then((response) => {
        eventStore.initializeWatchlist(response.data);
        dataDone.watchlist = true
    }).catch((error) => {
        console.log(error);
    })
}

const getLocations = () => {
    axios.get(
        '/locations'
    ).then((response) => {
        locationStore.initializeLocations(response.data);
        dataDone.locations = true
    }).catch((error) => {
        console.log(error);
    })
}

const getArtists = () => {
    axios.get(
        '/artists'
    ).then((response) => {
        artistStore.initializeArtists(response.data);
        dataDone.artists = true
    }).catch((error) => {
        console.log(error);
    })
}

const getUsers = () => {
    axios.get(
        '/users'
    ).then((response) => {
        userStore.initializeUsers(response.data);
        dataDone.users = true
    }).catch((error) => {
        console.log(error)
    })
}

const getFriends = () => {
    axios.get(
        `/users/${userStore.getUserId}/friends`
    ).then((response) => {
        userStore.initializeFriends(response.data);
        dataDone.friends = true
    }).catch((error) => {
        console.log(error);
    })
}

onMounted(() => {
    let user = JSON.parse(props.currentuser);
    userStore.initializeUser(user)

    getEvents();
    getArtists();
    getLocations();
    getUsers();
    getFriends();
})
</script>
