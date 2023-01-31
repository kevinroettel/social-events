<template>
    <div v-if="events != null" class="col">
        <div 
            v-for="(event, index) in events"
            :key="index"
            class="row event-list-entry"
            @click="showEventPage(event.id)"
        >
            <hr v-if="index != 0">

            <div class="col-2 event-list-date">
                <div class="row">
                    <div class="event-list-day-month">{{ getDate(event.date) }}</div>
                    <div class="event-list-year">{{ getYear(event.date) }}</div>
                </div>
            </div>

            <div class="col event-list-details">
                <div class="row event-list-name">
                    {{ event.name }}
                </div>

                <p v-if="parentModel == 'artist'" class="row event-list-location">
                    {{ event.location.name }}, {{ event.location.city }}
                </p>

                <p v-else-if="parentModel == 'location'" class="row event-list-line-up">
                    Line-Up: {{ event.artists.map(artist => artist.name).join(", ") }}
                </p>
            </div>
        </div>

        <p v-if="events.length == 0">Hierfür konnten wir keine Veranstaltungen finden.</p>
    </div>
</template>
<script setup>
import { onMounted } from '@vue/runtime-core';

const props = defineProps({
    events: {
        required: true,
        type: Object,
        default: null
    },

    parentModel: {
        required: true,
        type: String,
        default: "artist"
    },
});

const emit = defineEmits([
    'show-event-page'
]);

const showEventPage = (eventId) => emit('show-event-page', eventId);

const getDate = (stringDate) => {
    let date = new Date(stringDate);
    let month = date.toLocaleString('default', {month: 'short'});
    return date.getDate() + ". " + month;
}

const getYear = (stringDate) => {
    let date = new Date(stringDate);
    return date.getFullYear();
}
</script>