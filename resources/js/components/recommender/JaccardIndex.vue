<template>
    <div class="card mb-3">
        <div class="card-header">
            Vorschläge basierend auf ihren Events (Jaccard Index)
        </div>
        <div class="card-body">
            <div v-if="similarEvents.length == 0">
                <p>Zurzeit können wir dir keine Events basierend auf ihren Präferenzen anzeigen.</p>
            </div>

            <EventTeaserCarousel
                v-else
                :events="similarEvents"
            />
        </div>
    </div>
</template>
<script setup>
import EventTeaserCarousel from '../layouts/EventTeaserCarousel.vue';
import { useEventStore } from "../../stores/EventStore";
import { onMounted, ref } from 'vue';
import { useArtistStore } from '../../stores/ArtistStore';

const eventStore = useEventStore();
const artistStore = useArtistStore();

const similarEvents = ref([]);

const jaccards = ref([]);

const getData = () => {
    let entries = JSON.parse(JSON.stringify(eventStore.getWatchlist.concat(eventStore.getOldWatchlist)));

    if (entries.length == 0) return;

    let events = JSON.parse(JSON.stringify(eventStore.getAllEvents));
    
    events.forEach((event, index) => {

        entries.forEach((entry) => {
            let jaccard = jaccardIndex(new Set(event.tags), new Set(entry.event.tags));
            
            jaccards.value.push({
                entry: entry.event_id,
                event: event.id,
                jaccard: jaccard,
            });

            if (jaccard >= 0.12) {
                let isAlreadyInSimilar = false;

                similarEvents.value.forEach((similar) => {
                    if (similar.id == event.id) {
                        isAlreadyInSimilar = true;
                        return;
                    }
                });

                if (!isAlreadyInSimilar) {
                    similarEvents.value.push(event)
                }
            }
        })
    })
}

const jaccardIndex = (s1, s2) => {
    let intersection = new Set([...s1].filter(i => s2.has(i)));
    return intersection.size / (s1.size + s2.size - intersection.size);
}

onMounted(() => {
    getData();
})
</script>