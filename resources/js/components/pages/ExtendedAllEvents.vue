<template>
    <div>
        <h3>Alle Events</h3>

        <div class="row row-cols-lg-5 row-cols-md-2 row-cols-auto">
            <div v-for="(event, index) in events" :key="index">
                <div class="col mb-3">
                    <EventTeaser
                        :status="null"
                        :event="event"
                    />
                </div>
            </div>
        </div>
    </div>

    <!-- pagination auf 30? -->
</template>
<script setup>
import EventTeaser from '../layouts/EventTeaser.vue';
import { useEventStore } from '../../stores/EventStore';
import { onMounted, ref } from 'vue';
const eventStore = useEventStore();

const events = ref([]);

const getEvents = () => {
    events.value = eventStore.getAllEvents

    events.value.sort((a, b) => {
        return new Date(a.date) - new Date(b.date)
    });
}

onMounted(() => {
    getEvents()
})
</script>