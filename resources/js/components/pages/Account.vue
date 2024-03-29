<template>
    <div>
        <div class="row">
            <div class="col">
                <h3>Account</h3>

                <div 
                    v-if="userData != null"
                    class="account-page"
                >
                    <img 
                        v-if="profilePictureUrl != null"
                        :src="profilePictureUrl"
                        class="profile-picture"
                    >

                    <img
                        v-else-if="userData.profile_picture != null"
                        :src="'/storage' + userData.profile_picture"
                        class="profile-picture"
                    >

                    <FontAwesomeIcon
                        v-if="profilePictureUrl == null && userData.profile_picture == null"
                        class="profile-picture"
                        icon="fa-solid fa-circle-user"
                    />

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="name-input-label">Name</span>
                        <input type="text" :class="`form-control ${errorClass('name')}`" aria-label="Name" aria-describedby="name-input-label" v-model="userData.username" @change="changeDetected = true">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="email-input-label">E-Mail</span>
                        <input type="text" :class="`form-control ${errorClass('email')}`" aria-label="E-Mail" aria-describedby="email-input-label" v-model="userData.email" @change="changeDetected = true">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="picture-input-label">Profilbild</span>
                        <input type="file" class="form-control" aria-label="Profilbild" aria-describedby="picture-input-label" @change="handleFile($event)">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="address-input-label">Adresse</span>
                        <input type="text" class="form-control" aria-label="Adresse" aria-describedby="address-input-label" v-model="userData.address" @change="changeDetected = true">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="city-input-label">Stadt/Ort</span>
                        <input type="text" class="form-control" aria-label="Stadt/Ort" aria-describedby="city-input-label" v-model="userData.city" @change="changeDetected = true">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="password-input-label">Neues Passwort</span>
                        <input type="password" :class="`form-control ${errorClass('password')}`" aria-label="Passwort" aria-describedby="password-input-label" v-model="userData.password" @change="changeDetected = true">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="password-confirm-input-label">Passwort Bestätigung</span>
                        <input type="password" :class="`form-control ${errorClass('password_confirmation')}`" aria-label="Passwort Bestätigung" aria-describedby="password-confirm-input-label" v-model="userData.password_confirmation" @change="changeDetected = true">
                    </div>

                    <div id="password-errors"></div>
                </div>

                <button
                    v-if="changeDetected"
                    type="button"
                    class="btn btn-primary"
                    @click="checkInputs()"
                >
                    Daten speichern
                </button>
            </div>
        </div>
    </div>
</template>
<script setup>
import { inject, onMounted, reactive, ref } from 'vue';
import { useUserStore } from '../../stores/UserStore.js';
import { toast } from '../helpers/toast.js';
const userStore = useUserStore();
const axios = inject('axios');

const changeDetected = ref(false);

const profilePictureUrl = ref(null);

const userData = reactive({
    id: null,
    username: null,
    email: null,
    profile_picture: null,
    address: null,
    city: null,
    password: null,
    password_confirmation: null
});

const hasError = reactive({
    username: false,
    email: false,
    password: false,
    password_confirmation: false
});

const errorClass = (input) => {
    return hasError[input] ? 'is-invalid' : '';
}

const checkInputs = () => {
    hasError.username = userData.username == null || userData.username == "";
    hasError.email = userData.email == null || userData.username == "";

    if (userData.password != null && userData.password != "") {
        let el = document.getElementById('password-errors');
        if (userData.password_confirmation != userData.password) {
            el.innerHTML = "Die Passwörter stimmen nicht überein.";
            hasError.password_confirmation = true;
        } else hasError.password_confirmation = false;
        
        if (userData.password.length < 6) {
            el.innerHTML = "Das Passwort benötigt mindestens 6 Zeichen.";
            hasError.password = true;
        } else hasError.password = false;
    }

    if (!hasError.username && 
        !hasError.email && 
        !hasError.password && 
        !hasError.password_confirmation) 
        saveUser();
}

const saveUser = () => {
    let formData = new FormData();
    if (userData.profile_picture != null) formData.append('profile_picture', userData.profile_picture);
    formData.append('username', userData.username);
    formData.append('email', userData.email);
    if (userData.address != null) formData.append('address', userData.address);
    if (userData.city != null) formData.append('city', userData.city);
    formData.append('password', userData.password);
    formData.append('password_confirmation', userData.password_confirmation);

    axios.post(
        `/users/${userData.id}`,
        formData, {
            'Content-Type': 'multipart/form-data; charset=utf-8;'
        }
    ).then((response) => {
        toast('Nutzerdaten erfolgreich aktualisiert.', 'success');
    }).catch((error) => {
        toast(error.message, 'error');
    })
}

const handleFile = (input) => {
    changeDetected.value = true;

    userData.profile_picture = input.target.files[0]
    profilePictureUrl.value = URL.createObjectURL(userData.profile_picture)
}

onMounted(() => {
    userData.id = userStore.getUserId;
    userData.username = userStore.getUserName;
    userData.email = userStore.getUserEmail;
    userData.address = userStore.getUserAddress;
    userData.city = userStore.getUserCity;
    userData.profile_picture = userStore.getUserPicture;
})
</script>