import $ from "jquery";
window.$ = window.jQuery = $;

import "./bootstrap";
import "select2/dist/js/select2.full.min.js";
import "select2/dist/css/select2.min.css";
import Swal from "sweetalert2";
window.Swal = Swal;

// Alpine (si lo ocupas en el storefront)
import Alpine from "alpinejs";
window.Alpine = Alpine;
Alpine.start();
