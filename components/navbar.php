<!-- NAVBAR: Minimalista, moderna y de lujo discreto -->
<header class="sticky top-0 z-50 bg-ink/90 backdrop-blur-md border-b border-line-dark transition-colors duration-200">
    <div class="max-w-6xl mx-auto px-6 h-20 flex justify-between items-center">
        
        <!-- Logotipo -->
        <a href="#" class="flex items-center gap-3 group focus:outline-none">
            <span class="font-serif text-2xl font-bold tracking-tight text-bone group-hover:text-gold transition-colors duration-200">
                Tech Barbería
            </span>
            <span class="text-xs uppercase tracking-widest text-muted-dark border-l border-line-dark pl-3 font-sans">
                UTN
            </span>
        </a>

        <!-- Enlaces de navegación desktop (Sin cápsulas ni pastillas) -->
        <nav class="hidden md:flex items-center gap-8 text-sm font-normal text-muted-dark" aria-label="Navegación principal">
            <a href="#servicios" class="hover:text-bone transition-colors duration-200">Servicios</a>
            <a href="#barberos" class="hover:text-bone transition-colors duration-200">Barberos</a>
            <a href="#consultar-cita" class="hover:text-bone transition-colors duration-200">Consultar cita</a>
            <a href="#cuenta-cliente" class="hover:text-bone transition-colors duration-200">Mi cuenta</a>
        </nav>

        <!-- Botón de acción principal -->
        <div class="hidden md:flex items-center">
            <a href="#reservar" class="inline-flex items-center justify-center px-6 py-2.5 rounded-full bg-gold hover:bg-[#b8923e] text-ink font-medium text-xs tracking-wider uppercase transition-all duration-200 shadow-sm">
                Agendar cita
            </a>
        </div>

        <!-- Botón Menú Hamburguesa Móvil -->
        <button id="btn-menu-movil" type="button" class="md:hidden text-bone p-2 hover:text-gold focus:outline-none" aria-label="Abrir menú de navegación" aria-expanded="false">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
        </button>
    </div>

    <!-- Panel Lateral Desplegable Móvil -->
    <div id="panel-menu-movil" class="fixed inset-0 z-50 bg-ink/95 backdrop-blur-xl md:hidden transition-opacity duration-300 opacity-0 pointer-events-none flex flex-col justify-between p-8" aria-hidden="true">
        <div>
            <div class="flex justify-between items-center pb-6 border-b border-line-dark">
                <span class="font-serif text-xl font-bold text-bone">Tech Barbería <span class="text-xs font-sans text-muted-dark">UTN</span></span>
                <button id="btn-cerrar-menu-movil" class="text-muted-dark hover:text-bone p-2 focus:outline-none" aria-label="Cerrar menú">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <nav class="flex flex-col gap-6 mt-10 text-lg font-serif">
                <a href="#servicios" class="enlace-movil text-muted-dark hover:text-bone transition-colors">Servicios</a>
                <a href="#barberos" class="enlace-movil text-muted-dark hover:text-bone transition-colors">Barberos</a>
                <a href="#consultar-cita" class="enlace-movil text-muted-dark hover:text-bone transition-colors">Consultar cita</a>
                <a href="#cuenta-cliente" class="enlace-movil text-muted-dark hover:text-bone transition-colors">Mi cuenta</a>
            </nav>
        </div>
        <div class="pt-6 border-t border-line-dark">
            <a href="#reservar" class="enlace-movil block w-full text-center py-3.5 rounded-full bg-gold text-ink font-medium text-sm tracking-wider uppercase">
                Agendar cita
            </a>
            <p class="text-xs text-muted-dark text-center mt-4 font-sans">Lunes a Sábado 10:00 – 20:00 · Campus UTN</p>
        </div>
    </div>
</header>
