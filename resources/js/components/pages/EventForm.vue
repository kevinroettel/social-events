<template>
    <div>
        <div class="row">
            <div class="col">
                <h3>Neues Event</h3>
                <!-- name -->
                <div class="input-group mb-3">
                    <span class="input-group-text" id="name-input-label">Name *</span>
                    <input type="text" :class="`form-control ${errorClass('name')}`" aria-label="Name" aria-describedby="name-input-label" v-model="newEvent.name">
                </div>

                <!-- description -->
                <div class="input-group mb-3">
                    <span class="input-group-text" id="description-input-label">Beschreibung</span>
                    <textarea type="text" :class="`form-control`" aria-label="Beschreibung" aria-describedby="description-input-label" v-model="newEvent.description"></textarea>
                </div>

                <!-- flyer -->
                <div class="input-group mb-3">
                    <span class="input-group-text" id="flyer-input-label">Flyer</span>
                    <input type="file" ref="flyerInput" :class="`form-control`" aria-label="Fyler" aria-describedby="flyer-input-label" @change="handleFile($event)">
                </div>

                <!-- date -->
                <div class="input-group mb-3">
                    <span class="input-group-text" id="date-input-label">Datum *</span>
                    <input type="date" :class="`form-control ${errorClass('date')}`" aria-label="Datum" aria-describedby="date-input-label" v-model="newEvent.date">
                </div>

                <!-- doors -->
                <div class="input-group mb-3">
                    <span class="input-group-text" id="doors-input-label">Einlass</span>
                    <input type="time" :class="`form-control`" aria-label="Einlass" aria-describedby="doors-input-label" v-model="newEvent.doors">
                </div>

                <!-- begin -->
                <div class="input-group mb-3">
                    <span class="input-group-text" id="begin-input-label">Beginn *</span>
                    <input type="time" :class="`form-control ${errorClass('begin')}`" aria-label="Beginn" aria-describedby="begin-input-label" v-model="newEvent.begin">
                </div>

                <div class="input-group mb-3">
                    <span class="input-group-text" id="ticket-input-label">Ticket Link</span>
                    <input type="text" class="form-control" aria-label="Ticketlink" aria-describedby="ticket-input-label" v-model="newEvent.ticketLink">
                </div>

                <!-- location id -->
                <div class="input-group mb-3">
                    <span class="input-group-text" id="location-select-label">Venue *</span>
                    <v-select :class="`form-control ${errorClass('location')}`" aria-label="Venue" aria-describedby="location-select-label" :options="locationStore.locations" label="name" v-model="newEvent.location"></v-select>
                </div>

                <!-- artists -->
                <div class="input-group mb-3">
                    <span class="input-group-text" id="artist-select-label">Künstler *</span>
                    <v-select 
                        multiple
                        :class="`form-control ${errorClass('artists')}`" 
                        aria-label="Künstler" 
                        aria-describedby="artist-select-label" 
                        :options="artistStore.artists" 
                        label="name" 
                        v-model="newEvent.artists"
                    ></v-select>
                </div>

                <button type="button" class="btn btn-primary" @click="checkInputs()">
                    Event Speichern
                </button>
            </div>
            <div class="col">
                <h3>Vorschau</h3>
                <EventPreview
                    :event="newEvent"
                    :flyer="flyerUrl"
                />
            </div>
        </div>
    </div>
</template>
<script setup>
import { inject, onMounted, reactive, ref } from "vue";
import EventPreview from '../layouts/EventPreview.vue';
import { useLocationStore } from '../../stores/LocationStore.js';
import { useArtistStore } from '../../stores/ArtistStore.js';

const locationStore = useLocationStore();
const artistStore = useArtistStore();

const axios = inject('axios');

const emit = defineEmits([
    'event-created'
]);

const flyerUrl = ref(null);

const newEvent = reactive({
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

const handleFile = (input) => {
    setTimeout(() => {
        newEvent.flyer = input.target.files[0];
        flyerUrl.value = URL.createObjectURL(newEvent.flyer);
    }, 1);
}

const selectArtist = (artist) => {
    newEvent.artists.push(artist);
}

const deselectArtist = (artist) => {
    newEvent.artists.forEach((a, i) => {
        if (a.id == artist.id) {
            newEvent.artists.splice(i, 1);
            return;
        }
    })
}

const checkInputs = () => {
    let allInputsOkay = true;

    for (let [key, val] of Object.entries(hasError)) {
        if (newEvent[key] == null || newEvent[key].length == 0) {
            hasError[key] = true;
            allInputsOkay = false;
        } else hasError[key] = false;
    }

    if (allInputsOkay) saveEvent();
}

const saveEvent = () => {
    let formData = new FormData();
    formData.append('name', newEvent.name);
    formData.append('description', newEvent.description);
    formData.append('flyer', newEvent.flyer);
    formData.append('date', newEvent.date);
    formData.append('doors', newEvent.doors);
    formData.append('begin', newEvent.begin);
    formData.append('ticketLink', newEvent.ticketLink);
    formData.append('location', newEvent.location.id);
    formData.append('artists', JSON.stringify(newEvent.artists.map(a => a.id)));

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

</script>