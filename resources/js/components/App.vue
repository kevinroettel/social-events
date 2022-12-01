<template>
    <div class="page">
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
            />

            <EventForm
                v-else-if="currentPage == 'eventform'"
                @event-created="addEvent($event)"
            />

            <Artists
                v-else-if="currentPage == 'artists'"
            />

            <Locations
                v-else-if="currentPage == 'locations'"
            />
        </div>
    </div>
</template>
<script setup>
import { reactive, onMounted, ref, inject } from 'vue';
import Navbar from './layouts/Navbar.vue';
import Dashboard from './pages/Dashboard.vue';
import EventForm from './pages/EventForm.vue';
import Event from './pages/Event.vue';
import Artists from './pages/Artists.vue';
import Locations from './pages/Locations.vue';

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

const changePage = (page) => {
    currentPage.value = page;
}

const showEventPage= (eventId) => {
    currentPage.value = "event";
    showEvent.value = eventId;
}

const getEvents = () => {
    axios.get(
        '/events'
    ).then((response) => {
        eventStore.initializeEvents(response.data);
        getWatchlistEntries();
    }).catch((error) => {
        console.log(error);
    });
}

const addEvent = (event) => {
    currentPage.value = "home";
    eventStore.addNewEvent(event);
}

const getWatchlistEntries = () => {
    axios.get(
        `/users/${userStore.getUserId}/watchlists`
    ).then((response) => {
        eventStore.initializeWatchlist(response.data);
    }).catch((error) => {
        console.log(error);
    })
}

const getLocations = () => {
    axios.get(
        '/locations'
    ).then((response) => {
        locationStore.initializeLocations(response.data);
    }).catch((error) => {
        console.log(error);
    })
}

const getArtists = () => {
    axios.get(
        '/artists'
    ).then((response) => {
        artistStore.initializeArtists(response.data);
    }).catch((error) => {
        console.log(error);
    })
}

onMounted(() => {
    let user = JSON.parse(props.currentuser);
    userStore.initializeUser(user)

    getEvents();
    getLocations();
    getArtists();
})
</script>
