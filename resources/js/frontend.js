// Main Frontend JS Entry Point

import './frontend/counter.js';
import './frontend/TabbedSwiperHandler.js';

// Npm libraries
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import 'swiper/css/bundle';
import Swiper from 'swiper/bundle';
import AOS from 'aos';
import 'aos/dist/aos.css';
import 'flowbite';

window.Swal = Swal;
window.Swiper = Swiper;
window.AOS = AOS;

// Import Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

const wechatQrModal = document.getElementById('wechat-qr-modal');

if (wechatQrModal) {
    let lastTrigger = null;

    document.querySelectorAll('[data-wechat-qr-trigger]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            lastTrigger = trigger;

            if (!wechatQrModal.open) {
                wechatQrModal.showModal();
            }
        });
    });

    wechatQrModal.querySelectorAll('[data-wechat-qr-close]').forEach((button) => {
        button.addEventListener('click', () => wechatQrModal.close());
    });

    wechatQrModal.addEventListener('click', (event) => {
        if (event.target === wechatQrModal) {
            wechatQrModal.close();
        }
    });

    wechatQrModal.addEventListener('close', () => {
        lastTrigger?.focus();
    });
}

AOS.init({
    duration: 650,
    easing: 'ease-out-cubic',
    once: true,
    offset: 80,
    disable: () => window.matchMedia('(prefers-reduced-motion: reduce)').matches,
});
