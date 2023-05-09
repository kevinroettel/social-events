import { createRouter, createWebHistory } from "vue-router";

import Dashboard from './components/pages/Dashboard.vue';
import Event from './components/pages/Event.vue';
import EventForm from './components/pages/EventForm.vue';
import Artist from './components/pages/Artist.vue';
import SearchResults from './components/pages/SearchResults.vue';
import Location from './components/pages/Location.vue';
import Friends from './components/pages/Friends.vue';
import Account from './components/pages/Account.vue';
import ExtendedWatchlist from './components/pages/ExtendedWatchlist.vue';
import ExtendedAllEvents from './components/pages/ExtendedAllEvents.vue';

export const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/login', redirect: '/' },
        { path: '/js/bootstrap.esm.js.map', redirect: '/' },

        { path: '/', component: Dashboard },
        { path: '/neues-event', component: EventForm },
        { path: '/event-aktualisieren/:eventId', component: EventForm },
        { path: '/event/:eventId', component: Event },
        { path: '/freunde', component: Friends },
        { path: '/suche/:query', component: SearchResults },
        { path: '/account', component: Account },
        { path: '/kuenstler/:artistId', component: Artist },
        { path: '/venue/:locationId', component: Location },
        { path: '/watchlist', component: ExtendedWatchlist },
        { path: '/allevents', component: ExtendedAllEvents },
    ]
});