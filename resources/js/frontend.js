// Main Frontend JS Entry Point

import './frontend/cart.js';
import './frontend/counter.js';
import './frontend/TabbedSwiperHandler.js';

// Npm libraries
import '@fortawesome/fontawesome-free/css/all.min.css';
import 'swiper/css/bundle';
import Swiper from 'swiper/bundle';

window.Swiper = Swiper;

// Import Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
