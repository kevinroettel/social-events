<template>
    <div class="card event-teaser-card">
        <div @click="showEventPage()">
            <EventImage
                :source="event.flyer"
            />
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
import EventImage from './EventImage.vue';
import { getFormattedDate } from '../helpers/dateFormat.js';
import { inject } from 'vue';
import { useRouter } from 'vue-router';

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