<template>
    <div>
        <div class="row">
            <div  class="col">
                <h4>Freunde:</h4>

                <p v-if="userStore.getFriends == null || userStore.getFriends.length == 0">
                    Noch hast du hier keine Freunde. Füge doch welche hinzu!
                </p>

                <UserBox 
                    v-for="(friend, index) in userStore.getFriends"
                    :key="index"
                    :user="friend" 
                    :friendStatus="'friend'"
                />
            </div>

            <div class="col">
                <h4>Ausstehende Anfragen:</h4>

                <p v-if="userStore.getFriendRequests == null || userStore.getFriendRequests.length == 0">
                    Es gibt momentan keine ausstehenden Freundschaftsanfragen.
                </p>

                <UserBox
                    v-for="(user, index) in userStore.getFriendRequests"
                    :key="index"
                    :user="user"
                    :friendStatus="'pending'"
                />
            </div>

            <div class="col">
                <h4>Nach neuen Freunden suchen:</h4>

                <input 
                    type="text" 
                    class="form-control w-50" 
                    v-model="searchUser" 
                    @input="filterUsers()"
                >

                <div v-if="searchUser != ''">
                    <UserBox
                        v-for="(user, index) in possibleUsers"
                        :key="index"
                        :user="user"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import UserBox from '../layouts/UserBox.vue';
import { inject, onMounted, ref } from '@vue/runtime-core';
import { useUserStore } from '../../stores/UserStore.js';
const userStore = useUserStore();

const axios = inject('axios');

const searchUser = ref("");
const possibleUsers = ref(null);

const getUsers = () => {
    axios.get(
        '/users'
    ).then((response) => {
        userStore.initializeUsers(response.data);
    }).catch((error) => {
        console.log(error)
    })
}

const getFriendRequests = () => {
    axios.get(
        `/users/${userStore.getUserId}/friendrequests`
    ).then((response) => {
        userStore.initializeRequests(response.data);
    }).catch((error) => {
        console.log(error)
    })
}

const filterUsers = () => {
    setTimeout(() => {
        possibleUsers.value = userStore.getUsersALike(searchUser.value);
    }, 500);
}
 
onMounted(() => {
    getUsers();
    getFriendRequests();
});
</script>