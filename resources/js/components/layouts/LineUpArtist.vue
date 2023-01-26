<template>
    <div class="mt-1">
        {{ artist.name }}
        
        <button
            type="button"
            class="btn btn-primary"
            @click="showConfirmation = true"
        >
            <FontAwesomeIcon icon="fa-solid fa-trash" />
        </button>

        <span v-if="showConfirmation">
            Wirklich aus Line-Up entfernen?

            <button
                type="button"
                class="btn btn-secondary ml-1"
                @click="removeArtistFromLineUp()"
            >
                <FontAwesomeIcon icon="fa-solid fa-check" />
            </button>

            <button
                type="button"
                class="btn btn-secondary ml-1"
                @click="showConfirmation = false"
            >
                <FontAwesomeIcon icon="fa-solid fa-times" />
            </button>
        </span>

    </div>
</template>
<script setup>
import { inject, ref } from "vue";

const axios = inject('axios');

const props = defineProps({
    artist: {
        required: true,
        type: Object,
        default: null
    },

    eventId: {
        required: true,
        type: Number,
        default: null
    }
});

const emit = defineEmits([
    'artist-removed'
]);

const showConfirmation = ref(false);

const removeArtistFromLineUp = () => {
    axios.delete(
        `/events/${props.eventId}/artists/${props.artist.id}`
    ).then((response) => {
        emit('artist-removed', props.artist.id)
    }).catch((error) => {
        console.log(error);
    })
}
</script>