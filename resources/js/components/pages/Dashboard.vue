<template v-cloak>
    <div>
        <Watchlist />

        <CollaborativeBased />

        <!-- <ContentBased /> -->

        <!-- <JaccardIndex /> -->

        <FriendWatchlists />

        <AllEvents />
    </div>
</template>
<script setup>
import Watchlist from '../recommender/Watchlist.vue';
import CollaborativeBased from '../recommender/CollaborativeBased.vue';
import ContentBased from '../recommender/ContentBased.vue';
// import JaccardIndex from '../recommender/JaccardIndex.vue'; 
import FriendWatchlists from '../recommender/FriendWatchlists.vue'
import AllEvents from '../recommender/AllEvents.vue';
</script>