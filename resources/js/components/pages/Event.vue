<template>
    <div v-if="event != null">
        <ImageModal :image="'/storage' + (event.flyer ?? '/fallback-flyer.jpg')" :name="event.name + '-flyer'" />

        <div class="card w-50 mx-auto">
            <div class="big-flyer-overflow" @click="toggleImage()" data-bs-toggle="modal" data-bs-target="#image-modal">
                <img :src="'/storage' + (event.flyer ?? '/fallback-flyer.jpg')" class="card-img-top">
            </div>
            <div class="card-body">
                <h3 class="text-center">{{ event.name }}</h3>

                <div class="row">
                    <div class="col">
                        <span>{{ getFormattedDate(event.date) }}</span><br>
                        <span v-if="event.doors">Einlass: {{ event.doors }}</span><br>
                        <span>Beginn: {{ event.begin }}</span><br>
                        <span v-if="event.location != null">{{ event.location.name }} in {{ event.location.city }}</span><br>
                        <span>{{ getDistance() }}</span><br>
                        <span v-if="event.ticketLink != null"><a :href="event.ticketLink" target="_blank">Tickets</a></span><br><hr>
                    </div>
                    <div class="col">
                        <EventStatusButtons
                            :event="event.id"
                            :status="watchlistStatus"
                            @status-updated="watchlistStatus = $event"
                        />

                        <br><span>{{ interestedCount }} Personen sind interessiert.</span><br>
                        <span>{{ attendingCount }} Personen nehmen teil.</span><br><hr>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col pre-wrap">{{ event.description }}</div>
                    <div class="col">
                        Artists
                        <p v-if="event.artists.length != 0">Line-up:</p>
                        <ul>
                            <li 
                                v-for="(artist, index) in event.artists"
                                :key="index"
                            >
                                <a 
                                    @click="showArtistPage(artist.id)"
                                    class="event-line-up-artist-link"
                                >
                                    {{ artist.name }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <hr>

                <div class="row user-posts">
                    <CreatePost 
                        :eventId="event.id"
                        @created-post="addPost($event)"
                    />

                    <Posts
                        v-if="posts != null"
                        :postsData="posts"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref, watch } from '@vue/runtime-core';
import * as geo from '../helpers/geoCoding.js';
import ImageModal from '../layouts/ImageModal.vue';
import EventStatusButtons from '../layouts/EventStatusButtons.vue';
import CreatePost from '../layouts/CreatePost.vue';
import Posts from '../layouts/Posts.vue';
import { getFormattedDate } from '../helpers/dateFormat.js';
import { useEventStore } from '../../stores/EventStore.js';
import { useLocationStore } from '../../stores/LocationStore.js';
import { useArtistStore } from '../../stores/ArtistStore.js';
import { useUserStore } from '../../stores/UserStore.js';
const eventStore = useEventStore();
const locationStore = useLocationStore();
const artistStore = useArtistStore();
const userStore = useUserStore();

const props = defineProps({
    eventId: {
        required: true,
        type: [Number, null],
        default: null
    }
});

const emit = defineEmits([
    'show-artist-page'
]);

const event = ref(null);
const posts = ref(null);
const watchlistStatus = ref(null);
const showFullImage = ref(false);
const interestedCount = ref(0);
const attendingCount = ref(0);

const getEventData = () =>  {
    let eventFromStore = eventStore.getEventById(props.eventId);
    if (typeof eventFromStore.status !== "undefined") {
        event.value = eventFromStore.event;
        watchlistStatus.value = eventFromStore.status;
    } else {
        event.value = eventFromStore;
    }

    getEventPosts()
    getLocation()
    getArtists()
}

const getEventPosts = () => {
    axios.get(
        `/events/${props.eventId}/posts`
    ).then((response) => {
        posts.value = response.data;
    }).catch((error) => {
        console.log(error);
    })
}

const addPost = (newPost) => {
    posts.value.unshift(newPost);
}

const getLocation = () => {
    event.value.location = locationStore.getLocationById(event.value.location_id);
}

const getArtists = () => {
    event.value.artists.forEach((artistId, index) => {
        event.value.artists[index] = artistStore.getArtistById(artistId);
    });
}

const toggleImage = () => {
    showFullImage.value = !showFullImage.value
}

const getWatchlistEntriesCount = () => {
    event.value.watchlists.forEach((entry) => {
        if (entry.status == 'interested') interestedCount.value++;
        else if (entry.status == 'attending') attendingCount.value++;
    });
}

const showArtistPage = (artistId) => {
    emit('show-artist-page', artistId);
}

const getDistance = () => {
    let origin = geo.getCoordinatesOfAddress(userStore.getUserAddress, userStore.getUserCity);
    let destination = geo.getCoordinatesOfPlace(event.value.location.name, event.value.location.city);
    let distance = geo.getDistanceBetweenTwoPoints(origin, destination);
    return distance
}

onMounted(() => {
    if (props.eventId != null) getEventData();
    getWatchlistEntriesCount();
})
</script>