<template>
    <div class="card mb-3">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    Watchlist
                </div>
                <div class="col text-end">
                    <a @click="showExtendedWatchlistPage()" class="btn btn-primary">></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div v-if="eventStore.isWatchlistEmpty">
                <p>Zurzeit befinden sich keine Einträge in deiner Watchlist. Füge welche hinzu!</p>
            </div>

            <EventTeaserCarousel
                v-else
                :events="eventStore.getWatchlist"
            />
        </div>
    </div>
</template>
<script setup>
import EventTeaserCarousel from '../layouts/EventTeaserCarousel.vue';
import { useEventStore } from "../../stores/EventStore";
import { router } from '../../router';
const eventStore = useEventStore();

const showExtendedWatchlistPage = () => {
    router.push(`/watchlist`);
}
</script>