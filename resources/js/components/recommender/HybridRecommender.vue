<template>
    <div class="card mb-3">
        <div class="card-header">
            Vorschläge basierend auf ihren Events
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

const eventStore = useEventStore();
const userStore = useUserStore();

const similarEvents = ref([]);
const contentSimilarEvents = ref([]);
const collaborativeSimilarEvents = ref([]);

const interestedStatistics = ref(null);
const eventCosineMatrix = ref(null);

const getData = () => {
    collaborativeBased();
    contentBased();
    weightSimilarEvents()
}

const collaborativeBased = () => {
    let allUsers = userStore.getAllUsersWithWatchlistEntries
    
    let currentUserWatchlist = eventStore.getWatchlist.map((entry) => {
        return {
            'event_id': entry.event.id,
            'status': entry.status
        }
    });

    allUsers.push({
        id: userStore.getUserId,
        watchlist: currentUserWatchlist
    });
    
    let allRatedEvents = [];

    allUsers.forEach((user) => {
        user.watchlist.forEach((entry) => {
            if (!allRatedEvents.includes(entry.event_id)) {
                allRatedEvents.push(entry.event_id);
            }
        })
    })

    let data = new Map(); // User Id, Map[EventId, "Rating"]
    
    allUsers.forEach((user) => {
        let userItemList = new Map(); // [EventId, "Rating"]

        allRatedEvents.forEach((ratedEvent) => {
            userItemList.set(ratedEvent, 0);
        })

        user.watchlist.forEach((entry) => {
            userItemList.set(entry.event_id, 1);
        });

        data.set(user.id, userItemList);
    })

    interestedStatistics.value = data;

    eventCosineMatrix.value = new Map();

    allRatedEvents.forEach((ratedEvent1) => {
        let eventCosines = new Map(); // EventId, Cosine
        let eventVector1 = getEventVector(ratedEvent1);
        
        allRatedEvents.forEach((ratedEvent2) => {
            let eventVector2 = getEventVector(ratedEvent2);
            let cosine = cosineSimilarity(eventVector1, eventVector2);
            eventCosines.set(ratedEvent2, parseFloat(cosine.toFixed(2)));
        });
        
        eventCosineMatrix.value.set(ratedEvent1, eventCosines); // EventId, Map[EventId, Cosine]
    })
    
    eventStore.getWatchlist.forEach((entry) => {
        let cosines = eventCosineMatrix.value.get(entry.event_id);
        
        for (let [event, cosine] of cosines.entries()) {
            if (cosine < 0.5) continue;
            if (entry.event_id == event) continue;
            if (eventStore.checkIfEventIsInWatchlist(event)) continue;
            if (collaborativeSimilarEvents.value.includes(event)) continue;
            
            let eventData = eventStore.getEventById(event);
            if (eventData == null) continue;

            collaborativeSimilarEvents.value.push(eventData)
        }
    })
}

const getEventVector = (event) => {
    let eventVector = [];

    for (let [user, eventStatistic] of interestedStatistics.value.entries()) {
        eventVector.push(eventStatistic.get(event))
    }

    return eventVector;
}

const contentBased = () => {
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
            if (cosine >= 0.3) addEventToSimilar(event);
        })
    })
}

const addEventToSimilar = (event) => {
    let isAlreadyInSimilar = false;

    contentSimilarEvents.value.forEach((similar) => {
        if (similar.id == event.id) {
            isAlreadyInSimilar = true;
            return;
        }
    })

    if (!isAlreadyInSimilar) contentSimilarEvents.value.push(event)
}

const wordCountMap = (wordArray) => {
    let wordCount = {};

    wordArray.forEach((word) => {
        wordCount[word] = (wordCount[word] || 0) + 1;
    });

    return wordCount;
}

const addWordsToDictionary = (map, dictonary) => {
    for (let key in map) dictonary[key] = true;
}

const wordMapToVector = (map, dictonary) => {
    let wordCountVector = [];

    for (let term in dictonary) {
        wordCountVector.push(map[term] || 0);
    }

    return wordCountVector;
}

const dotProduct = (vectorA, vectorB) => {
    return vectorA.reduce((pv, cv, i) => pv += cv * vectorB[i], 0);
}

const magnitude = (vector) => {
    return Math.sqrt(vector.reduce((pv, cv) => pv += cv * cv, 0));
}

const cosineSimilarity = (vectorA, vectorB) => {
    return dotProduct(vectorA, vectorB) / (magnitude(vectorA) * magnitude(vectorB));
}

const weightSimilarEvents = () => {
    similarEvents.value = [...contentSimilarEvents.value, ...collaborativeSimilarEvents.value];
    similarEvents.value.sort((a, b) => {
        return new Date(a.date) - new Date(b.date)
    });

}

onMounted(() => {
    getData();
})
</script>