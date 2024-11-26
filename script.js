function toggleActive(id, ids){
    var elemToActivate = document.getElementById(id).classList;    
    if (elemToActivate.contains("active")){
        elemToActivate.remove("active");
    }
    else {
        elemToActivate.add("active");
    }
    document.getElementById(id).classList = elemToActivate;

    ids.forEach(element => {
        if (element == id){
            return;
        }
        var elemToDeactivate = document.getElementById(element).classList;
        elemToDeactivate.remove("active");
        document.getElementById(element).classList = elemToDeactivate;
    });
}