<?php
$carImages = $car['images'] ?? [];
$hasImages = $carImages !== [];
$popular = array_slice($popular_cars ?? [], 0, 4);
?>

<section class="car-hero" aria-labelledby="car-hero-title">
    <div class="container">
        <div class="car-hero__content">
            <div class="car-hero__left">
                <nav class="breadcrumb" role="navigation" aria-label="Навигация по сайту">
                    <a href="<?= e(route_url('home')) ?>" class="breadcrumb__link">Главная</a>
                    <span class="breadcrumb__separator" aria-hidden="true">/</span>
                    <a href="<?= e(route_url('catalog_list')) ?>" class="breadcrumb__link">Каталог</a>
                    <span class="breadcrumb__separator" aria-hidden="true">/</span>
                    <span class="breadcrumb__current"><?= e((string) $car['manufacturer']) ?> <?= e((string) $car['model']) ?></span>
                </nav>

                <div class="car-hero__main-info">
                    <div class="car-hero__brand-badge"><span class="car-hero__brand"><?= e((string) $car['manufacturer']) ?></span></div>
                    <h1 class="car-hero__title" id="car-hero-title"><?= e((string) $car['model']) ?></h1>

                    <div class="car-hero__meta">
                        <div class="car-hero__meta-item"><span><?= e((string) $car['year']) ?> год</span></div>
                        <div class="car-hero__meta-item"><span><?= e(origin_label((string) $car['origin'])) ?></span></div>
                    </div>
                </div>

                <div class="car-hero__pricing">
                    <div class="car-hero__price-row">
                        <span class="car-hero__price-value"><?= e(format_price((float) $car['price'])) ?></span>
                    </div>

                    <div class="car-hero__actions">
                        <a href="#" class="car-hero__cta-btn" data-open-modal="request">
                            <span class="car-hero__cta-text">Оставить заявку</span>
                        </a>
                        <div class="car-hero__guarantee"><span>Бесплатный расчёт</span></div>
                    </div>
                </div>

                <div class="car-hero__specs">
                    <h3 class="car-hero__specs-title">Характеристики</h3>
                    <div class="car-hero__specs-grid">
                        <div class="car-hero__spec-card"><div class="car-hero__spec-content"><div class="car-hero__spec-value"><?= e(format_intcomma((int) $car['mileage_km'])) ?> км</div><div class="car-hero__spec-label">Пробег</div></div></div>
                        <div class="car-hero__spec-card"><div class="car-hero__spec-content"><div class="car-hero__spec-value"><?= e(value_or_dash((string) ($car['engine_volume'] ?? ''))) ?></div><div class="car-hero__spec-label">Двигатель</div></div></div>
                        <div class="car-hero__spec-card"><div class="car-hero__spec-content"><div class="car-hero__spec-value"><?= e(value_or_dash((string) ($car['fuel'] ?? ''))) ?></div><div class="car-hero__spec-label">Топливо</div></div></div>
                        <div class="car-hero__spec-card"><div class="car-hero__spec-content"><div class="car-hero__spec-value"><?= e(value_or_dash((string) ($car['body_type'] ?? ''))) ?></div><div class="car-hero__spec-label">Кузов</div></div></div>
                        <div class="car-hero__spec-card"><div class="car-hero__spec-content"><div class="car-hero__spec-value"><?= e(value_or_dash((string) ($car['drive'] ?? ''))) ?></div><div class="car-hero__spec-label">Привод</div></div></div>
                    </div>
                </div>
            </div>

            <div class="car-hero__right" aria-hidden="true">
                <div class="car-hero__gallery">
                    <div class="car-gallery">
                        <div class="car-gallery__main">
                            <div class="car-gallery__main-wrapper">
                                <?php if ($hasImages): ?>
                                    <?php foreach ($carImages as $index => $image): ?>
                                        <?php
                                        $imgUrl = $image['url'] ?? asset('images/placeholder-car.jpg');
                                        $imgAlt = trim((string) ($image['alt_text'] ?? ''));
                                        if ($imgAlt === '') {
                                            $imgAlt = (string) ($car['name'] ?? 'Автомобиль');
                                        }
                                        ?>
                                        <div class="car-gallery__slide <?= $index === 0 ? 'car-gallery__slide--active' : '' ?>" data-image="<?= e((string) $imgUrl) ?>" data-alt="<?= e($imgAlt) ?>">
                                            <div class="car-gallery__image-container">
                                                <img src="<?= e((string) $imgUrl) ?>" alt="<?= e($imgAlt) ?>" class="car-gallery__image">
                                                <div class="car-gallery__overlay"></div>
                                                <button class="car-gallery__zoom-btn" type="button" aria-label="Увеличить фото"></button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="car-gallery__slide car-gallery__slide--active" data-image="<?= e(asset('images/placeholder-car.jpg')) ?>" data-alt="<?= e((string) ($car['name'] ?? 'Автомобиль')) ?>">
                                        <div class="car-gallery__image-container">
                                            <img src="<?= e(asset('images/placeholder-car.jpg')) ?>" alt="<?= e((string) ($car['name'] ?? 'Автомобиль')) ?>" class="car-gallery__image">
                                            <div class="car-gallery__overlay"></div>
                                            <button class="car-gallery__zoom-btn" type="button" aria-label="Увеличить фото"></button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (count($carImages) > 1): ?>
                                <div class="car-gallery__navigation">
                                    <button class="car-gallery__nav-btn car-gallery__nav-btn--prev" type="button" aria-label="Предыдущее фото"></button>
                                    <div class="car-gallery__indicators">
                                        <?php foreach ($carImages as $index => $image): ?>
                                            <button class="car-gallery__indicator <?= $index === 0 ? 'car-gallery__indicator--active' : '' ?>" type="button" data-slide="<?= e((string) $index) ?>" aria-label="Фото <?= e((string) ($index + 1)) ?>"><span class="car-gallery__indicator-dot"></span></button>
                                        <?php endforeach; ?>
                                    </div>
                                    <button class="car-gallery__nav-btn car-gallery__nav-btn--next" type="button" aria-label="Следующее фото"></button>
                                </div>
                                <div class="car-gallery__counter">
                                    <span class="car-gallery__counter-current">1</span>
                                    <span class="car-gallery__counter-separator">/</span>
                                    <span class="car-gallery__counter-total"><?= e((string) count($carImages)) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (count($carImages) > 1): ?>
                            <div class="car-gallery__thumbnails">
                                <div class="car-gallery__thumbnails-header">
                                    <div class="car-gallery__thumbnails-info"><span class="car-gallery__thumbnails-title">Галерея автомобиля</span></div>
                                    <span class="car-gallery__thumbnails-count"><?= e((string) count($carImages)) ?> фото</span>
                                </div>
                                <div class="car-gallery__thumbnails-wrapper">
                                    <div class="car-gallery__thumbnails-track">
                                        <?php foreach ($carImages as $index => $image): ?>
                                            <?php
                                            $imgUrl = $image['url'] ?? asset('images/placeholder-car.jpg');
                                            $imgAlt = trim((string) ($image['alt_text'] ?? ''));
                                            if ($imgAlt === '') {
                                                $imgAlt = (string) ($car['name'] ?? 'Автомобиль');
                                            }
                                            ?>
                                            <button class="car-gallery__thumbnail <?= $index === 0 ? 'car-gallery__thumbnail--active' : '' ?>" type="button" data-slide="<?= e((string) $index) ?>" aria-label="Показать фото <?= e((string) ($index + 1)) ?>">
                                                <div class="car-gallery__thumbnail-image"><img src="<?= e((string) $imgUrl) ?>" alt="<?= e($imgAlt) ?>" loading="lazy"></div>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="photo-modal">
    <div class="photo-modal__overlay"></div>
    <div class="photo-modal__content">
        <button class="photo-modal__nav photo-modal__nav--prev" type="button" aria-label="Предыдущее фото"></button>
        <button class="photo-modal__nav photo-modal__nav--next" type="button" aria-label="Следующее фото"></button>
        <button class="photo-modal__close" type="button" aria-label="Закрыть"></button>
        <img src="" alt="" class="photo-modal__image">
        <div class="photo-modal__counter">
            <span class="photo-modal__counter-current">1</span>
            <span class="photo-modal__counter-separator">/</span>
            <span class="photo-modal__counter-total">1</span>
        </div>
    </div>
</div>

<?php if ($popular !== []): ?>
<section class="popular-models-section">
    <div class="container">
        <div class="popular-models__inner">
            <h2 class="popular-models__title">Популярные модели</h2>
            <p class="popular-models__subtitle">Самые востребованные автомобили в нашем каталоге</p>

            <div class="popular-models__grid">
                <?php foreach ($popular as $index => $popularCar): ?>
                    <article class="car-card">
                        <div class="car-card__img-wrap">
                            <img src="<?= e($popularCar['image_url'] ?? asset('images/placeholder-car.jpg')) ?>" alt="<?= e((string) ($popularCar['name'] ?? 'Автомобиль')) ?>" class="car-card__img">
                        </div>
                        <div class="car-card__body">
                            <p class="car-card__pub">Номер публикации #<?= e((string) (1001 + $index)) ?></p>
                            <h3 class="car-card__title"><?= e((string) $popularCar['manufacturer']) ?> <?= e((string) $popularCar['model']) ?></h3>

                            <div class="car-card__row">
                                <div class="car-card__col"><span class="car-card__label">Кузов</span><span class="car-card__value"><?= e(value_or_dash((string) ($popularCar['body_type'] ?? ''))) ?></span></div>
                                <div class="car-card__col"><span class="car-card__label">Год выпуска</span><span class="car-card__value"><?= e((string) $popularCar['year']) ?></span></div>
                            </div>

                            <div class="car-card__row">
                                <div class="car-card__col"><span class="car-card__label">Пробег</span><span class="car-card__value"><?= e(format_intcomma((int) $popularCar['mileage_km'])) ?> км</span></div>
                                <div class="car-card__col"><span class="car-card__label">Объем двигателя</span><span class="car-card__value"><?= e(value_or_dash((string) ($popularCar['engine_volume'] ?? ''))) ?></span></div>
                            </div>

                            <div class="car-card__divider"></div>

                            <div class="car-card__price-row">
                                <div><span class="car-card__label">Стоимость:</span><p class="car-card__price"><?= e(format_price((float) $popularCar['price'])) ?></p></div>
                                <a class="btn car-card__btn" href="<?= e(route_url('catalog_detail', ['id' => (int) $popularCar['id']])) ?>">Подробнее</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="popular-models__actions"><a href="<?= e(route_url('catalog_list')) ?>" class="btn btn--primary">Посмотреть все модели</a></div>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
// ================== PREMIUM CAR GALLERY FUNCTIONALITY ==================
document.addEventListener('DOMContentLoaded', function() {
    const gallery = {
        currentSlide: 0,
        slides: [],
        thumbnails: [],
        indicators: [],

        init() {
            this.slides = Array.from(document.querySelectorAll('.car-gallery__slide'));
            this.thumbnails = Array.from(document.querySelectorAll('.car-gallery__thumbnail'));
            this.indicators = Array.from(document.querySelectorAll('.car-gallery__indicator'));
            this.counter = document.querySelector('.car-gallery__counter-current');

            if (this.slides.length === 0) return;

            this.bindEvents();
            this.updateUI();
        },

        bindEvents() {
            const prevBtn = document.querySelector('.car-gallery__nav-btn--prev');
            const nextBtn = document.querySelector('.car-gallery__nav-btn--next');

            if (prevBtn) {
                prevBtn.addEventListener('click', () => this.prevSlide());
            }
            if (nextBtn) {
                nextBtn.addEventListener('click', () => this.nextSlide());
            }

            this.indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => this.goToSlide(index));
            });

            this.thumbnails.forEach((thumbnail, index) => {
                thumbnail.addEventListener('click', () => this.goToSlide(index));
            });

            document.addEventListener('keydown', (e) => {
                if (document.querySelector('.photo-modal--open')) return;

                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    this.prevSlide();
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    this.nextSlide();
                }
            });

            document.querySelectorAll('.car-gallery__zoom-btn').forEach((btn, index) => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.openModal(index);
                });
            });

            this.setupThumbnailScroll();
        },

        prevSlide() {
            this.goToSlide(this.currentSlide > 0 ? this.currentSlide - 1 : this.slides.length - 1);
        },

        nextSlide() {
            this.goToSlide(this.currentSlide < this.slides.length - 1 ? this.currentSlide + 1 : 0);
        },

        goToSlide(index) {
            if (index === this.currentSlide) return;

            this.slides[this.currentSlide].classList.remove('car-gallery__slide--active');
            this.slides[index].classList.add('car-gallery__slide--active');

            this.thumbnails.forEach((thumb, i) => {
                thumb.classList.toggle('car-gallery__thumbnail--active', i === index);
            });

            this.indicators.forEach((indicator, i) => {
                indicator.classList.toggle('car-gallery__indicator--active', i === index);
            });

            if (this.counter) {
                this.counter.textContent = index + 1;
            }

            this.currentSlide = index;
            this.scrollThumbnailIntoView();
        },

        scrollThumbnailIntoView() {
            const activeThumbnail = this.thumbnails[this.currentSlide];
            if (activeThumbnail) {
                activeThumbnail.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                    inline: 'center'
                });
            }
        },

        setupThumbnailScroll() {
            const track = document.querySelector('.car-gallery__thumbnails-track');
            if (!track) return;

            let isScrolling = false;

            track.addEventListener('scroll', () => {
                if (!isScrolling) {
                    clearTimeout(isScrolling);
                    isScrolling = setTimeout(() => {
                        isScrolling = false;
                    }, 100);
                }
            });
        },

        openModal(index) {
            const modal = document.querySelector('.photo-modal');
            const modalImage = document.querySelector('.photo-modal__image');
            const modalCounter = document.querySelector('.photo-modal__counter-current');
            const modalTotal = document.querySelector('.photo-modal__counter-total');

            if (!modal || !modalImage) return;

            const imageSrc = this.slides[index].dataset.image;
            const imageAlt = this.slides[index].dataset.alt;

            modalImage.src = imageSrc;
            modalImage.alt = imageAlt;

            if (modalCounter) modalCounter.textContent = index + 1;
            if (modalTotal) modalTotal.textContent = this.slides.length;

            document.body.style.overflow = 'hidden';

            modal.classList.add('photo-modal--open');

            this.setupModalNavigation(index);
        },

        setupModalNavigation(startIndex) {
            let currentModalIndex = startIndex;

            const modal = document.querySelector('.photo-modal');
            const prevBtn = document.querySelector('.photo-modal__nav--prev');
            const nextBtn = document.querySelector('.photo-modal__nav--next');
            const closeBtn = document.querySelector('.photo-modal__close');
            const overlay = document.querySelector('.photo-modal__overlay');
            const modalContent = document.querySelector('.photo-modal__content');
            const modalImage = document.querySelector('.photo-modal__image');
            const modalCounter = document.querySelector('.photo-modal__counter-current');

            if (!modal || !modalImage) return;

            const updateModalImage = (index) => {
                const imageSrc = this.slides[index].dataset.image;
                const imageAlt = this.slides[index].dataset.alt;

                modalImage.src = imageSrc;
                modalImage.alt = imageAlt;
                if (modalCounter) modalCounter.textContent = index + 1;

                currentModalIndex = index;
            };

            const goPrev = () => {
                const newIndex = currentModalIndex > 0 ? currentModalIndex - 1 : this.slides.length - 1;
                updateModalImage(newIndex);
            };

            const goNext = () => {
                const newIndex = currentModalIndex < this.slides.length - 1 ? currentModalIndex + 1 : 0;
                updateModalImage(newIndex);
            };

            let touchStartX = 0;
            let touchStartY = 0;
            const SWIPE_THRESHOLD = 40;
            const VERTICAL_TOLERANCE = 80;

            const handleTouchStart = (e) => {
                const touch = e.changedTouches && e.changedTouches[0];
                if (!touch) return;
                touchStartX = touch.clientX;
                touchStartY = touch.clientY;
            };

            const handleTouchEnd = (e) => {
                const touch = e.changedTouches && e.changedTouches[0];
                if (!touch) return;

                const deltaX = touch.clientX - touchStartX;
                const deltaY = touch.clientY - touchStartY;

                if (Math.abs(deltaX) < SWIPE_THRESHOLD) return;
                if (Math.abs(deltaY) > VERTICAL_TOLERANCE) return;

                if (deltaX > 0) {
                    goPrev();
                } else {
                    goNext();
                }
            };

            const handleKeydown = (e) => {
                if (!modal.classList.contains('photo-modal--open')) return;

                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    goPrev();
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    goNext();
                } else if (e.key === 'Escape') {
                    closeModal();
                }
            };

            const cleanupModalHandlers = () => {
                document.removeEventListener('keydown', handleKeydown);
                if (modalContent) {
                    modalContent.removeEventListener('touchstart', handleTouchStart);
                    modalContent.removeEventListener('touchend', handleTouchEnd);
                }
                if (prevBtn) prevBtn.onclick = null;
                if (nextBtn) nextBtn.onclick = null;
                if (closeBtn) closeBtn.onclick = null;
                if (overlay) overlay.onclick = null;
            };

            if (prevBtn) {
                prevBtn.onclick = goPrev;
            }

            if (nextBtn) {
                nextBtn.onclick = goNext;
            }

            const closeModal = () => {
                modal.classList.remove('photo-modal--open');
                document.body.style.overflow = '';
                cleanupModalHandlers();
            };

            if (closeBtn) {
                closeBtn.onclick = closeModal;
            }

            if (overlay) {
                overlay.onclick = closeModal;
            }

            document.addEventListener('keydown', handleKeydown);

            if (modalContent) {
                modalContent.addEventListener('touchstart', handleTouchStart);
                modalContent.addEventListener('touchend', handleTouchEnd);
            }
        },

        updateUI() {
            this.slides.forEach((slide, index) => {
                if (slide.classList.contains('car-gallery__slide--active')) {
                    this.currentSlide = index;
                }
            });

            if (this.counter) {
                this.counter.textContent = this.currentSlide + 1;
            }
        }
    };

    gallery.init();
});
</script>
