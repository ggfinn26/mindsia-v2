document.addEventListener("alpine:init", () => {
    Alpine.data("attendanceCamera", (config = {}) => ({
        showModal: false,
        step: "camera", // camera | captured | submitting
        stream: null,
        latitude: null,
        longitude: null,
        locationReady: false,
        locationError: "",
        capturedBlob: null,
        capturedDataUrl: null,
        notes: "",
        error: "",
        actionType: "",
        actionUrl: "",

        open(type, url) {
            this.actionType = type;
            this.actionUrl = url;
            this.step = "camera";
            this.capturedBlob = null;
            this.capturedDataUrl = null;
            this.notes = "";
            this.error = "";
            this.showModal = true;
            this.$nextTick(() => this._startCamera());
            this._getLocation();
        },

        _getLocation() {
            if (!navigator.geolocation) {
                this.locationError = "Browser tidak mendukung geolokasi.";
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.latitude = pos.coords.latitude;
                    this.longitude = pos.coords.longitude;
                    this.locationReady = true;
                    this.locationError = "";
                },
                () => {
                    this.locationError = "Izin lokasi ditolak. Aktifkan lokasi lalu coba lagi.";
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 },
            );
        },

        _startCamera() {
            const video = this.$refs.cameraVideo;
            if (!video) return;
            navigator.mediaDevices
                .getUserMedia({ video: { facingMode: "user" }, audio: false })
                .then((stream) => {
                    this.stream = stream;
                    video.srcObject = stream;
                })
                .catch(() => {
                    this.error = "Tidak dapat mengakses kamera. Izinkan akses kamera lalu coba lagi.";
                });
        },

        _stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach((t) => t.stop());
                this.stream = null;
            }
        },

        capture() {
            const video = this.$refs.cameraVideo;
            const canvas = this.$refs.cameraCanvas;
            if (!video || !canvas) return;
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext("2d").drawImage(video, 0, 0);
            this.capturedDataUrl = canvas.toDataURL("image/jpeg", 0.85);
            canvas.toBlob(
                (blob) => {
                    this.capturedBlob = blob;
                },
                "image/jpeg",
                0.85,
            );
            this._stopCamera();
            this.step = "captured";
        },

        retake() {
            this.step = "camera";
            this.capturedBlob = null;
            this.capturedDataUrl = null;
            this.$nextTick(() => this._startCamera());
        },

        close() {
            this._stopCamera();
            this.showModal = false;
        },

        async submit() {
            if (!this.locationReady) {
                this.error = "Lokasi belum tersedia. Tunggu sebentar atau izinkan akses lokasi.";
                return;
            }

            this.step = "submitting";
            this.error = "";

            const fd = new FormData();
            fd.append("_token", config.csrfToken);

            const prefix = this.actionType === "check-in" ? "check_in" : "check_out";
            fd.append(`${prefix}_latitude`, this.latitude);
            fd.append(`${prefix}_longitude`, this.longitude);
            if (this.capturedBlob) {
                fd.append("selfie", this.capturedBlob, "selfie.jpg");
            }
            if (this.notes.trim()) {
                fd.append(`${prefix}_notes`, this.notes.trim());
            }

            try {
                const resp = await fetch(this.actionUrl, { method: "POST", body: fd, redirect: "follow" });
                if (resp.ok || resp.redirected) {
                    window.location.reload();
                    return;
                }
                const json = await resp.json().catch(() => null);
                const firstError = json?.errors ? Object.values(json.errors)[0]?.[0] : null;
                this.error = firstError || json?.message || "Gagal menyimpan absensi.";
                this.step = "captured";
            } catch {
                this.error = "Terjadi kesalahan jaringan.";
                this.step = "captured";
            }
        },
    }));
});
