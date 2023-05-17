<template>
    <div class="modal fade modal-lg" id="artist-form-modal" ref="artistmodal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Neuen Künstler erstellen
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="name-input-label">Name *</span>
                        <input type="text" :class="`form-control ${errorClass('name')}`" aria-label="Name" aria-describedby="name-input-label" v-model="newArtist.name" @keyup="showNotifcationWithSimilarArtists()">
                    </div>

                    <p>Bevor Sie den Künstler erstellen, gehen Sie bitte sicher das sie ihn korrekt geschrieben haben.</p>
                    <p v-if="notificationText != null">{{ notificationText }}</p>

                    <div class="d-inline">
                        <div class="input-group">
                            <v-select
                                taggable
                                class="form-control"
                                :options="availableTags"
                                label="name"
                                v-model="newTag"
                                placeholder="Hier kannst du Tags eingeben welche den Künstler ein wenig beschreiben."
                            ></v-select>

                            <button
                                type="button"
                                class="btn btn-primary input-group-text"
                                @click="addTagToArtist()"
                            >
                                Hinzufügen
                            </button>
                        </div>
                    </div>

                    <div class="d-flex mt-2">
                        <div
                            v-for="(tag, index) in newArtist.tags"
                            :key="index"
                            class="m-1"
                        >
                            <span class="artist-tag">
                                {{ tag.name }} 
                                <button 
                                    class="btn btn-secondary" 
                                    @click="removeTagFromArtist(index)"
                                >
                                    X
                                </button>
                            </span>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <button type="submit" @click="checkInputs()" class="btn btn-primary">
                            Künstler Speichern
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
import { inject, onMounted, reactive, ref } from 'vue';
import { useArtistStore } from '../../stores/ArtistStore';
import { Modal } from 'bootstrap';
import { compareTwoStrings } from 'string-similarity';

const artistStore = useArtistStore();
const axios = inject('axios');

const emit = defineEmits([
    'artist-saved'
]);

const newArtist = reactive({
    name: null,
    tags: [],
});

const hasError = reactive({
    name: false,
    tags: false
});

const artistmodal = ref(null);

const availableTags = ref([]);
const newTag = ref(null);
const artistId = ref(null);

const notificationText = ref(null);

const errorClass = (input) => {
    return hasError[input] ? 'is-invalid' : '';
}

const resetForm = () => {
    Modal.getInstance(artistmodal.value).hide();
    newArtist.name = null;
    newArtist.tags = [];

    hasError.name = false;
    hasError.tags = false;
}

const getAllAvailableTags = () => {
    axios.get(
        '/tags'
    ).then((response) => {
        availableTags.value = response.data;
    }).catch((error) => {
        toast(error.message, 'error');
    })
}

const showNotifcationWithSimilarArtists = () => {
    let similar = [];
    let allArtists = artistStore.getArtists;

    allArtists.forEach(artist => {
        let similarity = compareTwoStrings(newArtist.name.toLowerCase(), artist.name.toLowerCase());

        if (similarity > 0.6) similar.push(artist.name);
    });

    if (similar.length != 0) {
        notificationText.value = "Dein eingegebener Künstler ist sehr ähnlich zu den folgenden vorhandenen Künstlern: ";
        similar.forEach((artist, index) => notificationText.value += artist + (index == similar.length - 1 ? "" : ", "));
    } else {
        notificationText.value = null
    }
}

const addTagToArtist = () => {
    newArtist.tags.push(newTag.value);
    newTag.value = null;
}

const removeTagFromArtist = (index) => {
    newArtist.tags.splice(index, 1);
}

const checkInputs = () => {
    if (newArtist.name == null) hasError.name = true;
    else hasError.name = false;

    if (newArtist.tags.length == 0) hasError.tags = true;
    else hasError.tags = false;

    if (hasError.name || hasError.tags) return;
    
    saveArtist();
}

const saveArtist = () => {
    axios.post(
        `/artists`,
        {name: newArtist.name}
    ).then((response) => {
        artistId.value = response.data.id;
        emit('artist-saved', response.data);
        addTagsToArtist();
    }).catch((error) => {
        console.log(error)
    })
}

const addTagsToArtist = () => {
    newArtist.tags.forEach((tag) => {
        axios.patch(
            `/artists/${artistId.value}/tags/${tag.id ?? tag.name}`
        ).then((response) => {
            resetForm();
        }).catch((error) => {
            console.log(error)
        })
    })
}

onMounted(() => {
    getAllAvailableTags();
})
</script>