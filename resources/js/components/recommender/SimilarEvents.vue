<template>
    <div class="card mb-3">
        <div class="card-header">
            Vorschläge basierend auf ihren Events
        </div>
        <div class="card-body">
            <div v-if="events.length == 0">
                <p>Zurzeit können wir dir keine Events basierend auf ihren Präferenzen anzeigen.</p>
            </div>

            <EventTeaserCarousel
                v-else
                :events="events"
            />
        </div>
    </div>
</template>
<script setup>
import EventTeaserCarousel from '../layouts/EventTeaserCarousel.vue';
import { useEventStore } from "../../stores/EventStore";
import { onMounted, ref } from 'vue';

const eventStore = useEventStore();

const events = ref([]);

const getData = () => {
    
}

onMounted(() => {
    getData()
})
</script>