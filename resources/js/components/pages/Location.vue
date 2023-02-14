<template>
    <div v-if="location != null">
        <LocationFormModal
            :locationToUpdate="location"
            @location-updated="locationUpdated($event)"
        />

        <div class="card w-50 mx-auto">
            <div class="card-body">
                <h3 class="text-center">{{ location.name }}</h3>

                <div class="row">
                    <div class="col">
                        <span v-if="location.streetAndNumber != null">{{ location.streetAndNumber }}, </span>
                        <span>{{ location.city }}<br></span>
                        <span v-if="location.website != null"><a :href="location.website" target="_blank">{{ location.website }}</a><br></span>
                        <span>{{ getParking() }}</span><br>
                        <span>{{ getBarrierFree() }}</span><br>
                    </div>
                    <div class="col">
                        <span id="location-details-description"></span><br>
                        
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-toggle="modal" 
                            data-bs-target="#location-form-modal"
                        >
                            Details bearbeiten
                        </button>
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
                        />
                    </div>
                    <div 
                        v-else-if="show == 'past'"
                        class="card-body"
                    >
                        <EventList 
                            :events="pastEvents" 
                            :parentModel="'location'"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref } from "@vue/runtime-core";
import { useRoute, useRouter } from "vue-router";
import { useArtistStore } from "../../stores/ArtistStore";
import { useEventStore } from "../../stores/EventStore";
import { useLocationStore } from "../../stores/LocationStore";
import EventList from '../layouts/EventList.vue';
import LocationFormModal from '../modals/LocationFormModal.vue';

const artistStore = useArtistStore();
const eventStore = useEventStore();
const locationStore = useLocationStore();

const route = useRoute();
const router = useRouter();

const show = ref('upcoming');
const location = ref(null);
const upcomingEvents = ref(null);
const pastEvents = ref(null)

const getLocation = () => {
    location.value = locationStore.getLocationById(route.params.locationId);

    upcomingEvents.value = eventStore.getEventsInLocation(route.params.locationId);
    upcomingEvents.value.forEach((event, eIndex) => {
        event.artists.forEach((artist, aIndex) => {
            if (Number.isInteger(artist)) {
                upcomingEvents.value[eIndex].artists[aIndex] = artistStore.getArtistById(artist);
            }
        })
    });

    pastEvents.value = eventStore.getPastEventsInLocation(route.params.locationId);
    pastEvents.value.forEach((event, eIndex) => {
        event.artists.forEach((artist, aIndex) => {
            if (Number.isInteger(artist)) {
                pastEvents.value[eIndex].artists[aIndex] = artistStore.getArtistById(artist);
            }
        })
    });

    updateDescription();
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

const locationUpdated = (newLocationData) => {
    location.value.name = newLocationData.name
    location.value.streetAndNumber = newLocationData.streetAndNumber
    location.value.city = newLocationData.city
    location.value.website = newLocationData.website
    location.value.parking = newLocationData.parking
    location.value.barrierFree = newLocationData.barrierFree
    location.value.description = newLocationData.description
    updateDescription();
}

const updateDescription = () => {
    setTimeout(() => {
        if (location.value.description != null) {
            let desc = location.value.description.replace('\n', '<br>');
            document.getElementById('location-details-description').innerHTML = desc;
        }
    }, 1);
}

onMounted(() => {
    if (route.params.locationId != null) getLocation()
})
</script>