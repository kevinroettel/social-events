<template>
    <div class="card mb-3">
        <div class="card-header">
            Vorschläge basierend auf ihren Events (Cosine Similarity)
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

const cosines = ref([]);

const getData = () => {
    // muss getan werden, damit reactivity von vue verschwindet
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
            let wordCountA = wordCountMap(entry.event.tags);
            let wordCountB = wordCountMap(event.tags);

            let dictonary = {};
            addWordsToDictionary(wordCountA, dictonary);
            addWordsToDictionary(wordCountB, dictonary);

            let vectorA = wordMapToVector(wordCountA, dictonary);
            let vectorB = wordMapToVector(wordCountB, dictonary);
            let cosine = cosineSimilarity(vectorA, vectorB);

            cosines.value.push({
                entry: entry.event_id,
                event: event.id,
                cosine: cosine
            });

            if (cosine >= 0.25) {
                let isAlreadyInSimilar = false;

                similarEvents.value.forEach((similar) => {
                    if (similar.id == event.id) {
                        isAlreadyInSimilar = true;
                        return;
                    }
                });

                if (!isAlreadyInSimilar) {
                    similarEvents.value.push(event);
                }
            }
        })
    })
}

const wordCountMap = (wordArray) => {
    let wordCount = {};

    wordArray.forEach((word) => {
        wordCount[word] = (wordCount[word] || 0) + 1;
    });

    return wordCount;
}

const addWordsToDictionary = (wordCountmap, dictonary) => {
    for (let key in wordCountmap) dictonary[key] = true;
}

const wordMapToVector = (map, dictonary) => {
    let wordCountVector = [];

    for (let term in dictonary) {
        wordCountVector.push(map[term] || 0);
    }

    return wordCountVector;
}

const dotProduct = (vectorA, vectorB) => {
    let dotProduct = 0;

    for (let i = 0; i < vectorA.length; i++) {
        dotProduct += vectorA[i] * vectorB[i];
    }

    return dotProduct;
}

const magnitude = (vector) => {
    let sum = 0;

    for (let i = 0; i < vector.length; i++) {
        sum += vector[i] * vector[i];
    }

    return Math.sqrt(sum);
}

const cosineSimilarity = (vectorA, vectorB) => {
    return dotProduct(vectorA, vectorB) / (magnitude(vectorA) * magnitude(vectorB));
}

onMounted(() => {
    getData()
})
</script>