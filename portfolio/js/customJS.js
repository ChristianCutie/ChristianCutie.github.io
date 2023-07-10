function displayModal(imageElement) {
    var modal = document.getElementById("myModal");
    var modalImage = document.getElementById("modalImage");

    modal.style.display = "block";
    modalImage.src = imageElement.src;

    var span = document.getElementsByClassName("close")[0];

    span.onclick = function () {
        modal.style.display = "none";
    };
    var closeSpan = document.getElementById('close-span');

    function closeOnEscape(event) {
        if (event.keyCode === 27) {
            modal.style.display = "none";
        }
    }
    document.addEventListener('keydown', closeOnEscape);
}

var typed = new Typed(".auto-input", {

    strings: ["-to create a Web application with responsive design and multiple function using C# language."],
    typeSpeed: 50,
    loop: false

})
