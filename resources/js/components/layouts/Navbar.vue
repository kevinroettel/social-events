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
                    <!-- <a :class="`nav-link ${props.currentPage == 'home' ? 'active' : ''}`" aria-current="page" @click="changePage('home')">Home</a>
                    <a :class="`nav-link ${props.currentPage == 'eventform' ? 'active' : ''}`" @click="changePage('eventform')">Neues Event</a>
                    <a :class="`nav-link ${props.currentPage == 'friends' ? 'active' : ''}`" @click="changePage('friends')">Freunde</a> -->
                    <router-link to="/" :class="`nav-link ${props.currentPage == 'home' ? 'active' : ''}`">Home</router-link>
                    <router-link to="/neues-event" :class="`nav-link ${props.currentPage == 'eventform' ? 'active' : ''}`">Neues Event</router-link>
                    <router-link to="/freunde" :class="`nav-link ${props.currentPage == 'friends' ? 'active' : ''}`">Freunde</router-link>

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
                            Suchen
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

                    <a :class="`nav-link account-link ${props.currentPage == 'account' ? 'active' : ''}`" @click="changePage('account')">
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
import { useUserStore } from '../../stores/UserStore.js';

const userStore = useUserStore();

const props = defineProps({
    currentPage: {
        required: true,
        type: String,
        default: null
    },
});

const emit = defineEmits([
    'page-change',
    'search-query'
]);

const currentPage = ref("home");
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const searchQuery = ref(null);

const changePage = (newPage) => {
    emit('page-change', newPage);
}

const submitLogoutForm = (event) => {
    event.preventDefault();
    document.getElementById('logout-form').submit();
}

const startSearch = () => {
    if (searchQuery.value != null && searchQuery.value != "") emit('search-query', searchQuery.value);
}

</script>