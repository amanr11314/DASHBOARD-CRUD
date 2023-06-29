<form enctype="multipart/form-data" method="post" action="upload.php">
    <div class="row">
        <label for="fileToUpload">Select Files to Upload</label><br />
        <input type="file" name="filesToUpload[]" id="filesToUpload" multiple="multiple" />
        <output id="filesInfo"></output>
    </div>
</form>

<script>
if (window.File && window.FileReader && window.FileList && window.Blob) {
    document.getElementById('filesToUpload').onchange = function() {
        var files = document.getElementById('filesToUpload').files;
        for (var i = 0; i < files.length; i++) {
            resizeAndUpload(files[i]);
        }
    };
} else {
    alert('The File APIs are not fully supported in this browser.');
}

function resizeAndUpload(file) {
    var reader = new FileReader();
    reader.onloadend = function() {

        var tempImg = new Image();
        tempImg.src = reader.result;
        tempImg.onload = function() {

            var MAX_WIDTH = 1920;
            var MAX_HEIGHT = 1200;
            var tempW = tempImg.width;
            var tempH = tempImg.height;
            if (tempW > tempH) {
                if (tempW > MAX_WIDTH) {
                    tempH *= MAX_WIDTH / tempW;
                    tempW = MAX_WIDTH;
                }
            } else {
                if (tempH > MAX_HEIGHT) {
                    tempW *= MAX_HEIGHT / tempH;
                    tempH = MAX_HEIGHT;
                }
            }

            var canvas = document.createElement('canvas');
            canvas.width = tempW;
            canvas.height = tempH;
            var ctx = canvas.getContext("2d");
            ctx.drawImage(this, 0, 0, tempW, tempH);
            var dataURL = canvas.toDataURL("image/jpeg");

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function(ev) {
                document.getElementById('filesInfo').innerHTML = 'Done!';
            };

            xhr.open('POST', 'uploadResized.php', true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            var data = 'image=' + dataURL;
            xhr.send(data);
        }

    }
    reader.readAsDataURL(file);
}
</script>