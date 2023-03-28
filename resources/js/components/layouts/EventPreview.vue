<template>
    <div class="card">
        <div class="flyer-overflow">
            <img 
                v-if="flyer != null"
                :src="flyer"
                class="card-img-top"
            >

            <img 
                v-else
                :src="'/storage/fallback-flyer.jpg'"
                class="card-img-top"
            >
        </div>
        <div class="card-body">
            <h3 class="text-center">{{ event.name }}</h3>
            
            <div class="row">
                <div class="col-md">
                    <span v-if="event.date">{{ getFormattedDate(event.date) }}<br></span>
                    <span v-if="event.doors">Einlass: {{ event.doors }}<br></span>
                    <span v-if="event.begin">Beginn: {{ event.begin }}<br></span>
                    <span v-if="event.location != null">{{ event.location.name }} in {{ event.location.city }}<br></span>
                    <span v-if="event.ticketLink != null"><a target="_blank">Tickets</a><br></span>
                    <hr>
                </div>
                <div class="col-md">
                    <button class="btn btn-primary" type="button">Interessiert</button>
                    <button class="btn btn-primary ml-1" type="button">Zusagen</button>

                    <br><span>0 Personen sind interessiert.</span><br>
                    <span>0 Personen nehmen teil.</span><br><hr>
                </div>
            </div>

            <div class="row">
                <div class="col-md pre-wrap">{{ event.description }}</div>
                <div class="col-md">
                    <p v-if="event.artists.length != 0">Line-up:</p>
                    <ul>
                        <li 
                            v-for="(artist, index) in event.artists"
                            :key="index"
                        >
                            {{ artist.name ?? artist }}
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
    },

    flyer: {
        required: false,
        type: String,
        default: null,
    }
})
</script>