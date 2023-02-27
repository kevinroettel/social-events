<template v-cloak>
    <div>
        <Watchlist />

        <CosineSimilatity />

        <JaccardIndex />

        <FriendWatchlists />

        <AllEvents />
    </div>
</template>
<script setup>
import Watchlist from '../recommender/Watchlist.vue';
import CosineSimilatity from '../recommender/CosineSimilatity.vue';
import JaccardIndex from '../recommender/JaccardIndex.vue'; 
import FriendWatchlists from '../recommender/FriendWatchlists.vue'
import AllEvents from '../recommender/AllEvents.vue';
</script>