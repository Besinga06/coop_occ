<video id="cam" autoplay playsinline style="width:400px;"></video>

<script>
navigator.mediaDevices.getUserMedia({ video: true })
.then(stream => {
    document.getElementById("cam").srcObject = stream;
})
.catch(err => {
    alert("Camera failed: " + err);
});
</script>
