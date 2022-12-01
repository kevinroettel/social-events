<template>
    <div v-if="event != null">
        <ImageModal :image="event.flyer" />

        <div class="card w-50 mx-auto">
            <div class="big-flyer-overflow" @click="toggleImage()" data-bs-toggle="modal" data-bs-target="#image-modal">
                <img :src="event.flyer" class="card-img-top">
            </div>
            <div class="card-body">
                <h3>{{ event.name  }}</h3>

                <div class="row">
                    <span v-if="event.date">{{ getFormattedDate(event.date) }}</span>
                    <span v-if="event.doors">Einlass: {{ event.doors }}</span>
                    <span v-if="event.begin">Beginn: {{ event.begin }}</span>
                </div>

                <p v-if="event.location != null">{{ event.location.name }} in {{ event.location.city }}</p>
                
                <p v-if="event.name != null">Name1 und zwei weitere Freunde sind an der Veranstaltung interessiert.</p>
                <div class="row">
                    <div class="col pre-wrap">{{ event.description }}</div>
                    <div class="col">
                        Artists
                        <!-- <p v-if="event.artists.length != 0">Line-up:</p>
                        <ul>
                            <li 
                                v-for="(artist, index) in event.artists"
                                :key="index"
                            >
                                {{ artist.name }}
                            </li>
                        </ul> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref, watch } from '@vue/runtime-core';
import ImageModal from '../layouts/ImageModal.vue';
import { getFormattedDate } from '../helpers/dateFormat.js';
import { useEventStore } from '../../stores/EventStore.js';
const eventStore = useEventStore();

const props = defineProps({
    eventId: {
        required: true,
        type: [Number, null],
        default: null
    }
});

const event = ref(null);
const watchlistStatus = ref(null);
const showFullImage = ref(false);

const getEventData = () =>  {
    let eventFromStore = eventStore.getEventById(props.eventId);
    if (typeof eventFromStore.status !== "undefined") {
        event.value = eventFromStore.event;
        watchlistStatus.value = eventFromStore.status;
    } else {
        event.value = eventFromStore;
    }
}

const toggleImage = () => {
    showFullImage.value = !showFullImage.value
}

onMounted(() => {
    if (props.eventId != null) getEventData();
})
</script>