function showTab(tabID) {
    var tabs = document.getElementsByClassName('bob-settings-tab');
    for (var i = 0; i < tabs.length; i++) {
        tabs[i].style.display = 'none';
    }
    document.getElementById(tabID).style.display = 'block';
}