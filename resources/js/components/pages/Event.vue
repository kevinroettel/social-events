<template>
    <div v-if="event != null">
        <ImageModal :image="'/storage' + (event.flyer ?? '/fallback-flyer.jpg')" :name="event.name + '-flyer'" />

        <DistanceInfoModal />

        <div class="card w-50 mx-auto">
            <div class="big-flyer-overflow" @click="toggleImage()" data-bs-toggle="modal" data-bs-target="#image-modal">
                <img :src="'/storage' + (event.flyer ?? '/fallback-flyer.jpg')" class="card-img-top">
            </div>
            <div class="card-body">
                <h3 class="text-center mb-3">{{ event.name }}</h3>

                <div class="row">
                    <div class="col">
                        <div class="row">
                            <div class="col">
                                <span>{{ getFormattedDate(event.date) }}<br></span>
                                <span v-if="event.doors">Einlass: {{ event.doors }}<br></span>
                                <span>Beginn: {{ event.begin }}<br></span>
                            </div>
                            <div class="col">
                                <a 
                                    v-if="event.location != null"
                                    @click="showLocationPage(event.location.id)"
                                    class="list-item-link"
                                >
                                    {{ event.location.name }} in {{ event.location.city }}
                                    <br>
                                </a>
                                <span v-if="distance != null">
                                    ~ {{ distance }}km 
                                    <button 
                                        type="button" 
                                        class="small-info-button" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#distance-info-modal"
                                    >
                                        ?
                                    </button>
                                    <br>
                                </span>
                                <span v-if="event.ticketLink != null"><a :href="event.ticketLink" target="_blank">Tickets</a><br></span>
                            </div>
                        </div>
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
                                    class="list-item-link"
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
import ImageModal from '../modals/ImageModal.vue';
import DistanceInfoModal from '../modals/DistanceInfoModal.vue';
import EventStatusButtons from '../layouts/EventStatusButtons.vue';
import CreatePost from '../layouts/CreatePost.vue';
import Posts from '../layouts/Posts.vue';
import { getFormattedDate } from '../helpers/dateFormat.js';
import { useEventStore } from '../../stores/EventStore.js';
import { useLocationStore } from '../../stores/LocationStore.js';
import { useArtistStore } from '../../stores/ArtistStore.js';
import { useUserStore } from '../../stores/UserStore.js';
import { toast } from '../helpers/toast.js';
import { useRoute, useRouter } from 'vue-router';
const eventStore = useEventStore();
const locationStore = useLocationStore();
const artistStore = useArtistStore();
const userStore = useUserStore();

const route = useRoute();
const router = useRouter();

const event = ref(null);
const posts = ref(null);
const watchlistStatus = ref(null);
const showFullImage = ref(false);
const interestedCount = ref(0);
const attendingCount = ref(0);
const distance = ref(null);

const getEventData = () =>  {
    let eventFromStore = eventStore.getEventById(route.params.eventId);

    if (eventFromStore == null)
        eventFromStore = eventStore.getOldEventById(route.params.eventId);

    if (eventFromStore.hasOwnProperty('status')) {
        event.value = eventFromStore.event;
        watchlistStatus.value = eventFromStore.status;
    } else {
        event.value = eventFromStore;
    }

    getArtists()
    getEventPosts()
    getLocation()
}

const getEventPosts = () => {
    axios.get(
        `/events/${route.params.eventId}/posts`
    ).then((response) => {
        posts.value = response.data;
    }).catch((error) => {
        toast(error.message, 'error');
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
        if (entry.user_id != userStore.getUserId) {
            if (entry.status == 'interested') interestedCount.value++;
            else if (entry.status == 'attending') attendingCount.value++;
        }
    });
}

const showLocationPage = (locationId) => {
    router.push(`/venue/${locationId}`);
}

const showArtistPage = (artistId) => {
    router.push(`/kuenstler/${artistId}`);
}

const openEventUpdateForm = () => {
    router.push(`/event-aktualisieren/${route.params.eventId}`);
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

watch(
    watchlistStatus,
    (newStatus, oldStatus) => {
        if (newStatus == 'interested') interestedCount.value++;
        else if (newStatus == 'attending') attendingCount.value++;

        if (oldStatus == 'interested') interestedCount.value--;
        else if (oldStatus == 'attending') attendingCount.value--;
    }
);

onMounted(() => {
    if (route.params.eventId != null) {
        getEventData();
        getWatchlistEntriesCount();
        getDistance();
    }
})
</script>