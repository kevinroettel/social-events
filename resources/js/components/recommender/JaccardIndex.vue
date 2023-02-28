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

    entries.forEach((entry, eIndex) => {
        entries[eIndex].event.tags = [];
        
        entry.event.artists.forEach((artist, aIndex) => {
            if (Number.isInteger(artist)) {
                entries[eIndex].event.artists[aIndex] = artistStore.getArtistById(artist);
            }

            entries[eIndex].event.tags.push(
                ...entries[eIndex].event.artists[aIndex].tags.map(tag => tag.name)
            );
        });
    });
    
    let events = JSON.parse(JSON.stringify(eventStore.getAllEvents));
    
    events.forEach((event, eIndex) => {
        events[eIndex].tags = [];

        event.artists.forEach((artist, aIndex) => {
            if (Number.isInteger(artist)) {
                events[eIndex].artists[aIndex] = artistStore.getArtistById(artist);
            }

            events[eIndex].tags.push(
                ...events[eIndex].artists[aIndex].tags.map(tag => tag.name)
            );
        });
    });

    events.forEach((event, index) => {

        entries.forEach((entry) => {
            let jaccard = jaccard_index(new Set(event.tags), new Set(entry.event.tags));
            
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

const intersection = (a, b) => {
    let intersect = new Set([...a].filter(i => b.has(i)));
    return intersect;
}

const jaccard_index = (s1, s2) => {
    let size_s1 = s1.size;
    let size_s2 = s2.size;

    let intersect = intersection(s1, s2);
    let size_in = intersect.size;

    let jaccard_in = size_in / (size_s1 + size_s2 - size_in);

    return jaccard_in;
}

onMounted(() => {
    getData();
})
</script>