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

                <EventTeaser 
                    v-for="(entry, index) in eventStore.getWatchlist"
                    :key="index"
                    :status="entry.status"
                    :event="entry.event"
                    @show-event-page="showEventPage($event)"
                />
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Vorschläge (erstmal Platzhalter, soll aber schonmal im Dashboard zu sehen sein)
            </div>
            <div class="card-body">
                <EventTeaser 
                    v-for="(event, index) in eventStore.getAllEvents"
                    :key="index"
                    :event="event"
                    @show-event-page="showEventPage($event)"
                />
            </div>
        </div>
    </div>
</template>
<script setup>
import EventTeaser from '../layouts/EventTeaser.vue'
import { useEventStore } from '../../stores/EventStore.js';
const eventStore = useEventStore();

const emit = defineEmits([
    'show-event-page'
]);

const showEventPage = (eventId) => {
    emit('show-event-page', eventId);
}
</script>