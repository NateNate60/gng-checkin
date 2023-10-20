function checkfields () {
    document.getElementById("date_input").disabled = !document.getElementById("by_date").checked
    document.getElementById("name").disabled = !document.getElementById("by_name").checked
    document.getElementById("events_participated_player").disabled = !document.getElementById("events_participated").checked
    document.getElementById("event_type_selection").disabled = !document.getElementById("attendance").checked
    document.getElementById("event_date_input").disabled = !document.getElementById("attendance").checked
}


function defaultchecked () {
    let params = new URLSearchParams(location.search).get("view");
    if (params !== null) {
        document.getElementById(params).checked = true
    }
    checkfields()
}

function download_csv () {
    let params = new URLSearchParams(window.location.search);
    let redirect_url = "/gng/download.php?"
    if (params.get("view")) {
        redirect_url += "view=" + params.get("view") + "&"
    }
    if (params.get("date")) {
        redirect_url += "date=" + params.get("date") + "&"
    }
    if (params.get("name")) {
        redirect_url += "name=" + params.get("name") + "&"
    }
    if (params.get("player")) {
        redirect_url += "player=" + params.get("player") + "&"
    }
    if (params.get("event_type")) {
        redirect_url += "event_type=" + params.get("event_type") + "&"
    }
    redirect_url += "dummy=0"
    console.log(redirect_url)
    window.open(redirect_url, '_blank')
}