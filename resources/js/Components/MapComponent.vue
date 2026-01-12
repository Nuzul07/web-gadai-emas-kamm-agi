<script setup>
import { ref, onMounted, watch, onBeforeUnmount, nextTick } from 'vue';
import 'ol/ol.css';
import Map from 'ol/Map';
import View from 'ol/View';
import TileLayer from 'ol/layer/Tile';
import OSM from 'ol/source/OSM';
import { fromLonLat } from 'ol/proj';
import VectorLayer from 'ol/layer/Vector';
import VectorSource from 'ol/source/Vector';
import Feature from 'ol/Feature';
import Point from 'ol/geom/Point';
import Style from 'ol/style/Style';
import Icon from 'ol/style/Icon';

const props = defineProps({
    location: {
        type: String,
        required: true
    },
    showMap: {
        type: Boolean,
        required: true
    }
});

const mapContainer = ref(null);
const map = ref(null);
const [latitude, longitude] = props.location.split(',').map(Number);

onMounted(() => {
    if (props.showMap) {
        initializeMap();
    }
});

const initializeMap = () => {
    nextTick(() => {
        const coordinates = fromLonLat([longitude, latitude]);

        // Create a marker (Vector Layer)
        const marker = new Feature({
            geometry: new Point(coordinates),
        });

        // Style for the marker
        marker.setStyle(new Style({
            image: new Icon({
                src: 'https://openlayers.org/en/latest/examples/data/icon.png', // URL to marker icon
                scale: 0.05, // Adjust scale of the icon
                anchor: [0.5, 1], // Anchor the icon at the bottom-center
            })
        }));

        const vectorLayer = new VectorLayer({
            source: new VectorSource({
                features: [marker]
            })
        });

        // Create map with the marker and OSM layer
        map.value = new Map({
            target: mapContainer.value,
            layers: [
                new TileLayer({
                    source: new OSM()
                }),
                vectorLayer // Add the marker layer to the map
            ],
            view: new View({
                center: coordinates,
                zoom: 18
            })
        });
        map.value.updateSize(); // Ensure the map size is updated after rendering
    });
};

watch(() => props.showMap, (newValue) => {
    if (newValue) {
        nextTick(() => {
            if (map.value) {
                map.value.updateSize(); // Update map size when modal is shown
            } else {
                initializeMap(); // Initialize the map if not yet initialized
            }
        });
    }
});

onBeforeUnmount(() => {
    if (map.value) {
        map.value.setTarget(null); // Clean up the map when the component is unmounted
    }
});
</script>

<template>
    <div>
        <!-- Display map -->
        <div v-if="showMap" ref="mapContainer" style="width: 100%; height: 400px;"></div>
    </div>
</template>
