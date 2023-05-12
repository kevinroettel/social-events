<template>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand">Events Base</span>
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
                    <a 
                        class="nav-link" 
                        @click="routerLink('/')"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarNavAltMarkup"
                    >
                        Home
                    </a>
                    
                    <a 
                        class="nav-link" 
                        @click="routerLink('/neues-event')"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarNavAltMarkup"
                    >
                        Neues Event
                    </a>
                    
                    <a 
                        class="nav-link" 
                        @click="routerLink('/freunde')"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarNavAltMarkup"
                    >
                        Freunde
                    </a>

                    <a 
                        class="nav-link" 
                        @click="routerLink('/impressum')"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarNavAltMarkup"
                    >
                        Impressum
                    </a>

                    <div class="display-inline-flex">
                        <input 
                            class="form-control" 
                            type="search" 
                            placeholder="Suche" 
                            aria-label="Suche"
                            v-model="searchQuery"
                            @keyup.enter="startSearch()"
                        >

                        <button
                            type="button"
                            class="btn btn-secondary ml-1"
                            data-bs-toggle="collapse"
                            data-bs-target="#navbarNavAltMarkup"
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

                    <a 
                        class="nav-link account-link"
                        @click="routerLink('/account')"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarNavAltMarkup"
                    >
                        <FontAwesomeIcon class="navbar-profile-picture" v-if="userStore.getUserPicture == null" icon="fa-solid fa-circle-user" />
                        <img class="navbar-profile-picture" v-else :src="'/storage' + userStore.getUserPicture">
                    </a>
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
const visible = ref(false);

const submitLogoutForm = (event) => {
    event.preventDefault();
    document.getElementById('logout-form').submit();
}

const startSearch = () => {
    if (searchQuery.value != null && searchQuery.value != "") {
        router.push(`/suche/${searchQuery.value}`);
    } 
}

const routerLink = (url) => {
    router.push(url);
}

</script>