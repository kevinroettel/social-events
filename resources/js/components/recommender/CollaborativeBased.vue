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

const diff = ref(null);
const freq = ref(null);
const inputData = ref(null);
const outputData = ref(null);

const getData = () => {
    userDataToMap();
    buildDifferencesMatrix();
    predict();
}

const userDataToMap = () => {
    let allUsers = userStore.getAllUsersWithWatchlistEntries
    
    let data = new Map(); // User Id, Map(Event Id, Weight)
    
    allUsers.forEach((user) => {
        let userItemList = new Map(); // Event Id, Weight

        user.watchlist.forEach((entry) => {
            userItemList.set(entry.event_id, (entry.status == 'interested' ? 0.5 : 1.0));
        });

        data.set(user.id, userItemList);
    })

    inputData.value = data;
}

const buildDifferencesMatrix = () => {
    for (let userEntries of inputData.value.values()) {
        // console.log(userEntries)

        for (let userEntry of userEntries.entries()) {
            console.log(userEntry)
        }
    }
}

const predict = () => {

}

onMounted(() => {
    getData()
})
</script>