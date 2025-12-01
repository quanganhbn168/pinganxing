// public/js/TabbedSwiperHandler.js

class TabbedSwiperHandler {
    /**
     * @param {string} containerSelector - Selector cho container chính chứa cả tabs và tab-content.
     */
    constructor(containerSelector) {
        this.container = document.querySelector(containerSelector);
        if (!this.container) {
            console.error(`Container not found for selector: ${containerSelector}`);
            return;
        }

        this.tabs = this.container.querySelectorAll('[data-bs-toggle="pill"]');
        this.swiperInstances = new Map(); // Lưu trữ các instance của Swiper để tránh khởi tạo lại
    }

    /**
     * Khởi tạo Swiper config chung
     * @returns {object} - Cấu hình cho Swiper
     */
    getSwiperConfig(pane) {
        return {
            loop: true,
            centeredSlides: true,
            grabCursor: true,
            breakpoints: {
                // Màn hình nhỏ: 1 slide chính giữa, 2 bên ẩn bớt
                320: {
                    slidesPerView: 1.5,
                    spaceBetween: 15,
                },
                // Màn hình lớn: 3 slide chính giữa, 2 bên ẩn bớt
                992: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                }
            },
            navigation: {
                nextEl: pane.querySelector('.swiper-button-next'),
                prevEl: pane.querySelector('.swiper-button-prev'),
            },
            pagination: {
                el: pane.querySelector('.swiper-pagination'),
                clickable: true,
                // Render pagination dạng thanh progress
                renderBullet: function (index, className) {
                    return `<span class="${className}"><span class="progress-bar"></span></span>`;
                },
            },
        };
    }

    /**
     * Khởi tạo một Swiper instance cho một tab-pane cụ thể
     * @param {HTMLElement} pane - Element của tab-pane cần khởi tạo slider.
     */
    initSwiperForPane(pane) {
        const paneId = pane.id;
        // Nếu đã có instance rồi thì không làm gì cả
        if (this.swiperInstances.has(paneId)) {
            return;
        }

        const swiperContainer = pane.querySelector('.project-swiper');
        if (swiperContainer && swiperContainer.querySelectorAll('.swiper-slide').length > 0) {
            const config = this.getSwiperConfig(pane);
            const swiper = new Swiper(swiperContainer, config);
            this.swiperInstances.set(paneId, swiper);
        }
    }

    /**
     * Bắt đầu lắng nghe sự kiện và khởi tạo slider đầu tiên
     */
    init() {
        if (!this.container) return;

        // 1. Khởi tạo slider cho tab đang active lúc tải trang
        const initialActivePane = this.container.querySelector('.tab-pane.active');
        if (initialActivePane) {
            // Đảm bảo slider được khởi tạo sau khi mọi thứ đã sẵn sàng
            setTimeout(() => {
                this.initSwiperForPane(initialActivePane);
            }, 100);
        }

        // 2. Lắng nghe sự kiện khi một tab được hiển thị
        this.tabs.forEach(tab => {
            tab.addEventListener('shown.bs.tab', (event) => {
                const targetPaneId = event.target.getAttribute('data-bs-target');
                const targetPane = document.querySelector(targetPaneId);
                this.initSwiperForPane(targetPane);
            });
        });
    }
}