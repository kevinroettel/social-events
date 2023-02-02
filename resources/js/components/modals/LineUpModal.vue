<template>
    <div class="modal fade update-line-up-modal" id="update-line-up-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 clas="modal-title">Line-Up aktualisieren</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <LineUpArtist
                        v-for="(artist, index) in lineup"
                        :key="index"
                        :artist="artist"
                        :eventId="eventId"
                        @artist-removed="artistRemoved($event)"
                    />

                    <button
                        type="button"
                        class="btn btn-secondary mt-2"
                        @click="showArtistSelection = true"
                    >
                        Neuen Künstler hinzufügen
                    </button>

                    <div v-if="showArtistSelection">
                        <p v-if="showNotifcation">Bevor Sie den Künstler endgültig erstellen, gehen Sie bitte sicher sie den Namen korrekt geschrieben haben.</p>
                        
                        <v-select 
                            taggable 
                            class="form-control" 
                            aria-label="Künstler" 
                            aria-describedby="artist-select-label" 
                            :options="artistStore.artists" 
                            label="name" 
                            v-model="newArtist"
                            @option:created="showNotifcation = true"
                        ></v-select>

                        <button
                            type="button"
                            class="btn btn-primary"
                            @click="addArtistToLineUp()"
                        >
                            Hinzufügen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import LineUpArtist from '../layouts/LineUpArtist.vue';
import { onMounted, ref } from "vue";
import { useArtistStore } from "../../stores/ArtistStore";
import { toast } from '../helpers/toast';
const artistStore = useArtistStore();

const props = defineProps({
    lineup: {
        required: true,
        type: Array,
        default: []
    },

    eventId: {
        required: true,
        type: Number,
        default: null
    }
});

const emit = defineEmits([
    'artist-removed',
    'added-artist'
]);

const newArtist = ref(null);
const showArtistSelection = ref(false);
const showNotifcation = ref(false);

const addArtistToLineUp = () => {
    if (newArtist.value.id !== undefined) {
        axios.patch(
            `/events/${props.eventId}/artists/${newArtist.value.id}`
        ).then((response) => {
            emit('added-artist', newArtist.value);
        }).catch((error) => {
            toast(error.message, 'error');
        })
    } else {
        createArtist()
    }
}

const createArtist = () => {
    axios.post(
        '/artists',
        newArtist.value
    ).then((response) => {
        newArtist.value = response.data;
        addArtistToLineUp();
    }).catch((error) => {
        toast(error.message, 'error');
    })
}

const artistRemoved = (artistId) => emit('artist-removed', artistId);

</script>