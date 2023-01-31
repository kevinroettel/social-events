<template>
    <div v-if="location != null">
        <div class="card w-50 mx-auto">
            <div class="card-body">
                <h3 class="text-center">{{ location.name }} ({{ location.city }})</h3>

                <div class="row">
                    <div class="col">
                        <span v-if="location.streetAndNumber != null">{{ location.streetAndNumber }}<br></span>
                        <span v-if="location.website != null"><a :href="location.website" target="_blank">{{ location.website }}</a><br></span>
                        <span>{{ getParking() }}</span><br>
                        <span>{{ getBarrierFree() }}</span><br>
                    </div>
                    <div class="col">
                        <span>{{ location.description }}</span>
                    </div>
                </div>                

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

const getParking = () => {
    switch (location.value.parking) {
        case 'possible': return "Parkmöglichkeiten vorhanden"
        case 'impossible': return "Keine Parkmöglichkeiten"
        case 'unknown': return "Unbekannte Parksituation"
    }
}

const getBarrierFree = () => {
    switch (location.value.barrierFree) {
        case 'possible': return "Venue ist Barrierefrei"
        case 'impossible': return "Venue ist nicht Barrierefrei"
        case 'unknown': return "Barrierefreiheit ist nicht bekannt"
    }
}

const showEventPage = (eventId) => emit('show-event-page', eventId);

onMounted(() => {
    if (props.showLocation != null) getLocation()
})
</script>