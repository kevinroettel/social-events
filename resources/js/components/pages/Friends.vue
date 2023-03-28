<template>
    <div>
        <div class="row">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">
                        <h4>Freunde:</h4>
                    </div>

                    <div class="card-body">
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
                </div>
            </div>

            <div class="col-md">
                <div class="card">
                    <div class="card-header">
                        <h4>Ausstehende Anfragen:</h4>
                    </div>

                    <div class="card-body">
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
                </div>
            </div>

            <div class="col-md">
                <div class="card">
                    <div class="card-header">
                        <h4>Nach neuen Freunden suchen:</h4>
                    </div>

                    <div class="card-body">
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
        </div>
    </div>
</template>
<script setup>
import UserBox from '../layouts/UserBox.vue';
import { inject, onMounted, ref } from 'vue';
import { useUserStore } from '../../stores/UserStore.js';
import { toast } from '../helpers/toast';
const userStore = useUserStore();

const axios = inject('axios');

const searchUser = ref("");
const possibleUsers = ref(null);

const getFriendRequests = () => {
    axios.get(
        `/users/${userStore.getUserId}/friendrequests`
    ).then((response) => {
        userStore.initializeRequests(response.data);
    }).catch((error) => {
        toast(error.message, 'error');
    })
}

const filterUsers = () => {
    setTimeout(() => {
        possibleUsers.value = userStore.getUsersALike(searchUser.value);
    }, 500);
}
 
onMounted(() => {
    getFriendRequests();
});
</script>