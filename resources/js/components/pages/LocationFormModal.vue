<template>
    <div class="modal fade modal-xl" id="new-location-modal" ref="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>New Location</h3>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="name-input-label">Location Name *</span>
                        <input type="text" :class="`form-control ${errorClass('name')}`" aria-label="Location Name" aria-describedby="name-input-label" v-model="newLocation.name">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="street-input-label">Street/Number</span>
                        <input type="text" class="form-control" aria-label="Street/Number" aria-describedby="street-input-label" v-model="newLocation.streetAndNumber">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="city-input-label">City *</span>
                        <input type="text" :class="`form-control ${errorClass('city')}`" aria-label="City" aria-describedby="city-input-label" v-model="newLocation.city">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="website-input-label">Website</span>
                        <input type="text" class="form-control" aria-label="Website" aria-describedby="website-input-label" v-model="newLocation.website">
                    </div>

                    <div class="input-group mb-3">
                        <div class="radio-group-label form-check-inline">
                            How is the parking situation at the venue? *
                        </div>
                        
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="location-parking-radios" id="location-parking-radios-1" value="unknown" v-model="newLocation.parking">
                            <label class="form-check-label" for="location-parking-radios-1">
                                Parking Situation unknwon
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="location-parking-radios" id="location-parking-radios-2" value="possible" v-model="newLocation.parking">
                            <label class="form-check-label" for="location-parking-radios-2">
                                Parking is possible
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="location-parking-radios" id="location-parking-radios-3" value="impossible" v-model="newLocation.parking">
                            <label class="form-check-label" for="location-parking-radios-3">
                                Parking not possible
                            </label>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <div class="radio-group-label form-check-inline">
                            Is the venue barrier free? *
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="location-barrierfree-radios" id="location-barrierfree-radios-1" value="unknown" v-model="newLocation.barrierFree">
                            <label class="form-check-label" for="location-barrierfree-radios-1">
                                Barrier-Free unknown
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="location-barrierfree-radios" id="location-barrierfree-radios-2" value="possible" v-model="newLocation.barrierFree">
                            <label class="form-check-label" for="location-barrierfree-radios-2">
                                Venue is Barrier-Free
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="location-barrierfree-radios" id="location-barrierfree-radios-3" value="impossible" v-model="newLocation.barrierFree">
                            <label class="form-check-label" for="location-barrierfree-radios-3">
                                Venue is not Barrier-Free
                            </label>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text">Description</span>
                        <textarea class="form-control" aria-label="Description" v-model="newLocation.description"></textarea>
                    </div>

                    <div class="input-group mb-3">
                        * Required
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

const errorClass = (input) => {
    return hasError[input] ? 'is-invalid' : '';
}

const checkInputs = () => {
    const allInputsOkay = true;

    for (let [key, val] of Object.entries(hasError)) {
        if (newLocation[key] == null) {
            hasError[key] = true;
            allInputsOkay = false;
        } else hasError[key] = false;
    }

    if (allInputsOkay) {
        saveLocation();
    }
}

const saveLocation = () => {
    axios.post(
        '/locations', 
        newLocation
    ).then((response) => {
        emit('location-saved', response.data);
        Modal.getInstance(modal.value).hide();
    }).catch((error) =>  {
        console.warn(error)
    })
}

</script>
