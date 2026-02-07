function showAlert(type, message, redirectUrl = null) {
    Swal.fire({
        title: type === 'success' ? "Success!" : "Error!",
        text: message,
        icon: type,
        // width: '50%',
        // heightAuto: false,
        customClass: {
            popup: 'custom-swal-popup',
            confirmButton: 'swal2-confirm'
        }
    }).then((result) => {
        if (result.isConfirmed && redirectUrl) {
            window.location.href = redirectUrl;
        }
    });
}

async function getBase64Image(imgUrl) {
    return new Promise((resolve, reject) => {
        var img = new Image();
        img.crossOrigin = 'Anonymous';
        img.onload = function () {
            var canvas = document.createElement('CANVAS');
            var ctx = canvas.getContext('2d');
            canvas.height = this.naturalHeight;
            canvas.width = this.naturalWidth;
            ctx.drawImage(this, 0, 0);
            var dataURL = canvas.toDataURL();
            resolve(dataURL);
        };
        img.onerror = function () {
            reject('Failed to load image');
        };
        img.src = imgUrl;
        if (img.complete || img.complete === undefined) {
            img.src = 'data:image/jpg;base64,' + imgUrl;
        }
    });
}
