<template>
    <Carousel :settings="settings" :breakpoints="breakpoints">
        <Slide v-for="(event, index) in eventStore.getAllEvents" :key="index">
            <div class="carousel__item">
                <EventTeaser 
                    :event="event"
                    @show-event-page="showEventPage($event)"
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

const emit = defineEmits([
    'show-event-page'
]);

const showEventPage = (eventId) => {
    emit('show-event-page', eventId);
}
</script>
