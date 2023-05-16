<template>
    <div v-if="artistTags != null">
        <div class="d-flex mb-2">
            <div
                v-for="(tag, index) in artistTags"
                :key="index"
                class="m-1"
            >
                <span class="artist-tag">
                    {{ tag.name }} 
                    <button 
                        class="btn btn-secondary" 
                        @click="removeTagFromArtist(tag.id)"
                    >
                        X
                    </button>
                </span>
            </div>
        </div>

        <div class="d-inline">
            <div class="input-group">
                <v-select
                    taggable
                    class="form-control w-25"
                    :options="availableTags"
                    label="name"
                    v-model="newTag"
                    placeholder="Hier kannst du Tags eingeben um sie dem Künstler hinzuzufügen."
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
    </div>
</template>
<script setup>
import { inject, onMounted, ref } from "vue";
import { toast } from "../helpers/toast";

const axios = inject('axios');

const props = defineProps({
    artistTags: {
        required: true,
        type: Array,
        default: null
    },
});

const emit = defineEmits([
    'tag-added',
    'tag-removed'
]);

const availableTags = ref([]);
const newTag = ref(null);

const getAllAvailableTags = () => {
    axios.get(
        '/tags'
    ).then((response) => {
        availableTags.value = response.data;
    }).catch((error) => {
        toast(error.message, 'error');
    })
}

const addTagToArtist = () => {
    if (newTag.value.hasOwnProperty('id')) {
        emit('tag-added', newTag.value);
        newTag.value = null;
    } else {
        createTag();
    }
}

const createTag = () => {
    axios.post(
        '/tags', {
            name: (newTag.value.name != undefined ? newTag.value.name : newTag.value)
        }
    ).then((response) => {
        emit('tag-added', response.data);
        newTag.value = null;
    }).catch((error) => {
        toast(error.message, 'error');
    })
}

const removeTagFromArtist = (tagId) => {
    emit('tag-removed', tagId)
}

onMounted(() => {
    getAllAvailableTags();
})
</script>