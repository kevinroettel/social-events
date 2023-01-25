<template v-cloak>
    <div>
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
                    @show-event-page="showEventPage($event)"
                />
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Vorschläge (erstmal Platzhalter, soll aber schonmal im Dashboard zu sehen sein)
            </div>
            <div class="card-body">
                <EventTeaserCarousel 
                    :events="eventStore.getAllEvents"
                    @show-event-page="showEventPage($event)"
                />
            </div>
        </div>
    </div>
</template>
<script setup>
import EventTeaserCarousel from '../layouts/EventTeaserCarousel.vue';
import { useEventStore } from '../../stores/EventStore.js';
const eventStore = useEventStore();

const emit = defineEmits([
    'show-event-page'
]);

const showEventPage = (eventId) => {
    emit('show-event-page', eventId);
}
</script>