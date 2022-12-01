export function getFormattedDate(date) {
    var options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    
    var format = new Date(date);
    return format.toLocaleDateString("de-DE", options);
}