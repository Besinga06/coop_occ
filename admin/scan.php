<!DOCTYPE html>
<html>
<head>
    <title>Scan Uploaded Barcode</title>

    <!-- Dynamsoft Barcode Reader -->
    <script src="https://cdn.jsdelivr.net/npm/dynamsoft-javascript-barcode@9.6.20/dist/dbr.js"></script>

    <style>
        body { font-family: Arial; padding: 20px; }
        #preview { margin-top: 10px; max-width: 300px; border: 1px solid #ccc; padding: 5px; }
        #result { margin-top: 15px; font-size: 20px; font-weight: bold; }
    </style>
</head>
<body>

<h2>Upload Barcode Image (Code39)</h2>

<input type="file" id="fileInput" accept="image/*">
<br><br>

<img id="preview">

<p id="result">Waiting for image...</p>

<script>
let reader;

(async () => {
    reader = await Dynamsoft.DBR.BarcodeReader.createInstance();
})();

document.getElementById("fileInput").addEventListener("change", async function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const img = document.getElementById("preview");
    img.src = URL.createObjectURL(file);
    document.getElementById("result").innerHTML = "Reading...";

    await new Promise(res => img.onload = res);

    const results = await reader.decode(img);

    if (results.length > 0) {
        const code = results[0].barcodeText;
        document.getElementById("result").innerHTML = "Detected: " + code;

        // Redirect to PHP
        window.location.href = "process_scan.php?code=" + encodeURIComponent(code);
    } else {
        document.getElementById("result").innerHTML = "No barcode detected";
    }
});
</script>

</body>
</html>
