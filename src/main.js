import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";

// Bootstrap


import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";
import "bootstrap-icons/font/bootstrap-icons.css";
// main.js
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import { nextTick } from 'vue';

const openEditModal = (asset) => {
  isEditMode.value = true;
  Object.assign(editForm, asset);
  newImageFile.value = null;

  nextTick(() => {
    const modalEl = document.getElementById("assetModal");
    modalInstance = new window.bootstrap.Modal(modalEl);
    modalInstance.show();
  });
};
createApp(App).use(router).mount("#app");

