<template>
    <div class="modal fade modal-xl" id="new-location-modal" ref="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Neue Venue</h3>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="name-input-label">Name *</span>
                        <input type="text" :class="`form-control ${errorClass('name')}`" aria-label="Name" aria-describedby="name-input-label" v-model="newLocation.name">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="street-input-label">Straße und Hausnummer</span>
                        <input type="text" class="form-control" aria-label="Straße und Hausnummer" aria-describedby="street-input-label" v-model="newLocation.streetAndNumber">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="city-input-label">Stadt *</span>
                        <input type="text" :class="`form-control ${errorClass('city')}`" aria-label="Stadt" aria-describedby="city-input-label" v-model="newLocation.city">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="website-input-label">Webseite</span>
                        <input type="text" class="form-control" aria-label="Webseite" aria-describedby="website-input-label" v-model="newLocation.website">
                    </div>

                    <div class="input-group mb-3">
                        <div class="radio-group-label form-check-inline">
                            Gibt es ausreichend Parkplätze vor Ort? *
                        </div>
                        
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="location-parking-radios" id="location-parking-radios-1" value="unknown" v-model="newLocation.parking">
                            <label class="form-check-label" for="location-parking-radios-1">
                                Nicht bekannt
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="location-parking-radios" id="location-parking-radios-2" value="possible" v-model="newLocation.parking">
                            <label class="form-check-label" for="location-parking-radios-2">
                                Genug Parkplätze vorhanden
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="location-parking-radios" id="location-parking-radios-3" value="impossible" v-model="newLocation.parking">
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
                            <input class="form-check-input" type="radio" name="location-barrierfree-radios" id="location-barrierfree-radios-1" value="unknown" v-model="newLocation.barrierFree">
                            <label class="form-check-label" for="location-barrierfree-radios-1">
                                Nicht bekannt
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="location-barrierfree-radios" id="location-barrierfree-radios-2" value="possible" v-model="newLocation.barrierFree">
                            <label class="form-check-label" for="location-barrierfree-radios-2">
                                Venue ist barrierefrei
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="location-barrierfree-radios" id="location-barrierfree-radios-3" value="impossible" v-model="newLocation.barrierFree">
                            <label class="form-check-label" for="location-barrierfree-radios-3">
                                Venue ist nicht barrierefrei
                            </label>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text">Beschreibung **</span>
                        <textarea class="form-control" aria-label="Description" v-model="newLocation.description"></textarea>
                    </div>

                    <div class="input-group mb-3">
                        <p>* Benötigt</p>
                        <p>** Eventuelle Erörterungen zu Parkplätzen und Barrierefreiheit.</p>
                    </div>

                    <div class="input-group mb-3">
                        <button type="submit" @click="checkInputs()" class="btn btn-primary">
                            Save Venue
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { inject, reactive, ref } from "vue";
import { Modal } from 'bootstrap';
import { useLocationStore } from '../../stores/LocationStore.js';
const locationStore = useLocationStore();

const axios = inject('axios');

const emit = defineEmits([
    'location-saved'
]);

const newLocation = reactive({
    name: null,
    streetAndNumber: null,
    city: null,
    website: null,
    parking: "unknown",
    barrierFree: "unknown",
    description: null
});

const hasError = reactive({
    name: false,
    city: false,
})

const modal = ref(null);

const resetForm = () => {
    newLocation.name = null;
    newLocation.streetAndNumber = null,
    newLocation.city = null,
    newLocation.website = null,
    newLocation.parking = "unknown",
    newLocation.barrierFree = "unknown",
    newLocation.description = null

    hasError.name = false;
    hasError.city = false;
}

const errorClass = (input) => {
    return hasError[input] ? 'is-invalid' : '';
}

const checkInputs = () => {
    let allInputsOkay = true;

    for (let [key, val] of Object.entries(hasError)) {
        if (newLocation[key] == null) {
            hasError[key] = true;
            allInputsOkay = false;
        } else hasError[key] = false;
    }

    if (allInputsOkay) saveLocation();
}

const saveLocation = () => {
    axios.post(
        '/locations', 
        newLocation
    ).then((response) => {
        locationStore.addNewLocation(response.data);
        emit('location-saved', response.data);;
        Modal.getInstance(modal.value).hide();
        resetForm();
    }).catch((error) =>  {
        console.warn(error)
    })
}

</script>
