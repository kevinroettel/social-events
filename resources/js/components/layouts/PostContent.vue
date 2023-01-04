<template>
    <div 
        v-if="user != null"
        class="display-inline-flex mb-2"
    >
        <UserBox
            :user="user"
            :withButtons="false"
        />
        
        <div class="main-post-content">
            {{ post.content }}
            <br>
            <p class="time-foot-note">{{ getTimeSpan(post.created_at) }}</p>
        </div>
    </div>
</template>
<script setup>
import UserBox from './UserBox.vue';
import { useUserStore } from '../../stores/UserStore.js'
import { onMounted, ref } from '@vue/runtime-core';
const userStore = useUserStore();

const props = defineProps({
    post: {
        required: true,
        type: Object,
        default: null
    }
});

const user = ref(null);

const getUser = () => {
    user.value = userStore.getUserById(props.post.user_id);
}

const getTimeSpan = (timestamp) => {
    let date = new Date(timestamp);
    let now = new Date();
    let diff = now.getDate() - date.getDate();
    
    if (diff > 1) {
        return "Vor: " + diff + " Tagen gepostet"
    } else {
        let diff = now.getHours() - date.getHours();
        if (diff < 0) diff += 24;
        if (diff == 0) return "Grade eben gepostet";
        return "Vor: " + diff + " Stunden gepostet"
    }
}

onMounted(() => {
    getUser();
})
</script>