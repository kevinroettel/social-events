<template>
    <div>
        <h3>Locations</h3>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#new-location-modal">New Location</button>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Addresse</th>
                    <th>Webseite</th>
                    <th>Parken</th>
                    <th>Barrierefreier Eintritt</th>
                    <th>Beschreibung</th>
                </tr>
            </thead>
            <tbody>
                <tr 
                    v-for="(location, index) in locations"
                    :key="index"
                >
                    <td>{{ location.name }}</td>
                    <td>{{ getAddress(location) }}</td>
                    <td><a :href="location.website" target="_blank">{{ location.website }}</a></td>
                    <td>{{ getTranslation(location.parking) }}</td>
                    <td>{{ getTranslation(location.barrierFree) }}</td>
                    <td>{{ location.description }}</td>
                </tr>
            </tbody>
        </table>

        <LocationFormModal 
            @location-saved="getLocations()"
        />
    </div>
</template>
<script setup>
import { inject, onMounted, ref } from 'vue';
import LocationFormModal from './LocationFormModal.vue';
import { useLocationStore } from '../../stores/LocationStore.js';

const store = useLocationStore();

const emit = defineEmits([
    'location-saved'
]);

const locations = ref([]);

const getAddress = (location) => {
    return location.streetAndNumber == null ?
        location.city : 
        location.streetAndNumber + ", " + location.city;
}

const getTranslation = (val) => {
    switch (val) {
        case 'unkown': return "Unbekannt";
        case 'possible' : return "Möglich";
        case 'impossible': return "Nicht möglich";
    }
}

const getLocations = () => {
    locations.value = store.getLocations;
}

onMounted(() =>  {
    getLocations();
})

</script>