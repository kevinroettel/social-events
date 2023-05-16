<template>
    <div class="card mb-3">
        <div class="card-header">
            Nutzer wie Sie, waren auch hieran interessiert (B)
        </div>
        <div class="card-body">
            <div v-if="similarEvents.length == 0">
                <p>Zurzeit können wir dir keine Event-Voschläge generieren.</p>
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
import { onMounted, ref } from 'vue';
import { useUserStore } from '../../stores/UserStore';
import { useEventStore } from '../../stores/EventStore';

const userStore = useUserStore();
const eventStore = useEventStore();

const similarEvents = ref([]);

const interestedStatistics = ref(null);
const eventCosineMatrix = ref(null);

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
            // if (similarEvents.value.includes(event)) continue;
            
            let eventData = eventStore.getEventById(event);
            if (eventData == null) continue;

            addEventToSimilar(eventData);
            // similarEvents.value.push(eventData)
        }
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

const getEventVector = (event) => {
    let eventVector = [];

    for (let [user, eventStatistic] of interestedStatistics.value.entries()) {
        eventVector.push(eventStatistic.get(event))
    }

    return eventVector;
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

onMounted(() => {
    collaborativeBased();
})
</script>