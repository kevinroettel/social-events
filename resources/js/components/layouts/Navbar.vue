<template>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand">social-events</span>
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <router-link to="/" class="nav-link">Home</router-link>
                    <router-link to="/neues-event" class="nav-link">Neues Event</router-link>
                    <router-link to="/freunde" class="nav-link">Freunde</router-link>

                    <div class="display-inline-flex">
                        <input 
                            class="form-control" 
                            type="search" 
                            placeholder="Search" 
                            aria-label="Search"
                            v-model="searchQuery"
                            @keyup.enter="startSearch()"
                        >

                        <button
                            type="button"
                            class="btn btn-secondary ml-1"
                            @click="startSearch()"
                        >
                            <FontAwesomeIcon icon="fa-solid fa-magnifying-glass" />
                        </button>
                    </div>

                    <div class="logout-link">
                        <a 
                            class="nav-link"
                            @click="submitLogoutForm($event)"
                        >
                            Logout
                        </a>

                        <form 
                            id="logout-form" 
                            action="/logout"
                            method="POST"
                            style="display:none;"
                        >
                            <input type="hidden" name="_token" :value="csrf">
                        </form>
                    </div>

                    <router-link to="/account" class="nav-link account-link">
                        <FontAwesomeIcon class="navbar-profile-picture" v-if="userStore.getUserPicture == null" icon="fa-solid fa-circle-user" />
                        <img class="navbar-profile-picture" v-else :src="'/storage' + userStore.getUserPicture">
                    </router-link>
                </div>
            </div>
        </div>
    </nav>
</template>
<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useUserStore } from '../../stores/UserStore.js';

const userStore = useUserStore();
const router = useRouter();

const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const searchQuery = ref(null);

const submitLogoutForm = (event) => {
    event.preventDefault();
    document.getElementById('logout-form').submit();
}

const startSearch = () => {
    if (searchQuery.value != null && searchQuery.value != "") {
        router.push(`/suche/${searchQuery.value}`);
    } 
}
</script>