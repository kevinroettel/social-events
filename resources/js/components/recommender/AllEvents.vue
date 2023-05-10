<template>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    Alle Events
                </div>
                <div class="col text-end">
                    <a @click="showExtendedAllEventsPage()" class="btn btn-primary">
                        <FontAwesomeIcon icon="fa-solid fa-folder-open" />
                    </a>
                </div>
            </div>
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
import { router } from '../../router';
const eventStore = useEventStore();

const showExtendedAllEventsPage = () => {
    router.push(`/allevents`);
}
</script>