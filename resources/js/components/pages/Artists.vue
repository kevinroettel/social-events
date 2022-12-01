<template>
    <div>
        <h3>Künstler</h3>
        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample">
            Neuer Künstler
        </button>
        <div class="collapse" id="collapseExample">
            <div class="card card-body">
                <div class="row">
                    <div class="input-group col">
                        <span class="input-group-text" id="name-input-label">Name *</span>
                        <input type="text" :class="`form-control`" aria-label="Name" aria-describedby="name-input-label" v-model="newArtist.name">
                    </div>

                    <div class="input-group col">
                        <span class="input-group-text" id="website-input-label">Webseite</span>
                        <input type="text" :class="`form-control`" aria-label="Webseite" aria-describedby="website-input-label" v-model="newArtist.website">
                    </div>

                    <div class="input-group col">
                        <button class="btn btn-primary" type="button" @click="saveArtist()">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Webseite</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <tr 
                    v-for="(artist, index) in artists"
                    :key="index"
                >
                    <td>{{ artist.name }}</td>
                    <td><a :href="artist.website" target="_blank">{{ artist.website }}</a></td>
                    <td>
                        <button
                            type="button"
                            class="btn btn-secondary"
                            @click="openTagModal()"
                        >
                            Tags
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script setup>
import { inject, onMounted, reactive, ref } from "vue";
import { useArtistStore } from '../../stores/ArtistStore.js';

const store = useArtistStore();

const axios = inject('axios');

const artists = ref([]);

const newArtist = reactive({
    name: null,
    website: null,
});

const resetForm = () => {
    newArtist.name = null;
    newArtist.website = null;
}

const saveArtist = () => {
    axios.post(
        '/artists',
        newArtist
    ).then((response) => {
        store.addNewArtist(response.data);
        resetForm();
        getArtists();
    }).catch((error) => {
        console.log(error);
    })
}

const getArtists = () => {
    artists.value = store.getArtists;
}

onMounted(() => {
    getArtists();
});
</script>