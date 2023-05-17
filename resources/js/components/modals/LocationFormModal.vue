<template>
    <div class="modal fade modal-xl" id="location-form-modal" ref="locationmodal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 v-if="locationToUpdate == null">Neue Venue</h3>
                    <h3 v-else>Venue Aktualisieren</h3>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="name-input-label">Name *</span>
                        <input type="text" :class="`form-control ${errorClass('name')}`" aria-label="Name" aria-describedby="name-input-label" v-model="location.name">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="street-input-label">Straße und Hausnummer</span>
                        <input type="text" class="form-control" aria-label="Straße und Hausnummer" aria-describedby="street-input-label" v-model="location.streetAndNumber">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="city-input-label">Stadt *</span>
                        <input type="text" :class="`form-control ${errorClass('city')}`" aria-label="Stadt" aria-describedby="city-input-label" v-model="location.city">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="website-input-label">Webseite</span>
                        <input type="text" class="form-control" aria-label="Webseite" aria-describedby="website-input-label" v-model="location.website">
                    </div>

                    <div class="input-group mb-3">
                        <div class="radio-group-label form-check-inline">
                            Gibt es ausreichend Parkplätze vor Ort? *
                        </div>
                        
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="location-parking-radios" id="location-parking-radios-1" value="unknown" v-model="location.parking">
                            <label class="form-check-label" for="location-parking-radios-1">
                                Nicht bekannt
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="location-parking-radios" id="location-parking-radios-2" value="possible" v-model="location.parking">
                            <label class="form-check-label" for="location-parking-radios-2">
                                Genug Parkplätze vorhanden
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="location-parking-radios" id="location-parking-radios-3" value="impossible" v-model="location.parking">
                            <label class="form-check-label" for="location-parking-radios-3">
                                Keine Parkplätze vorhanden
                            </label>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <div class="radio-group-label form-check-inline">
                            Ist die Venue barrierefrei? *
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="location-barrierfree-radios" id="location-barrierfree-radios-1" value="unknown" v-model="location.barrierFree">
                            <label class="form-check-label" for="location-barrierfree-radios-1">
                                Nicht bekannt
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="location-barrierfree-radios" id="location-barrierfree-radios-2" value="possible" v-model="location.barrierFree">
                            <label class="form-check-label" for="location-barrierfree-radios-2">
                                Venue ist barrierefrei
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="location-barrierfree-radios" id="location-barrierfree-radios-3" value="impossible" v-model="location.barrierFree">
                            <label class="form-check-label" for="location-barrierfree-radios-3">
                                Venue ist nicht barrierefrei
                            </label>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text">Beschreibung **</span>
                        <textarea class="form-control" aria-label="Description" v-model="location.description"></textarea>
                    </div>

                    <span>* Benötigte Angaben</span>
                    <br>
                    <span>** Eventuelle Erörterungen zu Parkplätzen und Barrierefreiheit.</span>

                    <div class="input-group mb-3">
                        <button type="submit" @click="checkInputs()" class="btn btn-primary">
                            Venue Speichern
                        </button>

                        <button type="submit" @click="resetForm()" class="btn btn-secondary">
                            Abbrechen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { inject, onMounted, reactive, ref } from "vue";
import { Modal } from 'bootstrap';
import { useLocationStore } from '../../stores/LocationStore.js';
import { toast } from "../helpers/toast.js";
import { getLongAndLat } from "../helpers/geoCoding.js";
const locationStore = useLocationStore();

const axios = inject('axios');

const props = defineProps({
    locationToUpdate: {
        required: false,
        type: Object,
        default: null
    }
});

const emit = defineEmits([
    'location-saved',
    'location-updated'
]);

const location = reactive({
    name: null,
    streetAndNumber: null,
    city: null,
    website: null,
    parking: "unknown",
    barrierFree: "unknown",
    description: null,
    latitude: null,
    longitude: null
});

const hasError = reactive({
    name: false,
    city: false,
})

const locationmodal = ref(null);

const resetForm = () => {
    Modal.getInstance(locationmodal.value).hide();

    location.name = null;
    location.streetAndNumber = null;
    location.city = null;
    location.website = null;
    location.parking = "unknown";
    location.barrierFree = "unknown";
    location.description = null;
    location.latitude = null;
    location.longitude = null;

    hasError.name = false;
    hasError.city = false;
}

const errorClass = (input) => {
    return hasError[input] ? 'is-invalid' : '';
}

const checkInputs = () => {
    let allInputsOkay = true;

    for (let [key, val] of Object.entries(hasError)) {
        if (location[key] == null) {
            hasError[key] = true;
            allInputsOkay = false;
        } else hasError[key] = false;
    }

    if (allInputsOkay) {
        getLongAndLat(
            location.name,
            location.city,
            location.streetAndNumber
        ).then((response) => {
            location.latitude = response.latitude;
            location.longitude = response.longitude;

            if (props.locationToUpdate == null) saveLocation();
            else updateLocation();
        }).catch((error) => {
            console.log('should not happen tbh: ' + error);
        })
    }
}

const saveLocation = () => {
    axios.post(
        '/locations', 
        location
    ).then((response) => {
        locationStore.addNewLocation(response.data);
        emit('location-saved', response.data);
        resetForm();
    }).catch((error) =>  {
        toast(error.message, 'error');
    })
}

const updateLocation = () => {
    axios.post(
        `/locations/${props.locationToUpdate.id}`,
        location
    ).then((response) => {
        locationStore.updateLocation(response.data);
        emit('location-updated', response.data);
        resetForm();
    }).catch((error) => {
        toast(error.message, 'error');
    })
}

onMounted(() => {
    if (props.locationToUpdate != null) {
        location.name = props.locationToUpdate.name
        location.streetAndNumber = props.locationToUpdate.streetAndNumber
        location.city = props.locationToUpdate.city
        location.website = props.locationToUpdate.website
        location.parking = props.locationToUpdate.parking
        location.barrierFree = props.locationToUpdate.barrierFree
        location.description = props.locationToUpdate.description
        location.latitude = props.locationToUpdate.latitude
        location.longitude = props.locationToUpdate.longitude
    }
})
</script>
