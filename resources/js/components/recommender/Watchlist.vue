<template>
    <div class="card mb-3">
        <div class="card-header">
            Watchlist
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
const eventStore = useEventStore();
</script>