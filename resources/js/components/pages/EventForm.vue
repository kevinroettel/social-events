<template>
    <div>
        <LocationFormModal 
            @location-saved="selectCreated($event)"
        />

        <LineUpModal
            v-if="isUpdate"
            :lineup="event.artists"
            :eventId="eventToUpdate"
            @added-artist="addArtist($event)"
            @artist-removed="removeArtist($event)"
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

                <div class="row">
                    <div class="col">
                        <!-- doors -->
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="doors-input-label">Einlass</span>
                            <input type="time" :class="`form-control`" aria-label="Einlass" aria-describedby="doors-input-label" v-model="event.doors">
                        </div>
                    </div>
                    <div class="col">
                        <!-- begin -->
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="begin-input-label">Beginn *</span>
                            <input type="time" :class="`form-control ${errorClass('begin')}`" aria-label="Beginn" aria-describedby="begin-input-label" v-model="event.begin">
                        </div>
                    </div>
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
                    >
                       <template v-slot:no-options="{ search, searching }">
                            <template v-if="searching">
                                <em>{{ search }}</em> konnte nicht gefunden werden. Vergewissere dich ob du den Namen richtig geschrieben hast. Ansonsten kannst du die Venue über den rechten Button erstellen.
                            </template>
                            <em v-else style="opacity: 0.5">Fange an zu Tippen um eine Venue zu finden.</em>
                        </template>
                    </v-select>
                    <button type="button" class="btn btn-primary input-group-text" data-bs-toggle="modal" data-bs-target="#new-location-modal">
                        Neue Venue erstellen
                    </button>
                </div>

                <!-- artists -->
                <p v-if="showNotifcation">Bevor Sie das Event erstellen, gehen Sie bitte sicher das sie alle Künstler korrekt geschrieben haben.</p>
                <div v-if="eventToUpdate == null" class="input-group mb-3">
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
                    >
                        <template v-slot:no-options="search">
                            <em style="opacity: 0.5">Hier die Künstler eingeben.</em>
                        </template>
                    </v-select>
                </div>
                
                <span v-else>
                    <button
                        type="button"
                        class="btn btn-secondary w-auto ml-1"
                        data-bs-toggle="modal" 
                        data-bs-target="#update-line-up-modal"
                    >
                        Line-up ändern
                    </button>
                </span>

                <p>* Benötigte Eingaben</p>

                <button type="button" class="btn btn-primary" @click="checkInputs()">
                    <span v-if="isUpdate">Bearbeitung speichern</span>
                    <span v-else>Event Speichern</span>
                </button>

                <button v-if="isUpdate" type="button" class="btn btn-secondary ml-2" @click="discardUpdate()">
                    Bearbeitung abbrechen
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
import LineUpModal from '../modals/LineUpModal.vue';
import EventPreview from '../layouts/EventPreview.vue';
import LocationFormModal from '../modals/LocationFormModal.vue';
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
    'event-created',
    'discard-update',
    'event-updated'
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
    if (eventData.event != undefined) eventData = eventData.event;
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

const addArtist = (artist) => {
    event.artists.push(artist);
}

const removeArtist = (artistId) => {
    event.artists = event.artists.filter(artist => artist.id !== artistId);
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

    if (allInputsOkay) {
        if (props.eventToUpdate == null) createEvent();
        else updateEvent();
    }
    
}

const createEvent = () => {
    axios.post(
        '/events',
        getFormData()
    ).then((response) => {
        emit('event-created', response.data);
    }).catch((error) => {
        console.log(error);
    })
}

const updateEvent = () => {
    axios.post(
        `/events/${props.eventToUpdate}`,
        getFormData()
    ).then((response) => {
        console.log(response.data)
        emit('event-updated', response.data);
    }).catch((error) => {
        console.log(error);
    })
}

const getFormData = () => {
    let formData = new FormData();
    formData.append('name', event.name);
    if (event.description != null) formData.append('description', event.description);
    if (event.flyer != null) formData.append('flyer', event.flyer);
    formData.append('date', event.date);
    if (event.doors != null) formData.append('doors', event.doors);
    formData.append('begin', event.begin);
    if (event.ticketLink != null) formData.append('ticketLink', event.ticketLink);
    formData.append('location', event.location.id);
    formData.append('artists', JSON.stringify(event.artists.map(a => a.id ?? (a.name ?? a))));
    return formData;
}

const errorClass = (input) => {
    return hasError[input] ? 'is-invalid' : '';
}

const selectCreated = (location) => {
    event.location = location
}

const discardUpdate = () => {
    emit('discard-update');
}

onMounted(() => {
    if (props.eventToUpdate != null) {
        isUpdate.value = true
        getEventData();
    }
})
</script>