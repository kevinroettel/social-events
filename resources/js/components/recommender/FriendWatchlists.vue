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

const events = ref([]);
const friendsWatchlists = ref([]);

const getData = () => {
    friendsWatchlists.value = userStore.getFriendWatchlistEntries

    // calculates weights of event priorization
    // interested friend adds 1, attending friend adds 2
    friendsWatchlists.value.forEach(entry => {
        if (events.value.length != 0) {

            // if event hasnt been inserted into array yet, do it
            if (!events.value.find(event => event.event_id === entry.event_id)) {
                events.value.push({
                    event_id: entry.event_id,
                    weight: (entry.status == 'interested' ? 1 : 2)
                })
            } else {
                // goes through events and adds weight to event
                events.value.forEach((event, index) => {
                    if (event.event_id == entry.event_id) {
                        events[index].weight += (entry.status == 'interested' ? 1 : 2);
                    }
                });
            }

        } else {
            // first event push
            events.value.push({
                event_id: entry.event_id,
                weight: (entry.status == 'interested' ? 1 : 2)
            });
        }
    });

    // sort events descending by weight
    events.value = events.value.sort((eventA, eventB) => eventB.weight - eventA.weight)

    // gets eventdata from store, if null event is in the past and gets removed
    let index = events.value.length - 1;

    while (index >= 0) {
        let eventData = eventStore.getEventById(events.value[index].event_id);
        if (eventData === null) events.value.splice(index, 1);
        else events.value[index] = eventData;
        index--;
    }
}

onMounted(() => {
    getData()
})
</script>