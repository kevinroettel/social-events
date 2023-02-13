<template v-cloak>
    <div>
        <Watchlist />
        <SimilarEvents />
        <FriendWatchlists />


        <div class="card">
            <div class="card-header">
                Alle Events
            </div>
            <div class="card-body">
                <div v-if="eventStore.getAllEvents.length == 0">
                    <p>Zurzeit existieren keine aktuellen Events. Erstelle ein paar!</p>
                </div>

                <EventTeaserCarousel 
                    v-else
                    :events="eventStore.getAllEvents"
                />
            </div>
        </div>
    </div>
</template>
<script setup>
import Watchlist from '../recommender/Watchlist.vue';
import SimilarEvents from '../recommender/SimilarEvents.vue';
import FriendWatchlists from '../recommender/FriendWatchlists.vue'
import EventTeaserCarousel from '../layouts/EventTeaserCarousel.vue';
import { useEventStore } from '../../stores/EventStore.js';
const eventStore = useEventStore();
</script>