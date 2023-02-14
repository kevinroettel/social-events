<template v-cloak>
    <div>
        <Watchlist />

        <SimilarEvents />

        <FriendWatchlists />

        <AllEvents />
    </div>
</template>
<script setup>
import Watchlist from '../recommender/Watchlist.vue';
import SimilarEvents from '../recommender/SimilarEvents.vue';
import FriendWatchlists from '../recommender/FriendWatchlists.vue'
import AllEvents from '../recommender/AllEvents.vue';
</script>