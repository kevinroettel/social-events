<template>
    <div>
        <h3>Watchlist</h3>

        <h4>Zugesagt</h4>
        <div 
            v-if="going.length != 0"
            class="row row-cols-lg-5 row-cols-md-2 row-cols-auto"
        >
            <div v-for="(event, index) in going" :key="index">
                <div class="col mb-3">
                    <EventTeaser
                        :status="event.status"
                        :event="event.event"
                    />
                </div>
            </div>
        </div>
        <p v-else>Zurzeit hast du zu keinem Event zugesagt.</p>

        <h4>Interessiert</h4>
        <div 
            v-if="interested.length != 0"
            class="row row-cols-lg-5 row-cols-md-2 row-cols-auto"
        >
            <div v-for="(event, index) in interested" :key="index">
                <div class="col mb-3">
                    <EventTeaser
                        :status="event.status"
                        :event="event.event"
                    />
                </div>
            </div>
        </div>
        <p v-else>Zurzeit bist du an keinen Events interessiert.</p>
    </div>
</template>
<script setup>
import EventTeaser from '../layouts/EventTeaser.vue';
import { useEventStore } from '../../stores/EventStore';
import { onMounted, ref } from 'vue';
const eventStore = useEventStore();

const interested = ref([]);
const going = ref([]);

const splitEvents = () => {
    eventStore.getWatchlist.forEach((entry) => {
        if (entry.status == "interested") interested.value.push(entry)
        else going.value.push(entry)
    })
}

onMounted(() => {
    splitEvents();
})
</script>