const form = document.getElementById("pesanForm");
const media = document.getElementById("media");

const previewContainer =
    document.getElementById("previewContainer");

const previewImage =
    document.getElementById("previewImage");

const previewVideo =
    document.getElementById("previewVideo");

const hasil =
    document.getElementById("hasil");

const kirimBtn =
    document.getElementById("kirimBtn");


// ============================
// PREVIEW FOTO / VIDEO
// ============================

media.addEventListener("change", function () {

    const file = this.files[0];

    if (!file) {
        previewContainer.style.display = "none";
        previewImage.style.display = "none";
        previewVideo.style.display = "none";
        return;
    }

    const url = URL.createObjectURL(file);

    previewContainer.style.display = "block";

    if (file.type.startsWith("image/")) {

        previewImage.src = url;

        previewImage.style.display = "block";
        previewVideo.style.display = "none";

    } else if (file.type.startsWith("video/")) {

        previewVideo.src = url;

        previewVideo.style.display = "block";
        previewImage.style.display = "none";

    }

});


// ============================
// KIRIM PESAN
// ============================

form.addEventListener("submit", async function (event) {

    event.preventDefault();

    if (kirimBtn.disabled) {
        return;
    }

    const data = new FormData(form);

    kirimBtn.disabled = true;
    kirimBtn.textContent = "⏳ MENGIRIM...";

    hasil.className = "";
    hasil.textContent = "";

    try {

        const response = await fetch("send.php", {
            method: "POST",
            body: data
        });

        const result = await response.json();

        if (result.success) {

            hasil.className = "success";

            hasil.textContent =
                "✅ Pesan berhasil dikirim ke Telegram imam";

            form.reset();

            previewContainer.style.display = "none";
            previewImage.style.display = "none";
            previewVideo.style.display = "none";

        } else {

            hasil.className = "";

            hasil.textContent =
                "❌Pesan tidak terkirim klik 1 kali lagi!." + result.message;

        }

    } catch (error) {

        hasil.className = "";

        hasil.textContent =
            "❌ Pesan tidak terkirim klik 1 kali lagi!.";

    }

    kirimBtn.disabled = false;

    kirimBtn.textContent =
        "KIRIM PESAN";

});


// ============================
// AKTIFKAN SUARA VIDEO
// ============================

const video =
    document.querySelector(".media-box video");

document.addEventListener("click", function () {

    if (video) {

        video.muted = false;
        video.volume = 1;
        video.play();

    }

}, { once: true });