<template>
    <div>
        <LocationFormModal 
            @location-saved="selectCreated($event)"
        />

        <div class="row">
            <div class="col">
                <h3 v-if="isUpdate">Eventbearbeitung</h3>
                <h3 v-else>Neues Event</h3>
                
                <!-- name -->
                <div class="input-group mb-3">
                    <span class="input-group-text" id="name-input-label">Name *</span>
                    <input type="text" :class="`form-control ${errorClass('name')}`" aria-label="Name" aria-describedby="name-input-label" v-model="event.name">
                </div>

                <!-- description -->
                <div class="input-group mb-3">
                    <span class="input-group-text" id="description-input-label">Beschreibung</span>
                    <textarea type="text" :class="`form-control`" aria-label="Beschreibung" aria-describedby="description-input-label" v-model="event.description"></textarea>
                </div>

                <!-- flyer -->
                <div class="input-group mb-3">
                    <span class="input-group-text" id="flyer-input-label">Flyer</span>
                    <input type="file" ref="flyerInput" :class="`form-control`" aria-label="Fyler" aria-describedby="flyer-input-label" @change="handleFile($event)">
                </div>

                <!-- date -->
                <div class="input-group mb-3">
                    <span class="input-group-text" id="date-input-label">Datum *</span>
                    <input type="date" :class="`form-control ${errorClass('date')}`" aria-label="Datum" aria-describedby="date-input-label" v-model="event.date">
                </div>

                <!-- doors -->
                <div class="input-group mb-3">
                    <span class="input-group-text" id="doors-input-label">Einlass</span>
                    <input type="time" :class="`form-control`" aria-label="Einlass" aria-describedby="doors-input-label" v-model="event.doors">
                </div>

                <!-- begin -->
                <div class="input-group mb-3">
                    <span class="input-group-text" id="begin-input-label">Beginn *</span>
                    <input type="time" :class="`form-control ${errorClass('begin')}`" aria-label="Beginn" aria-describedby="begin-input-label" v-model="event.begin">
                </div>

                <div class="input-group mb-3">
                    <span class="input-group-text" id="ticket-input-label">Ticket Link</span>
                    <input type="text" class="form-control" aria-label="Ticketlink" aria-describedby="ticket-input-label" v-model="event.ticketLink">
                </div>

                <!-- location id -->
                <div class="input-group mb-3">
                    <span class="input-group-text" id="location-select-label">Venue *</span>
                    <v-select 
                        :class="`form-control ${errorClass('location')}`" 
                        aria-label="Venue" 
                        aria-describedby="location-select-label" 
                        :options="locationStore.locations" 
                        label="name" 
                        v-model="event.location"
                    ></v-select>
                </div>

                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#new-location-modal">
                    Neue Venue erstellen
                </button>

                <!-- artists -->
                <p v-if="showNotifcation">Bevor Sie das Event erstellen, gehen Sie bitte sicher das sie alle Künstler korrekt geschrieben haben.</p>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="artist-select-label">Künstler *</span>
                    <v-select 
                        multiple
                        taggable 
                        :class="`form-control ${errorClass('artists')}`" 
                        aria-label="Künstler" 
                        aria-describedby="artist-select-label" 
                        :options="artistStore.artists" 
                        label="name" 
                        
                        v-model="event.artists"
                        @option:created="showNotifcation = true"
                    ></v-select>
                </div>

                <p>* Benötigte Eingaben</p>

                <button type="button" class="btn btn-primary" @click="checkInputs()">
                    <span v-if="isUpdate">Event aktualisieren</span>
                    <span v-else>Event Speichern</span>
                </button>
            </div>
            <div class="col">
                <h3>Vorschau</h3>
                <EventPreview
                    :event="event"
                    :flyer="flyerUrl"
                />
            </div>
        </div>
    </div>
</template>
<script setup>
import { inject, onMounted, reactive, ref } from "vue";
import EventPreview from '../layouts/EventPreview.vue';
import LocationFormModal from './LocationFormModal.vue';
import { useLocationStore } from '../../stores/LocationStore.js';
import { useArtistStore } from '../../stores/ArtistStore.js';
import { useEventStore } from "../../stores/EventStore";

const eventStore = useEventStore();
const locationStore = useLocationStore();
const artistStore = useArtistStore();

const axios = inject('axios');

const props = defineProps({
    eventToUpdate: {
        required: false,
        type: Number,
        default: null
    }
});

const emit = defineEmits([
    'event-created'
]);

const isUpdate = ref(false);
const flyerUrl = ref(null);
const showNotifcation = ref(false);

const event = reactive({
    name: null,
    description: null,
    flyer: null,
    date: null,
    doors: null,
    begin: null,
    ticketLink: null,
    location: null,
    artists: [],
});

const hasError = reactive({
    name: false,
    date: false,
    begin: false,
    location: false,
    artists: false
});

const getEventData = () => {
    let eventData = eventStore.getEventById(props.eventToUpdate);
    event.name = eventData.name;
    event.description = eventData.description;
    event.flyer = eventData.flyer;
    flyerUrl.value = '/storage/' + eventData.flyer;
    event.date = eventData.date;
    event.doors = eventData.doors;
    event.begin = eventData.begin;
    event.ticketLink = eventData.ticketLink;
    event.location = eventData.location;
    event.artists = eventData.artists;
}

const handleFile = (input) => {
    setTimeout(() => {
        event.flyer = input.target.files[0];
        flyerUrl.value = URL.createObjectURL(event.flyer);
    }, 1);
}

const checkInputs = () => {
    let allInputsOkay = true;

    for (let [key, val] of Object.entries(hasError)) {
        if (event[key] == null || event[key].length == 0) {
            hasError[key] = true;
            allInputsOkay = false;
        } else hasError[key] = false;
    }

    if (allInputsOkay) saveEvent();
}

const saveEvent = () => {
    let formData = new FormData();
    formData.append('name', event.name);
    if (event.description != null) formData.append('description', event.description);
    if (event.flyer != null) formData.append('flyer', event.flyer);
    formData.append('date', event.date);
    if (event.doors != null) formData.append('doors', event.doors);
    formData.append('begin', event.begin);
    if (event.ticketLink != null) formData.append('ticketLink', event.ticketLink);
    formData.append('location', event.location.id);
    formData.append('artists', JSON.stringify(event.artists.map(a => a.id != undefined ? a.id : a.name)));

    axios.post(
        '/events',
        formData
    ).then((response) => {
        console.log(response.data)
        emit('event-created', response.data);
    }).catch((error) => {
        console.log(error);
    })
}

const errorClass = (input) => {
    return hasError[input] ? 'is-invalid' : '';
}

const selectCreated = (location) => {
    event.location = location
}

onMounted(() => {
    if (props.eventToUpdate != null) {
        getEventData();
        isUpdate.value = true
    }
})
</script>