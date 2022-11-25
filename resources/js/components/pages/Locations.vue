<template>
    <div>
        <h3>Locations</h3>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#new-location-modal">New Location</button>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Website</th>
                    <th>Parking</th>
                    <th>Barrier-free access</th>
                    <th>Description</th>
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
                    <td>{{ location.parking }}</td>
                    <td>{{ location.barrierFree }}</td>
                    <td>{{ location.description }}</td>
                </tr>
            </tbody>
        </table>

        <LocationFormModal 
            @location-saved="locationSaved($event)"
        />
    </div>
</template>
<script setup>
import { inject, onMounted, ref } from 'vue';
import LocationFormModal from './LocationFormModal.vue';

const props = defineProps({
    locationsData: {
        required: true,
        type: Array,
        default: []
    },
});

const locations = ref([]);

const getAddress = (location) => {
    return location.streetAndNumber == null ?
        location.city : 
        location.streetAndNumber + ", " + location.city;
}

const locationSaved = (newLocation) => {
    locations.value.push(newLocation);
}

onMounted(() =>  {
    locations.value = props.locationsData;
})

</script>