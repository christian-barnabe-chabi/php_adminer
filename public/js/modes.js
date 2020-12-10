var night_stylesheet = $("<link rel='stylesheet' href='/assets/css/night.css'>")
    
setMode();
if (typeof(Storage) !== "undefined") {
    if(localStorage.getItem('mode') == undefined) {
        localStorage.setItem('mode', 'light');
    }
}

function setMode() {
    if (typeof(Storage) !== "undefined") {
        if(localStorage.getItem('mode') == 'night') {
            $('head').append(night_stylesheet);
        } else {
            night_stylesheet.remove();
        }
    }
}
