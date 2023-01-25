<template>
    <div class="modal fade update-line-up-modal" id="update-line-up-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">Line-Up aktualisieren</div>
                <div class="modal-body">
                    <div v-for="(artist, index) in lineup" :key="index">
                        {{ artist.name }}
                        
                        <button
                            type="button"
                            class="btn btn-primary"
                            @click="showConfirmation = true"
                        >
                            <FontAwesomeIcon icon="fa-solid fa-trash" />
                        </button>

                        <span v-if="showConfirmation">
                            Sicher?

                            <button
                                type="button"
                                class="btn btn-secondary"
                                @click="removeArtistFromLineUp(artist.id)"
                            >
                                <FontAwesomeIcon icon="fa-solid fa-check" />
                            </button>

                            <button
                                type="button"
                                class="btn btn-secondary"
                                @click="showConfirmation = false"
                            >
                                <FontAwesomeIcon icon="fa-solid fa-times" />
                            </button>
                        </span>

                    </div>

                    <button
                        type="button"
                        class="btn btn-secondary"
                        @click="showArtistSelect()"
                    >
                        Neuen Künstler hinzufügen
                    </button>

                    <!-- v select -->
                    <!-- button for add -->
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref } from "vue";
import { useArtistStore } from "../../stores/ArtistStore";
const artistStore = useArtistStore();

const props = defineProps({
    lineup: {
        required: true,
        type: Array,
        default: []
    }
});

const artists = ref(null);
const showConfirmation = ref(false);

const getArtists = () => {
    artists.value = artistStore.getArtists;
}

onMounted(() => {
    getArtists();
})
</script>