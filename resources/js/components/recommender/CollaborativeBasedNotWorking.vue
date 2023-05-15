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

const allUsers = ref([]);
const allEvents = ref([]);
const allRatings = ref([]);
const similarityMatrix = ref([]);

const getData = () => {
    let events = eventStore.getAllEvents.concat(eventStore.getOldEvents)
    allEvents.value.push(...events.map(e => e.id))

    let usersWithWatchlists = userStore.getAllUsersWithWatchlistEntries
    usersWithWatchlists.push({
        username: userStore.getUserName,
        watchlist: eventStore.getWatchlist.concat(eventStore.getOldWatchlist)
    });

    usersWithWatchlists.forEach((user) => {
        let uId = user.id ?? userStore.getUserId
        allUsers.value.push(uId)
        
        user.watchlist.forEach((entry) => {
            if (!allEvents.value.includes(entry.event_id)) allEvents.value.push(entry.event_id);
            let uId = user.id ?? userStore.getUserId

            allRatings.value.push([
                allUsers.value.indexOf(uId),
                allEvents.value.indexOf(entry.event_id),
                1
            ]);
        })
    })

    for (let i = 0; i < allUsers.value.length; i++) {
        let row = [];

        for (let j = 0; j < allUsers.value.length; j++) {
            let similarities = [];

            for (let k = 0; k < allEvents.value.length; k++) {
                
                if (allRatings.value[i][k] && allRatings.value[j][k]) {
                    similarities.push(allRatings.value[i][k] - allRatings.value[j][k])
                }
            }

            if (similarities.length > 0) {
                let similarity = similarities.reduce((acc, cur) => acc + cur) / similarities.length;
                row.push(`${allUsers.value[i]}, ${allUsers.value[j]}: ` + similarity)
            } else {
                row.push(0);
            }
        }

        similarityMatrix.value.push(row)
    }

    console.log(similarityMatrix.value)
}

onMounted(() => {
    getData();
});
</script>