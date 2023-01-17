<template>
    <div v-if="events != null" class="col">
        <div 
            v-for="(event, index) in events"
            :key="index"
            class="row event-list-entry"
        >
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
                <a 
                    :href="getLocationMapsLink(event.location.name, event.location.city)"
                    target="_blank"
                    class="row event-list-location"
                >
                    {{ event.location.name }}, {{ event.location.city }}
                </a>
            </div>

            <hr v-if="index + 1 != events.length">
        </div>
    </div>
</template>
<script setup>

import { onMounted } from '@vue/runtime-core';
import { getLocationMapsLink } from '../helpers/locationMapsLink.js';


const props = defineProps({
    events: {
        required: true,
        type: Object,
        default: null
    }
});

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