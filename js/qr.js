function generateQRCode(elementId, url) {
    const container = document.getElementById(elementId);
    if (container && typeof QRCode !== 'undefined') {
        container.innerHTML = "";
        new QRCode(container, {
            text: url,
            width: 128,
            height: 128,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    }
}