document.addEventListener("DOMContentLoaded", () => {
    const viewer = document.querySelector("model-viewer");
    if (viewer) {
        viewer.addEventListener("error", (evt) => {
            console.error("3D 模型加载错误:", evt);
            const errDiv = document.getElementById("model-error");
            if (errDiv) errDiv.classList.remove("d-none");
        });

        // 监听 AR 状态
        viewer.addEventListener("ar-status", (event) => {
            if (event.detail.status === "failed") {
                alert("启动 AR 失败，请确保使用的设备与浏览器支持 AR 功能。");
            }
        });
    }
});