<template>
    <div class="card mb-3">
        <div class="card-header">
            Vorschläge basierend auf interessen deiner Freunde
        </div>
        <div class="card-body">
            <div v-if="events.length == 0">
                <p>Zurzeit können wir dir keine Events basierend auf den Präferenzen deiner Freunde anzeigen.</p>
            </div>

            <EventTeaserCarousel
                v-else
                :events="events"
                @show-event-page="showEventPage($event)"
            />
        </div>
    </div>
</template>
<script setup>
import EventTeaserCarousel from '../layouts/EventTeaserCarousel.vue';
import { ref, onMounted } from "vue";
import { useEventStore } from "../../stores/EventStore";
import { useUserStore } from "../../stores/UserStore";

const eventStore = useEventStore();
const userStore = useUserStore();

const emit = defineEmits([
    'show-event-page'
]);

const events = ref([]);
const friendsWatchlists = ref([]);

const getData = () => {
    friendsWatchlists.value = userStore.getFriendWatchlistEntries

    // event id, weight
    
    friendsWatchlists.value.forEach(entry => {
        if (events.value.length != 0) {

            if (!events.value.find(event => event.event_id === entry.event_id)) {
                events.value.push({
                    event_id: entry.event_id,
                    weight: (entry.status == 'interested' ? 1 : 2)
                })
            } else {
                events.value.forEach((event, index) => {
                    if (event.event_id == entry.event_id) {
                        events[index].weight += (entry.status == 'interested' ? 1 : 2);
                    }
                });
            }

        } else {
            events.value.push({
                event_id: entry.event_id,
                weight: (entry.status == 'interested' ? 1 : 2)
            });
        }
    });

    events.value = events.value.sort((eventA, eventB) => eventB.weight - eventA.weight)

    events.value.forEach((event, index) => {
        events.value[index] = eventStore.getEventById(event.event_id)
    })
}

const showEventPage = (eventId) => {
    emit('show-event-page', eventId);
}

onMounted(() => {
    getData()
})
</script>