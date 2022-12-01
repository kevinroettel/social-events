<template>
    <div class="card event-teaser-card" style="width: 18rem;">
        <div @click="showEventPage()">
            <div class="small-flyer-overflow">
                <img class="card-img-top" :src="event.flyer" alt="Card image cap">
            </div>
            <div class="card-body">
                <div class="card-text-headline">{{ event.name }}</div>
                <p class="card-text">{{ getFormattedDate(event.date) }}</p>
                <p v-if="location != null" class="card-text">{{ location.name }}, {{ location.city }}</p>
            </div>
        </div>
        <div class="card-footer">
            <button 
                v-if="status != 'interested'"
                class="btn btn-primary" 
                type="button" 
                @click="changeStatus('interested')"
            >
                Interessiert
            </button>
            <button
                v-else
                class="btn btn-danger"
                type="button"
                @click="changeStatus('delete')"
            >
                X Interessiert
            </button>
            
            <button 
                v-if="status != 'attending'"
                class="btn btn-primary ml-1" 
                type="button" 
                @click="changeStatus('attending')"
            >
                Zusagen
            </button>
            <button
                v-else
                class="btn btn-danger ml-1"
                type="button"
                @click="changeStatus('delete')"
            >
                X Zusage
            </button>
            
        </div>
    </div>
</template>
<script setup>
import { getFormattedDate } from '../helpers/dateFormat.js';
import { inject, onMounted, ref } from '@vue/runtime-core';
import { useLocationStore } from '../../stores/LocationStore.js';
import { useUserStore } from '../../stores/UserStore.js';
import { useEventStore } from '../../stores/EventStore.js';
const locationsStore = useLocationStore();
const userStore = useUserStore();
const eventStore = useEventStore();

const axios = inject('axios');

const props = defineProps({
    event: {
        required: true,
        type: Object,
        default: null
    },

    status: {
        required: false,
        type: String,
        default: null
    }
});

const emit = defineEmits([
    'show-event-page'
]);

const location = ref(null)

const showEventPage = () => {
    emit('show-event-page', props.event.id);
}

const changeStatus = (status) => {
    axios.patch(
        `/users/${userStore.getUserId}/watchlists/${props.event.id}/${status}`
    ).then((response) => {
        if (status == "delete") eventStore.removeFromWatchlist(props.event.id);
        else eventStore.updateWatchlist(response.data);
    }).catch((error) => {
        console.log(error);
    })
}

onMounted(() => {
    location.value = locationsStore.getLocationById(props.event.location_id);
})
</script>