<template>
    <div v-if="searchDone">
        <h3>Suchanfrage zu: "{{ route.params.query }}"</h3>

        <div class="row">
            <div class="col">
                <h5>Künstler</h5>
                <p v-if="result.artists.length == 0">Keine Künstler gefunden.</p>
                <ul>
                    <li v-for="(artist, index) in result.artists" :key="index">
                        <a @click="showArtistPage(artist.id)" class="list-item-link">
                            {{ artist.name }}
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col">
                <h5>Events</h5>
                <p v-if="result.events.length == 0">Keine Events gefunden.</p>
                <ul>
                    <li v-for="(event, index) in result.events" :key="index">
                        <a @click="showEventPage(event.hasOwnProperty('event') ? event.event.id : event.id)" class="list-item-link">
                            {{ event.name ?? event.event.name }}
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="col">
                <h5>Venues</h5>
                <p v-if="result.locations.length == 0">Keine Venues gefunden.</p>
                <ul>
                    <li v-for="(location, index) in result.locations" :key="index">
                        <a @click="showLocationPage(location.id)" class="list-item-link">
                            {{ location.name }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, reactive, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useArtistStore } from '../../stores/ArtistStore.js';
import { useEventStore } from '../../stores/EventStore.js';
import { useLocationStore } from '../../stores/LocationStore.js';

const artistStore = useArtistStore();
const eventStore = useEventStore();
const locationStore = useLocationStore();

const router = useRouter();
const route = useRoute();

const emit = defineEmits([
    'show-artist-page',
    'show-event-page',
    'show-location-page',
]);

const searchDone = ref(false);

const result = reactive({
    artists: null,
    events: null,
    locations: null
});

const search = () => {
    result.artists = artistStore.getArtistsByString(route.params.query);
    result.events = eventStore.getEventsByString(route.params.query);
    result.locations = locationStore.getLocationsByString(route.params.query);
    searchDone.value = true;
}

const showArtistPage = (artistId) => router.push(`/kuenstler/${artistId}`);
const showEventPage = (eventId) => router.push(`/event/${eventId}`);
const showLocationPage = (locationId) => router.push(`/venue/${locationId}`);

watch(
    () => route.params.query,
    (newQuery, oldQuery) => {
        if ((newQuery != oldQuery) && newQuery != null) search();
    }
);

onMounted(() => {
    search()
})
</script>