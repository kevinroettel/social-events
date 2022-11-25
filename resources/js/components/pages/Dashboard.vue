<template>
    <div>
        <div class="card mb-3">
            <div class="card-header">
                Watchlist
            </div>
            <div class="card-body">
                <div v-if="props.watchlist.length == 0">
                    <p>Zurzeit befinden sich keine Einträge in deiner Watchlist. Füge welche hinzu!</p>
                </div>

                <EventTeaser 
                    v-for="entry in props.watchlist"
                    :key="entry.id"
                    :event="entry"
                />
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Vorschläge (erstmal Platzhalter, soll aber schonmal im Dashboard zu sehen sein)
            </div>
            <div class="card-body">
                <EventTeaser 
                    :event="{
                        title: 'Band 1 in Location 1',
                        description: 'Band 1 sind wieder in Europa. Lasst euch dieses Spektakel nicht entgehen!'
                    }"
                />

                <EventTeaser 
                    :event="{
                        title: 'Band 2 in Location 2',
                        description: 'Band 2 sind wieder in Europa. Lasst euch dieses Spektakel nicht entgehen!'
                    }"
                />
            </div>
        </div>
    </div>
</template>
<script setup>
import EventTeaser from '../layouts/EventTeaser.vue'

const props = defineProps({
    watchlist: {
        required: true,
        type: Array,
        default: []
    },
});


</script>