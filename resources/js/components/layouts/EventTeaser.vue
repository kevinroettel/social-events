<template>
    <div v-if="event == null" class="card event-teaser-card" style="width: 18rem;">
        <img class="card-img-top" src="https://picsum.photos/200/200" alt="Card image cap">
        <div class="card-body">
            <h5 class="card-text-headline">Some short Event Title</h5>
            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary" type="submit">Interessiert</button>
            <button class="btn btn-primary" type="submit">Zusagen</button>
        </div>
    </div>

    <div v-else class="card event-teaser-card" style="width: 18rem;">
        <img class="card-img-top" src="https://picsum.photos/200/200" alt="Card image cap">
        <div class="card-body">
            <h5 class="card-text-headline">{{ event.title }}</h5>
            <p class="card-text">{{ event.description }}</p>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary" type="submit">Interessiert</button>
            <button class="btn btn-primary" type="submit">Zusagen</button>
        </div>
    </div>
</template>
<script setup>

const props = defineProps({
    event: {
        required: true,
        type: Object,
        default: null
    }
});
</script>