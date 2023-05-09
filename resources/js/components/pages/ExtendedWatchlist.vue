<template>
    <div>
        <h3>Watchlist</h3>

        <div class="row row-cols-5">
            <div v-for="(event, index) in eventStore.getWatchlist" :key="index">
                <div class="col mb-3">
                    <EventTeaser
                        :status="event.status"
                        :event="event.event"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import EventTeaser from '../layouts/EventTeaser.vue';
import { useEventStore } from '../../stores/EventStore';
const eventStore = useEventStore();
</script>