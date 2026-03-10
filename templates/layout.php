<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e((string) ($title ?? 'AkibaAuto')) ?></title>
    <link rel="icon" type="image/png" href="<?= e(asset('images/akiba_auto_min.png')) ?>">
    <link rel="apple-touch-icon" href="<?= e(asset('images/logo-akiba.png')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/styles.css')) ?>?v=73">
    <script type="module" src="https://unpkg.com/@splinetool/viewer@1.12.13/build/spline-viewer.js"></script>
</head>
<body class="page-loading">
<div id="page">
    <header class="site-header">
        <div class="site-header__bg" aria-hidden="true"></div>
        <div class="container site-header__content">
            <div class="site-header__top">
                <a href="<?= e(route_url('home')) ?>" class="logo" aria-label="Akiba Auto — на главную">
                    <img src="<?= e(asset('images/logo-akiba.png')) ?>" alt="Akiba Auto" class="logo__img">
                </a>
                <div class="header-phone-block">
                    <a href="tel:88006009239" class="header-phone">8-800-600-9239</a>
                    <div class="header-phone__note">Звонок по РФ бесплатный</div>
                </div>
                <div class="site-header__top-right">
                    <div class="site-header__socials" aria-label="Мы в соцсетях">
                        <a href="https://t.me/akibaautovl" target="_blank" class="social-btn" aria-label="Telegram">
                            <img src="<?= e(asset('images/Кнопки мобила/тг иконка/20682202 6.png')) ?>" alt="Telegram" class="social-btn__icon">
                        </a>
                        <a href="https://www.instagram.com/akibaauto/" target="_blank" class="social-btn" aria-label="Instagram">
                            <img src="<?= e(asset('images/Кнопки мобила/инст иконка/2068220 7.png')) ?>" alt="Instagram" class="social-btn__icon">
                        </a>
                        <a href="https://www.youtube.com/channel/UC4PVZayQJqVTBPuBlxFliUw" target="_blank" class="social-btn" aria-label="YouTube">
                            <img src="<?= e(asset('images/Кнопки мобила/ютуб иконка/2075630 5.png')) ?>" alt="YouTube" class="social-btn__icon">
                        </a>
                        <a href="https://vk.com/akibaauto" target="_blank" class="social-btn" aria-label="VK">
                            <img src="<?= e(asset('images/Кнопки мобила/вк иконка/VK_Compact_Logo_(2021-present)_svg-edited-free (carve.photos) 4.png')) ?>" alt="VK" class="social-btn__icon">
                        </a>
                    </div>
                    <a href="#" class="btn btn--calc" data-open-modal="request">Рассчитать стоимость авто</a>
                </div>
                <button class="burger" aria-label="Открыть меню" aria-expanded="false" aria-controls="main-nav">
                    <span></span><span></span><span></span>
                </button>
            </div>
            <div class="site-header__divider"></div>
            <nav class="site-header__nav" id="main-nav" aria-label="Основное меню">
                <div class="site-header__nav-links">
                    <a href="<?= e(route_url('home')) ?>" class="nav-pill <?= ($active_page ?? '') === 'home' ? 'nav-pill--active' : '' ?>">Главная</a>
                    <a href="<?= e(route_url('about')) ?>" class="nav-pill <?= ($active_page ?? '') === 'about' ? 'nav-pill--active' : '' ?>">О компании</a>
                    <a href="<?= e(route_url('process')) ?>" class="nav-pill <?= ($active_page ?? '') === 'process' ? 'nav-pill--active' : '' ?>">Процесс покупки</a>
                    <a href="<?= e(route_url('catalog_list')) ?>" class="nav-pill <?= ($active_page ?? '') === 'catalog' ? 'nav-pill--active' : '' ?>">Каталог</a>
                    <a href="<?= e(route_url('contacts')) ?>" class="nav-pill <?= ($active_page ?? '') === 'contacts' ? 'nav-pill--active' : '' ?>">Контакты</a>
                </div>
            </nav>
        </div>
    </header>

    <div class="page-offset" aria-hidden="true"></div>

    <main>
        <?= $content ?>
    </main>

    <footer class="site-footer">
        <div class="site-footer__glow" aria-hidden="true"></div>
        <div class="site-footer__accent" aria-hidden="true"></div>
        <div class="container site-footer__inner">
            <div class="site-footer__top">
                <div class="site-footer__brand">
                    <a href="<?= e(route_url('home')) ?>" class="footer-logo" aria-label="Akiba Auto — на главную">
                        <img src="<?= e(asset('images/logo-akiba.png')) ?>" alt="Akiba Auto" class="footer-logo__img">
                    </a>
                    <p class="site-footer__tagline">Автомобили из Японии, Кореи и Китая</p>
                </div>

                <div class="site-footer__cta">
                    <a href="#" class="btn btn--footer" data-open-modal="request">
                        <svg class="btn__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Оставить заявку
                    </a>
                </div>

                <div class="site-footer__socials" aria-label="Мы в соцсетях">
                    <a href="https://t.me/akibaautovl" target="_blank" class="footer-social" aria-label="Telegram">
                        <img src="<?= e(asset('images/Кнопки мобила/тг иконка/20682202 6.png')) ?>" alt="Telegram" class="footer-social__icon">
                    </a>
                    <a href="https://www.instagram.com/akibaauto/" target="_blank" class="footer-social" aria-label="Instagram">
                        <img src="<?= e(asset('images/Кнопки мобила/инст иконка/2068220 7.png')) ?>" alt="Instagram" class="footer-social__icon">
                    </a>
                    <a href="https://www.youtube.com/channel/UC4PVZayQJqVTBPuBlxFliUw" target="_blank" class="footer-social" aria-label="YouTube">
                        <img src="<?= e(asset('images/Кнопки мобила/ютуб иконка/2075630 5.png')) ?>" alt="YouTube" class="footer-social__icon">
                    </a>
                    <a href="https://vk.com/akibaauto" target="_blank" class="footer-social" aria-label="VK">
                        <img src="<?= e(asset('images/Кнопки мобила/вк иконка/VK_Compact_Logo_(2021-present)_svg-edited-free (carve.photos) 4.png')) ?>" alt="VK" class="footer-social__icon">
                    </a>
                </div>
            </div>

            <div class="site-footer__divider"></div>

            <div class="site-footer__bottom">
                <p class="site-footer__copyright">2020-<?= e(year_now()) ?> AkibaAuto. Все права защищены. ОГРН: 1212500019258 ИНН: 2540263103</p>
            </div>
        </div>
    </footer>
</div>

<div class="request-modal" id="requestModal" style="--union-bg: url('<?= e(asset('images/Union.png')) ?>');">
    <div class="request-modal__overlay" id="requestModalOverlay"></div>
    <div class="request-modal__content">
        <button class="request-modal__close" id="requestModalClose" aria-label="Закрыть">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <h2 class="request-modal__title">Заявка на подбор</h2>
        <p class="request-modal__subtitle">
            Наш менеджер свяжется с вами и поможет с выбором автомобиля под ваши потребности и бюджет
        </p>

        <form class="request-modal__form" id="requestForm">
            <input type="hidden" name="source_page" value="<?= e(current_url()) ?>">
            <input type="hidden" name="car_id" value="<?= isset($request_car_id) ? e((string) $request_car_id) : '' ?>">

            <div class="request-modal__field">
                <input
                    type="text"
                    name="name"
                    id="requestName"
                    class="request-modal__input"
                    placeholder="Ваше имя"
                    required
                >
            </div>

            <div class="request-modal__field">
                <input
                    type="tel"
                    name="phone"
                    id="requestPhone"
                    class="request-modal__input"
                    placeholder="Номер телефона"
                    required
                >
            </div>

            <div class="request-modal__field">
                <input
                    type="text"
                    name="budget"
                    id="requestBudget"
                    class="request-modal__input"
                    placeholder="Примерный бюджет"
                    required
                >
            </div>

            <div class="request-modal__field">
                <textarea
                    name="wishes"
                    id="requestWishes"
                    class="request-modal__textarea"
                    placeholder="Пожелания по авто (марка, модель, год, объем двигателя и т.д.)"
                    rows="3"
                ></textarea>
            </div>

            <button type="submit" class="request-modal__submit">
                Отправить
            </button>

            <p class="request-modal__privacy">
                Нажимая «Отправить» Вы даете согласие на обработку персональных данных в соответствии с политикой конфиденциальности. Ваши данные не передаются третьим лицам.
            </p>
        </form>
    </div>
</div>

<script>
window.AKIBA_BASE_URL = <?= json_encode(app_base_path(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
window.AKIBA_API_REQUEST_URL = <?= json_encode(route_url('api_request'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="<?= e(asset('js/main.js')) ?>?v=1" defer></script>
</body>
</html>
