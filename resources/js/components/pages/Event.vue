<template>
    <div v-if="event != null">
        <ImageModal :image="'/storage' + (event.flyer ?? '/fallback-flyer.jpg')" :name="event.name + '-flyer'" />

        <DistanceInfoModal />

        <div class="card w-50 mx-auto">
            <div class="big-flyer-overflow" @click="toggleImage()" data-bs-toggle="modal" data-bs-target="#image-modal">
                <img :src="'/storage' + (event.flyer ?? '/fallback-flyer.jpg')" class="card-img-top">
            </div>
            <div class="card-body">
                <h3 class="text-center">{{ event.name }}</h3>

                <div class="row">
                    <div class="col">
                        <span>{{ getFormattedDate(event.date) }}<br></span>
                        <span v-if="event.doors">Einlass: {{ event.doors }}<br></span>
                        <span>Beginn: {{ event.begin }}<br></span>
                        <span v-if="event.location != null">{{ event.location.name }} in {{ event.location.city }}<br></span>
                        <span v-if="distance != null">~ {{ distance }}km <button type="button" class="small-info-button" data-bs-toggle="modal" data-bs-target="#distance-info-modal">?</button><br></span>
                        <span v-if="event.ticketLink != null"><a :href="event.ticketLink" target="_blank">Tickets</a><br></span>
                        <hr>
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

                <div class="row">
                    <div class="col"></div>
                    <div class="col">
                        <button
                            type="button"
                            class="btn btn-secondary w-auto"
                            @click="openEventUpdateForm()"
                        >
                            Details ändern
                        </button>
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
import { calculateRouteDistance } from '../helpers/geoCoding.js';
import ImageModal from '../layouts/ImageModal.vue';
import DistanceInfoModal from '../layouts/DistanceInfoModal.vue';
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
    'show-artist-page',
    'show-event-update'
]);

const event = ref(null);
const posts = ref(null);
const watchlistStatus = ref(null);
const showFullImage = ref(false);
const interestedCount = ref(0);
const attendingCount = ref(0);
const distance = ref(null);

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
        if (event.value.artists[index].name == undefined) {
            event.value.artists[index] = artistStore.getArtistById(artistId);
        }
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

const openEventUpdateForm = () => {
    emit('show-event-update', props.eventId);
}

const getDistance = () => {
    if (userStore.getUserCity != null) {
        calculateRouteDistance(
            userStore.getUserAddress, 
            userStore.getUserCity, 
            event.value.location.name, 
            event.value.location.city
        ).then((response) => {
            distance.value = response
        })
    }
}

onMounted(() => {
    if (props.eventId != null) {
        getEventData();
        getWatchlistEntriesCount();
        getDistance();
    }
})
</script>