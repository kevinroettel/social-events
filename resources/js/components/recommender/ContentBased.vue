<!-- https://medium.com/analytics-vidhya/building-a-text-similarity-checker-using-cosine-similarity-in-javascript-and-html-75722d485703 -->
<template>
    <div class="card mb-3">
        <div class="card-header">
            Vorschläge basierend auf ihren Events (A)
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
import { useUserStore } from '../../stores/UserStore';
import { onMounted, ref } from 'vue';
import { getDistanceBetweenTwoPoints } from '../helpers/geoCoding.js';

const eventStore = useEventStore();
const userStore = useUserStore();

const similarEvents = ref([]);

const cosines = ref([]);

const getData = () => {
    let userLocation = userStore.getUserCoordinates;

    // muss getan werden, damit reactivity von vue verschwindet
    let entries = JSON.parse(JSON.stringify(eventStore.getWatchlist.concat(eventStore.getOldWatchlist)));

    if (entries.length == 0) return;

    let events = JSON.parse(JSON.stringify(eventStore.getAllEvents));
    
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
                let distance = null;

                if (userLocation.latitude != null) {
                    distance = getDistanceBetweenTwoPoints(
                        userLocation.latitude, 
                        userLocation.longitude, 
                        event.location.latitude, 
                        event.location.longitude
                    );
                }

                if (distance == null) addEventToSimilar(event);
                else if (distance <= 100) addEventToSimilar(event);
            }
        })
    })
}

const addEventToSimilar = (event) => {
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
