// https://nominatim.org/release-docs/develop/api/Search/
const requestUrl = "https://nominatim.openstreetmap.org/search";
const responseFormat = "format=json";

// http://project-osrm.org/docs/v5.24.0/api/?language=cURL#general-options
// http://router.project-osrm.org/route/v1/driving/7.373286149018651,51.0195563;7.324050764224658,51.47162635?alternatives=false&steps=false&geometries=polyline&overview=false&annotations=false

export async function calculateRouteDistance(userLatitude, userLongitude, locationLatitude, locationLongitude) {
    let base = "http://router.project-osrm.org/route/v1/driving/";
    let coordinates = `${userLongitude},${userLatitude};${locationLongitude},${locationLatitude}`;
    let options = "?alternatives=false&steps=false&geometries=polyline&overview=false&annotations=false";
    let url = `${base}${coordinates}${options}`;
    let routeResponse = await fetch(url);
    let routeJson = await routeResponse.json();
    let distance = Math.round(routeJson.routes[0].distance / 1000)

    return distance;
}

export async function getLongAndLat(name, city, street) {
    let url = null;
    if (street == null) url = `${requestUrl}?q=${name},${city}&${responseFormat}`;
    else url = `${requestUrl}?street=${street}&city=${city}&${responseFormat}`;

    let response = await fetch(url);
    let json = await response.json();
    if (json.length == 0) return null;
    
    return {
        "latitude": json[0].lat,
        "longitude": json[0].lon
    }
}

// Luftlinie
// http://movable-type.co.uk/scripts/latlong.html
export function getDistanceBetweenTwoPoints(originLat, originLon, destinationLat, destinationLon) {
    const R = 6371e3; // earths radius in metres
    const φ1 = originLat * Math.PI / 180; // φ, λ in radians
    const φ2 = destinationLat * Math.PI / 180;
    const Δφ = (destinationLat - originLat) * Math.PI / 180;
    const Δλ = (destinationLon - originLon) * Math.PI / 180;
    
    const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
            Math.cos(φ1) * Math.cos(φ2) *
            Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
    
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    
    const distance = R * c / 1000; // in kilometers
    return distance;
}
