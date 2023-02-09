<template>
    <div class="card event-teaser-card">
        <div @click="showEventPage()">
            <div class="small-flyer-overflow">
                <img class="card-img-top" :src="'/storage' + (event.flyer ?? '/fallback-flyer.jpg')" alt="Card image cap">
            </div>
            <div class="card-body">
                <div class="card-text-headline">{{ event.name }}</div>
                <p class="card-text">{{ getFormattedDate(event.date) }}</p>
                <p class="card-text">{{ event.location.name }}, {{ event.location.city }}</p>
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
import { inject, onMounted, ref, watch } from '@vue/runtime-core';
import { useUserStore } from '../../stores/UserStore.js';
import { useEventStore } from '../../stores/EventStore.js';
import { useRouter } from 'vue-router';
const userStore = useUserStore();
const eventStore = useEventStore();

const router = useRouter();

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
    },

    carouselIndex: {
        required: false,
        type: Number,
        default: null
    }
});

const showEventPage = () => {
    router.push(`/event/${props.event.id}`);
}
</script>