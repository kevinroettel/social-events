<template>
    <div v-lazyload class="small-flyer-overflow">
        <img 
            class="card-img-top" 
            :data-url="'/storage' + (source ?? '/fallback-flyer.jpg')" 
            alt="Event Flyer"
        >
    </div>
</template>
<script setup>

const props = defineProps({
    source: {
        required: false,
        type: String,
        default: null
    }
});

const vLazyload = {
    mounted(el) {
        function loadImage() {
            const imageElement = Array.from(el.children).find(el => el.nodeName === "IMG");
            if (imageElement) {
                imageElement.addEventListener("load", () => {
                    setTimeout(() => el.classList.add("loaded"), 100);
                });
                imageElement.addEventListener("error", () => console.log("error"));
                imageElement.src = imageElement.dataset.url;
            }
        }

        function handleIntersect(entries, observer) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    loadImage();
                    observer.unobserve(el);
                }
            });
        }

        function createObserver() {
            const options = {
                root: null,
                threshold: "0"
            };
            const observer = new IntersectionObserver(handleIntersect, options);
            observer.observe(el)
        }

        if (window["IntersectionObserver"]) createObserver();
        else loadImage();
    }
}

</script>
