<template v-cloak>
    <div>
        <Watchlist />

        <ContentBased />

        <!-- <JaccardIndex /> -->

        <FriendWatchlists />

        <AllEvents />
    </div>
</template>
<script setup>
import Watchlist from '../recommender/Watchlist.vue';
import ContentBased from '../recommender/ContentBased.vue';
// import JaccardIndex from '../recommender/JaccardIndex.vue'; 
import FriendWatchlists from '../recommender/FriendWatchlists.vue'
import AllEvents from '../recommender/AllEvents.vue';
</script>