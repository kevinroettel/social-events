<template v-cloak>
    <div>
        <Watchlist />

        <HybridRecommender />
        
        <!-- <CollaborativeBased /> -->

        <!-- <SlopeOneRecommender /> -->

        <!-- <ContentBased /> -->

        <!-- <JaccardIndex /> -->

        <FriendWatchlists />

        <AllEvents />
    </div>
</template>
<script setup>
import Watchlist from '../recommender/Watchlist.vue';
import SlopeOneRecommender from '../recommender/SlopeOneRecommender.vue';
import CollaborativeBased from '../recommender/CollaborativeBased.vue';
import ContentBased from '../recommender/ContentBased.vue';
import HybridRecommender from '../recommender/HybridRecommender.vue';
// import JaccardIndex from '../recommender/JaccardIndex.vue'; 
import FriendWatchlists from '../recommender/FriendWatchlists.vue'
import AllEvents from '../recommender/AllEvents.vue';
</script>