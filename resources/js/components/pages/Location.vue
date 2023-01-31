<template>
    <div v-if="location != null">
        <div class="card w-50 mx-auto">
            <div class="card-body">
                <h3 class="text-center">{{ location.name }}</h3>
                
                <hr>

                <div class="card text-center">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a 
                                    :class="`nav-link ${show == 'upcoming' ? 'active' : ''}`"
                                    @click="show = 'upcoming'"
                                >
                                    Bevorstehende Veranstaltungen
                                </a>
                            </li>
                            <li class="nav-item">
                                <a 
                                    :class="`nav-link ${show == 'past' ? 'active' : ''}`"
                                    @click="show = 'past'"
                                >
                                    Vergangene Veranstaltungen
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div 
                        v-if="show == 'upcoming'"
                        class="card-body"
                    >
                        <EventList 
                            :events="upcomingEvents" 
                            :parentModel="'location'"
                            @show-event-page="showEventPage($event)"
                        />
                    </div>
                    <div 
                        v-else-if="show == 'past'"
                        class="card-body"
                    >
                        <EventList 
                            :events="pastEvents" 
                            :parentModel="'location'"
                            @show-event-page="showEventPage($event)"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref } from "@vue/runtime-core";
import { useArtistStore } from "../../stores/ArtistStore";
import { useEventStore } from "../../stores/EventStore";
import { useLocationStore } from "../../stores/LocationStore";
import EventList from '../layouts/EventList.vue';

const artistStore = useArtistStore();
const eventStore = useEventStore();
const locationStore = useLocationStore();

const props = defineProps({
    showLocation: {
        required: true,
        type: Number,
        default: null
    }
});

const emit = defineEmits([
    'show-event-page'
]);

const show = ref('upcoming');

const location = ref(null);
const upcomingEvents = ref(null);
const pastEvents = ref(null)

const getLocation = () => {
    location.value = locationStore.getLocationById(props.showLocation);

    upcomingEvents.value = eventStore.getEventsInLocation(props.showLocation);
    upcomingEvents.value.forEach((event, eIndex) => {
        event.artists.forEach((artist, aIndex) => {
            if (Number.isInteger(artist)) {
                upcomingEvents.value[eIndex].artists[aIndex] = artistStore.getArtistById(artist);
            }
        })
    });

    pastEvents.value = eventStore.getPastEventsInLocation(props.showLocation);
    pastEvents.value.forEach((event, eIndex) => {
        event.artists.forEach((artist, aIndex) => {
            if (Number.isInteger(artist)) {
                pastEvents.value[eIndex].artists[aIndex] = artistStore.getArtistById(artist);
            }
        })
    });
}

const showEventPage = (eventId) => emit('show-event-page', eventId);

onMounted(() => {
    if (props.showLocation != null) getLocation()
})
</script>