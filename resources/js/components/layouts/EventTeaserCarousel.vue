<template>
    <Carousel :settings="settings" :breakpoints="breakpoints">
        <Slide v-for="(event, index) in events" :key="index">
            <div class="carousel__item">
                <EventTeaser 
                    :status="(event.status != undefined ? event.status : null)"
                    :event="(event.event != undefined ? event.event : event)"
                />
            </div>
        </Slide>

        <template #addons>
            <Navigation />
        </template>
    </Carousel>
</template>
<script setup>
import { defineComponent, reactive } from 'vue'
import { Carousel, Navigation, Slide } from 'vue3-carousel'
import EventTeaser from '../layouts/EventTeaser.vue';
import { useEventStore } from '../../stores/EventStore.js';
const eventStore = useEventStore();

const props = defineProps({
    events: {
        required: true,
        type: Array,
        default: []
    }
});

const settings = reactive({
    itemsToShow: 1,
    snapAlign: 'center',
});

const breakpoints = reactive({
    700: {
        itemsToShow: 3.5,
        snapAlign: 'center',
    },

    1024: {
        itemsToShow: 5,
        snapAlign: 'start',
    },
})
</script>
