// https://nominatim.org/release-docs/develop/api/Search/
const requestUrl = "https://nominatim.openstreetmap.org/search";
const responseFormat = "format=json";

function sendRequest(url) {
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open("GET", url, false);
    xmlHttp.send(null);

    let jsonResponse = JSON.parse(xmlHttp.responseText);
    
    
    return {
        "lat": jsonResponse[0].lat,
        "lon": jsonResponse[0].lon
    }
}

export function getCoordinatesOfPlace(name, city) {
    let url = `${requestUrl}?q=${name},${city}&${responseFormat}`;
    return sendRequest(url);
}

export function getCoordinatesOfAddress(streetAndNumber, city) {
    let url = `${requestUrl}?street=${streetAndNumber}&city=${city}&${responseFormat}`
    return sendRequest(url);
}

// http://movable-type.co.uk/scripts/latlong.html
export function getDistanceBetweenTwoPoints(origin, destination) {
    const R = 6371e3; // earths radius in metres
    const φ1 = origin.lat * Math.PI / 180; // φ, λ in radians
    const φ2 = destination.lat * Math.PI / 180;
    const Δφ = (destination.lat - origin.lat) * Math.PI / 180;
    const Δλ = (destination.lon - origin.lon) * Math.PI / 180;
    
    const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
            Math.cos(φ1) * Math.cos(φ2) *
            Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
    
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    
    const distance = R * c / 1000; // in kilometers
    return distance;
}

