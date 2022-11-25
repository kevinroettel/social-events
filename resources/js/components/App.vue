<template>
    <div class="page">
        <Navbar
            :currentPage="currentPage"
            @page-change="changePage($event)"
        />

        <div class="main-content">
            <Dashboard
                v-if="currentPage == 'home'"
                :watchlist="watchlist"
            />

            <Events
                v-else-if="currentPage == 'events'"
            />

            <Artists
                v-else-if="currentPage == 'artists'"
                :artistsData="artists"
            />

            <Locations
                v-else-if="currentPage == 'locations'"
                :locationsData="locations"
            />
        </div>
    </div>
</template>
<script setup>
import { reactive, onMounted, ref, inject } from 'vue';
import Navbar from './layouts/Navbar.vue';
import Dashboard from './pages/Dashboard.vue';
import Events from './pages/Events.vue';
import Artists from './pages/Artists.vue';
import Locations from './pages/Locations.vue';

const axios = inject('axios');

const props = defineProps({
    currentuser: {
        required: true,
        type: String,
        default: null
    },
});

const currentPage = ref("home");

const user = reactive({
    id: null,
    name: null,
    email: null,
    profilePicture: null
});

const watchlist = ref([]);
const locations = ref([]);
const artists = ref([]);

const changePage = (page) => {
    currentPage.value = page;
}

const getWatchlistEntries = () => {
    axios.get(
        `/users/${user.id}/watchlists`
    ).then((response) => {
        watchlist.value = response.data;
    }).catch((error) => {
        console.log(error);
    })
}

const getLocations = () => {
    axios.get(
        '/locations'
    ).then((response) => {
        locations.value = response.data;
    }).catch((error) => {
        console.log(error);
    })
}

const getArtists = () => {
    axios.get(
        '/artists'
    ).then((response) => {
        artists.value = response.data;
    }).catch((error) => {
        console.log(error);
    })
}

onMounted(() => {
    let u = JSON.parse(props.currentuser);
    user.id = u.id;
    user.name = u.username;
    user.email = u.email;
    user.profilePicture = u.profile_picture;

    getWatchlistEntries();
    getLocations();
    getArtists();
})
</script>
