<template v-cloak>
    <div>
        <Watchlist />

        <ContentBased v-if="recommenderNumber == 0" />
        
        <CollaborativeBased v-if="recommenderNumber == 1" />
        
        <HybridRecommender v-if="recommenderNumber == 2" />
        
        <!-- <SlopeOneRecommender /> -->

        <!-- <JaccardIndex /> -->

        <FriendWatchlists />

        <AllEvents />
    </div>
</template>
<script setup>
import Watchlist from '../recommender/Watchlist.vue';
// import SlopeOneRecommender from '../recommender/SlopeOneRecommender.vue';
import CollaborativeBased from '../recommender/CollaborativeBased.vue';
import ContentBased from '../recommender/ContentBased.vue';
import HybridRecommender from '../recommender/HybridRecommender.vue';
// import JaccardIndex from '../recommender/JaccardIndex.vue'; 
import FriendWatchlists from '../recommender/FriendWatchlists.vue'
import AllEvents from '../recommender/AllEvents.vue';
import { onMounted, ref } from 'vue';
import { useUserStore } from '../../stores/UserStore';

const userStore = useUserStore();

const recommenderNumber = ref(null);

onMounted(() => {
    recommenderNumber.value = userStore.getUserId % 3;
})
</script>