/**
 * Componente para el manejo del menú de navegación
 */
export class MenuComponent {
    constructor() {
        this.navbarToggle = document.querySelector('.navbar__toggle');
        this.navbarMenu = document.querySelector('.navbar__menu');
        this.navbarLinks = document.querySelectorAll('.navbar__link');
        this.userMenuToggle = document.querySelector('.user-menu-toggle');
        this.userMenuItem = document.querySelector('.navbar__item--user');
        
        this.init();
    }

    /**
     * Inicializa el componente
     */
    init() {
        this.setupToggle();
        this.setupSmoothScroll();
        this.setupUserMenu();
    }

    /**
     * Configura el botón de toggle para el menú en dispositivos móviles
     */
    setupToggle() {
        this.navbarToggle.addEventListener('click', () => {
            this.navbarMenu.classList.toggle('active');
        });
    }

    /**
     * Configura el scroll suave para los enlaces de navegación
     */
    setupSmoothScroll() {
        this.navbarLinks.forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                const href = anchor.getAttribute('href');

                // Solo aplicar scroll suave cuando el enlace sea a un ancla interna (#seccion)
                if (href && href.startsWith('#')) {
                    e.preventDefault();

                    const targetElement = document.querySelector(href);

                    if (targetElement) {
                        const headerOffset = document.querySelector('.header').offsetHeight;
                        const elementPosition = targetElement.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });

                        // Cerrar el menú desplegable después de hacer clic (en móviles)
                        if (window.innerWidth <= 768) {
                            this.navbarMenu.classList.remove('active');
                        }
                    }
                } else {
                    // Enlaces a otras páginas funcionan normalmente, solo cerramos el menú en móviles
                    if (window.innerWidth <= 768) {
                        this.navbarMenu.classList.remove('active');
                    }
                }
            });
        });
    }

    /**
     * Configura el menú de usuario desplegable
     */
    setupUserMenu() {
        if (!this.userMenuToggle || !this.userMenuItem) {
            return;
        }

        // Abrir/cerrar el menú al hacer clic en el icono/etiqueta de usuario
        this.userMenuToggle.addEventListener('click', (event) => {
            event.stopPropagation();
            this.userMenuItem.classList.toggle('open');
        });

        // Cerrar el menú si se hace clic fuera
        document.addEventListener('click', (event) => {
            if (!this.userMenuItem.classList.contains('open')) {
                return;
            }

            if (!this.userMenuItem.contains(event.target)) {
                this.userMenuItem.classList.remove('open');
            }
        });
    }
}
