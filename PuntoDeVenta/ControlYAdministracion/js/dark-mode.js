/**
 * Modo oscuro - ControlYAdministracion
 * Persiste la preferencia en localStorage y aplica el tema al cargar.
 */
(function () {
    "use strict";

    var STORAGE_KEY = "control-admin-theme";
    var THEME_DARK = "dark";

    function getStoredTheme() {
        try {
            return localStorage.getItem(STORAGE_KEY);
        } catch (e) {
            return null;
        }
    }

    function setStoredTheme(value) {
        try {
            if (value) {
                localStorage.setItem(STORAGE_KEY, value);
            } else {
                localStorage.removeItem(STORAGE_KEY);
            }
        } catch (e) {}
    }

    function isDark() {
        return document.documentElement.getAttribute("data-theme") === THEME_DARK;
    }

    function applyTheme(dark) {
        if (dark) {
            document.documentElement.setAttribute("data-theme", THEME_DARK);
        } else {
            document.documentElement.removeAttribute("data-theme");
        }
        updateToggleButtons();
    }

    function toggleTheme() {
        var dark = !isDark();
        applyTheme(dark);
        setStoredTheme(dark ? THEME_DARK : null);
    }

    function updateToggleButtons() {
        var dark = isDark();
        document.querySelectorAll("[data-dark-mode-toggle]").forEach(function (btn) {
            var iconMoon = btn.querySelector(".fa-moon");
            var iconSun = btn.querySelector(".fa-sun");
            var title = btn.getAttribute("title") || btn.getAttribute("aria-label");
            if (iconMoon) iconMoon.style.display = dark ? "none" : "inline-block";
            if (iconSun) iconSun.style.display = dark ? "inline-block" : "none";
            if (title !== undefined) {
                btn.setAttribute("title", dark ? "Usar modo claro" : "Usar modo oscuro");
                if (btn.getAttribute("aria-label")) btn.setAttribute("aria-label", dark ? "Usar modo claro" : "Usar modo oscuro");
            }
        });
    }

    function ensureFloatingToggle() {
        if (document.getElementById("dark-mode-toggle-float")) return;
        var nav = document.querySelector(".content .navbar");
        if (nav && nav.querySelector("[data-dark-mode-toggle]")) return;

        var floatBtn = document.createElement("button");
        floatBtn.id = "dark-mode-toggle-float";
        floatBtn.setAttribute("type", "button");
        floatBtn.setAttribute("data-dark-mode-toggle", "1");
        floatBtn.setAttribute("title", isDark() ? "Usar modo claro" : "Usar modo oscuro");
        floatBtn.setAttribute("aria-label", floatBtn.getAttribute("title"));
        floatBtn.className = "btn btn-primary btn-lg-square position-fixed";
        floatBtn.style.cssText = "top:20px;right:20px;z-index:9998;";
        floatBtn.innerHTML = '<i class="fa fa-moon" style="display:inline-block"></i><i class="fa fa-sun" style="display:none"></i>';
        floatBtn.addEventListener("click", toggleTheme);
        document.body.appendChild(floatBtn);
        updateToggleButtons();
    }

    function init() {
        var stored = getStoredTheme();
        applyTheme(stored === THEME_DARK);

        document.querySelectorAll("[data-dark-mode-toggle]").forEach(function (btn) {
            btn.addEventListener("click", function (e) {
                e.preventDefault();
                toggleTheme();
            });
        });

        setTimeout(ensureFloatingToggle, 100);
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }
})();
