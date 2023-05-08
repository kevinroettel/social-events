<!-- https://www.baeldung.com/java-collaborative-filtering-recommendations -->
<template>
    <div class="card mb-3">
        <div class="card-header">
            Nutzer wie Sie, waren auch hieran interessiert
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

const allUsers = ref(null);
const diff = ref(null);
const freq = ref(null);
const inputData = ref(null);
const outputData = ref(null);

const getData = () => {
    diff.value = new Map();
    freq.value = new Map();
    outputData.value = new Map();

    allUsers.value = userStore.getAllUsersWithWatchlistEntries
    
    let currentUserWatchlist = eventStore.getWatchlist.map((entry) => {
        return {
            'event_id': entry.event.id,
            'status': entry.status
        }
    });

    allUsers.value.push({
        id: userStore.getUserId,
        watchlist: currentUserWatchlist
    });

    userDataToMap();
    buildDifferencesMatrix();
    predict();
    getEventsFromPredictions();

    // printData(outputData.value)
}

const userDataToMap = () => {
    let data = new Map(); // User Id, Map[EventId, "Rating"]
    
    allUsers.value.forEach((user) => {
        let userItemList = new Map(); // [EventId, "Rating"]

        user.watchlist.forEach((entry) => {
            // userItemList.set(entry.event_id, 1);
            userItemList.set(entry.event_id, (entry.status == 'interested' ? 0.5 : 1));
        });

        data.set(user.id, userItemList);
    })

    inputData.value = data;
}

const buildDifferencesMatrix = () => {
    for (let userEntries of inputData.value.values()) {
        for (let [event, score] of userEntries.entries()) {

            if (!diff.value.has(event)) {
                diff.value.set(event, new Map());
                freq.value.set(event, new Map());
            }

            for (let [event2, score2] of userEntries.entries()) {
                let oldCount = 0;

                if (freq.value.get(event).has(event2)) {
                    oldCount = freq.value.get(event).get(event2);
                }

                let oldDiff = 0.0;
                if (diff.value.get(event).has(event2)) {
                    oldDiff = diff.value.get(event).get(event2);
                }

                let observedDiff = score - score2;
                freq.value.get(event).set(event2, oldCount + 1);
                diff.value.get(event).set(event2, oldDiff + observedDiff);
            }
        }
    }

    for (let j of diff.value.keys()) {
        for (let i of diff.value.get(j).keys()) {
            let oldValue = diff.value.get(j).get(i);
            let count = freq.value.get(j).get(i);
            diff.value.get(j).set(i, oldValue / count);
        }
    }
}

const predict = () => {
    let uPred = new Map(); // [Event, double]
    let uFreq = new Map(); // [Event, integer]

    for (let j of diff.value.keys()) {
        uFreq.set(j, 0);
        uPred.set(j, 0.0);
    }

    for (let [user, events] of inputData.value.entries()) {
        for (let j of events.keys()) {
            for (let k of diff.value.keys()) {
                try {
                    let predictedValue = diff.value.get(k).get(j) + events.get(j);
                    let finalValue = predictedValue * freq.value.get(k).get(j);
                    uPred.set(k, uPred.get(k) + finalValue);
                    uFreq.set(k, uFreq.get(k) + freq.value.get(k).get(j));
                } catch (error) {
                    console.warn(error)
                }
            }
        }

        let clean = new Map(); // [item, double]
        for (let j of uPred.keys()) {
            if (uFreq.get(j) > 0) {
                clean.set(j, uPred.get(j) / uFreq.get(j));
            }
        }

        for (let eventFromStore of eventStore.getAllEvents)  {
            if (events.has(eventFromStore.id)) {
                clean.set(eventFromStore.id, events.get(eventFromStore.id));
            } else if (!clean.has(eventFromStore.id)) {
                clean.set(eventFromStore.id, 0);
            }
        }

        outputData.value.set(user, clean);
    }
}

const getEventsFromPredictions = () => {
    for (let users of outputData.value.entries()) {
        for (let event of users[1]) {
            if (event[1] > 0) {
                addEventToSimilar(event[0]);
            }
        }
    }
}

const addEventToSimilar = (eventId) => {
    let eventFromStore = eventStore.getEventById(eventId);
    if (eventFromStore.event != undefined) eventFromStore = eventFromStore.event;

    if (eventFromStore == null) return; // Old Event, doesn't need to be recommended
    else if (eventStore.checkIfEventIsInWatchlist(eventId)) return; // Event is already in Watchlist

    let isInSimilar = false;

    similarEvents.value.forEach((event) => {
        if (event.id == eventId) isInSimilar = true;
    })
    
    if (!isInSimilar) {
        similarEvents.value.push(eventFromStore)
    }
}

const printData = (userMap) => {
    for (let [user, predictions] of userMap.entries()) {
        console.log(user  + ":")

        for (let [event, value] of predictions) {
            console.log(" " + event + " --> " + value);
        }
    }
}

onMounted(() => {
    getData()
})
</script>