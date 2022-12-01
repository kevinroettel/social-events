<template>
    <div class="card">
        <div class="flyer-overflow">
            <img :src="event.flyer" class="card-img-top">
        </div>
        <div class="card-body">
            <h3>{{ event.name }}</h3>
            
            <div class="row">
                <span v-if="event.date">{{ getFormattedDate(event.date) }}</span>
                <span v-if="event.doors">Einlass: {{ event.doors }}</span>
                <span v-if="event.begin">Beginn: {{ event.begin }}</span>
            </div>

            <p v-if="event.location != null">{{ event.location.name }} in {{ event.location.city }}</p>
            
            <p v-if="event.name != null">Name1 und zwei weitere Freunde sind an der Veranstaltung interessiert.</p>
            <div class="row">
                <div class="col pre-wrap">{{ event.description }}</div>
                <div class="col">
                    <p v-if="event.artists.length != 0">Line-up:</p>
                    <ul>
                        <li 
                            v-for="(artist, index) in event.artists"
                            :key="index"
                        >
                            {{ artist.name }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { getFormattedDate } from '../helpers/dateFormat.js';

const props = defineProps({
    event: {
        required: true,
        type: Object,
        default: null
    }
})
</script>