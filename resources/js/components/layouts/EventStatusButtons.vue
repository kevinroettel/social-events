<template>
    <div>
        <button 
            v-if="status != 'interested'"
            class="btn btn-primary" 
            type="button" 
            @click="changeStatus('interested')"
        >
            Interessiert
        </button>
        <button
            v-else
            class="btn btn-danger"
            type="button"
            @click="changeStatus('delete')"
        >
            X Interessiert
        </button>
        
        <button 
            v-if="status != 'attending'"
            class="btn btn-primary ml-1" 
            type="button" 
            @click="changeStatus('attending')"
        >
            Zusagen
        </button>
        <button
            v-else
            class="btn btn-danger ml-1"
            type="button"
            @click="changeStatus('delete')"
        >
            X Zusage
        </button>
    </div>
</template>
<script setup>
import { inject } from "vue";
import { useUserStore } from '../../stores/UserStore.js';
import { useEventStore } from '../../stores/EventStore.js';
import { toast } from "../helpers/toast.js";

const props = defineProps({
    event: {
        required: true,
        type: Number,
        default: null,
    },

    status: {
        required: false,
        type: String,
        default: null,
    }
});

const userStore = useUserStore();
const eventStore = useEventStore();
const axios = inject('axios');

const emit = defineEmits([
    'status-updated'
])

const changeStatus = (status) => {
    axios.patch(
        `/users/${userStore.getUserId}/watchlists/${props.event}/${status}`
    ).then((response) => {
        if (status == "delete") eventStore.removeFromWatchlist(props.event);
        else eventStore.updateWatchlist(response.data);

        emit('status-updated', status);
    }).catch((error) => {
        toast(error.message, 'error');
    })
}
</script>