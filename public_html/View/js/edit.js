window.onload = function () {

    var pass   = document.getElementById("password");
    var repeat = document.getElementById("repeat");

    document.forms[0].onsubmit = function(e) {
        var correctPassword = (pass.value == repeat.value);

        if (!correctPassword) {
            e.preventDefault();
            alert("Passwords do not match");
        }
    }
}