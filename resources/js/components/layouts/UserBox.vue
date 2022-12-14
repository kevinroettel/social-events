<template>
    <div v-if="user != null" class="user-box my-2">
        <FontAwesomeIcon 
            v-if="user.profile_picture == null" 
            icon="fa-solid fa-circle-user" 
            class="small-profile-picture"
        />

        <img 
            v-else 
            class="small-profile-picture" 
            :src="'/storage' + user.profile_picture"
        >

        <p class="d-inline mx-2">{{ user.username }}</p>

        <button 
            v-if="friendStatus == 'friend'"
            class="btn btn-danger" 
            type="button" 
            @click="changeFriendStatus('remove', removeFriendHandling)"
        >
            Freund entfernen
        </button>

        <div v-else-if="friendStatus == 'pending'">
            <button 
                class="btn btn-danger" 
                type="button" 
                @click="changeFriendStatus('accept', acceptRequestHandling)"
            >
                Annehmen
            </button>

            <button 
                class="btn btn-danger" 
                type="button" 
                @click="changeFriendStatus('deny', denyRequestHandling)"
            >
                Ignorieren
            </button>
        </div>

        <button v-else-if="friendStatus == 'unknown'"
            class="btn btn-primary" 
            type="button" 
            @click="changeFriendStatus('request', requestFriendHandling)"
        >
            Freundschaftsanfrage Senden
        </button>
    </div>
</template>
<script setup>
import { inject } from 'vue';
import { useUserStore } from '../../stores/UserStore.js';
import { toast } from '../helpers/toast.js';
const userStore = useUserStore();

const axios = inject('axios');

const props = defineProps({
    user: {
        required: true,
        type: Object,
        default: null
    },

    friendStatus: {
        required: false,
        type: String,
        default: 'unknown'
    }
});

const changeFriendStatus = (status, handlingFunction) => {
    axios.post(
        `/users/${userStore.getUserId}/friends/${props.user.id}/${status}`
    ).then((response) => 
        handlingFunction(response)
    ).catch((error) => {
        toast(error.message, 'error');
    })
}

const requestFriendHandling = (response) => {
    if (response.data.errors != null) {
        toast(response.data.errors, 'warning');
    } else {
        toast(response.data.success, 'success');
    }

    userStore.moveUserToRequested(props.user.id);
}

const removeFriendHandling = (response) => {
    toast(response.data.success, 'success');
    userStore.removeFriend(props.user.id);
}

const denyRequestHandling = (response) => {
    toast(response.data.success, 'success');
    userStore.removeFriendRequest(props.user.id);
}

const acceptRequestHandling = (response) => {
    if (response.data.errors != null) {
        toast(response.data.errors, 'warning');
    } else {
        toast(response.data.success, 'success');
    }

    userStore.acceptFriend(props.user.id);
}


</script>