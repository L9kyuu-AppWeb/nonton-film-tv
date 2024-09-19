document.querySelectorAll('#videoList a').forEach(link => {
    link.addEventListener('click', function(event) {
        event.preventDefault();
        const videoPlayer = document.getElementById('videoPlayer');
        const videoSrc = this.getAttribute('data-video');
        videoPlayer.src = videoSrc;
        videoPlayer.play();
    });
});
