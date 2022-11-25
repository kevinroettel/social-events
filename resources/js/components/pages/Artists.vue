<template>
    <div>
        <h3>Artists</h3>
        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample">
            New Artist
        </button>
        <div class="collapse" id="collapseExample">
            <div class="card card-body">
                <div class="input-group mb-3">
                    <span class="input-group-text" id="name-input-label">Artist Name *</span>
                    <input type="text" :class="`form-control ${errorClass('name')}`" aria-label="Artist Name" aria-describedby="name-input-label" v-model="newArtist.name">
                </div>

                <div class="input-group mb-3">
                    <span class="input-group-text" id="website-input-label">Website</span>
                    <input type="text" :class="`form-control`" aria-label="Website" aria-describedby="website-input-label" v-model="newArtist.website">
                </div>
            </div>
        </div>


        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Website</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                <tr 
                    v-for="(artist, index) in artists"
                    :key="index"
                >
                    <td>{{ artist.name }}</td>
                    <td><a :href="artist.website" target="_blank">{{ artist.website }}</a></td>
                    <td>{{ artist.type }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script setup>
import { onMounted, ref } from "vue";


const props = defineProps({
    artistsData: {
        required: true,
        type: Array,
        default: []
    },
});

const artists = ref([]);

onMounted(() => {
    artists.value = props.artistsData;
});
</script>