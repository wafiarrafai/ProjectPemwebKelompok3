document.addEventListener("DOMContentLoaded", function () {
  const authBody = document.querySelector(".auth-body");
  const visualPanel = document.querySelector(".login-left");

  if (!authBody) return;

  authBody.classList.add("auth-motion-ready");

  if (!visualPanel || window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
    return;
  }

  visualPanel.addEventListener("pointermove", function (event) {
    const bounds = visualPanel.getBoundingClientRect();
    const x = Math.round(((event.clientX - bounds.left) / bounds.width) * 100);
    const y = Math.round(((event.clientY - bounds.top) / bounds.height) * 100);

    visualPanel.style.setProperty("--auth-pointer-x", `${x}%`);
    visualPanel.style.setProperty("--auth-pointer-y", `${y}%`);
  }, { passive: true });

  visualPanel.addEventListener("pointerleave", function () {
    visualPanel.style.removeProperty("--auth-pointer-x");
    visualPanel.style.removeProperty("--auth-pointer-y");
  });
});
