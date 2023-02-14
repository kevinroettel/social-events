<template>
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
</template>
<script setup>
import EventTeaserCarousel from '../layouts/EventTeaserCarousel.vue';
import { useEventStore } from '../../stores/EventStore.js';
const eventStore = useEventStore();
</script>