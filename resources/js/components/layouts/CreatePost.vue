<template>
    <div class="mb-2">
        <button
            v-if="!showPostForm"
            type="button"
            class="btn btn-primary"
            @click="showPostForm = true"
        >
            {{ answerTo == null ? "Eigenen Beitrag erstellen" : "Kommentar hinzufügen"}}
        </button>

        <div v-else-if="showPostForm">
            <div class="input-group">
                <textarea 
                    class="form-control" 
                    placeholder="Ihr Beitrag" 
                    id="post-content-textarea" 
                    style="height: 100px" 
                    aria-describedby="button-addon"
                    v-model="post.content"
                ></textarea>
                
                <button 
                    type="button" 
                    id="button-addon"
                    class="btn btn-primary input-group-text" 
                    @click="postContribution()"
                >
                    Posten
                </button>
            </div>
        </div>
    </div>
</template>
<script setup>
import { inject, onMounted, reactive, ref } from "vue";
import { useUserStore } from '../../stores/UserStore.js';
import { toast } from "../helpers/toast.js";
const userStore = useUserStore();

const axios = inject('axios');

const props = defineProps({
    eventId: {
        required: true,
        type: Number,
        default: null,
    },

    answerTo: {
        required: false,
        type: Number,
        default: null
    }
});

const emit = defineEmits([
    'created-post'
])

const showPostForm = ref(false);

const post = reactive({
    event_id: null,
    user_id: null,
    content: null,
    answerTo: null
});

const postContribution = () => {
    if (post.content == null || post.content == "") {
        document.getElementById('post-content-textarea').classList.add('is-invalid');
        return;
    }

    axios.post(
        `/events/${props.eventId}/posts`,
        post
    ).then((response) => {
        emit('created-post', response.data);
    }).catch((error) => {
        toast(error.message, 'error');
    })
}

onMounted(() => {
    post.event_id = props.eventId;
    post.user_id = userStore.getUserId;
    post.answerTo = props.answerTo;
});
</script>