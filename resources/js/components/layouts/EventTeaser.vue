<template>
    <div class="card event-teaser-card" style="width: 18rem;">
        <div @click="showEventPage()">
            <div class="small-flyer-overflow">
                <img class="card-img-top" :src="'/storage' + event.flyer" alt="Card image cap">
            </div>
            <div class="card-body">
                <div class="card-text-headline">{{ event.name }}</div>
                <p class="card-text">{{ getFormattedDate(event.date) }}</p>
                <p v-if="location != null" class="card-text">{{ location.name }}, {{ location.city }}</p>
            </div>
        </div>
        <div class="card-footer">
            <EventStatusButtons
                :event="event.id"
                :status="status"
            />
        </div>
    </div>
</template>
<script setup>
import EventStatusButtons from './EventStatusButtons.vue';
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

onMounted(() => {
    location.value = locationsStore.getLocationById(props.event.location_id);
})
</script>