<!-- SECCIÓN DE BARBEROS: Carrusel Dinámico de Maestros Barberos en fondo negro (ink) -->
<section id="barberos" class="scroll-mt-24 bg-ink text-bone py-24 sm:py-32 border-b border-line-dark overflow-hidden">
    <div class="max-w-6xl mx-auto px-6">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 pb-6 border-b border-line-dark gap-6">
            <div>
                <h2 class="font-serif text-3xl sm:text-5xl font-bold tracking-tight text-bone">
                    Maestros Barberos
                </h2>
            </div>
            
            <div class="flex items-center justify-between md:justify-end gap-6 w-full md:w-auto">
                <p class="text-muted-dark text-sm max-w-xs font-normal leading-relaxed hidden sm:block">
                    Técnicos certificados con dominio en visagismo, tijera clásica y navaja libre en el campus UTN.
                </p>
                <!-- Controles del Carrusel (Flechas de navegación) -->
                <div class="flex items-center gap-3">
                    <button id="btn-barberos-prev" type="button" aria-label="Ver barbero anterior"
                            class="w-12 h-12 rounded-full border border-line-dark hover:border-gold text-bone hover:text-gold flex items-center justify-center transition-all duration-200 bg-ink hover:bg-white/5 active:scale-95 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button id="btn-barberos-next" type="button" aria-label="Ver siguiente barbero"
                            class="w-12 h-12 rounded-full border border-line-dark hover:border-gold text-bone hover:text-gold flex items-center justify-center transition-all duration-200 bg-ink hover:bg-white/5 active:scale-95 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Contenedor del Carrusel (Scroll suave + Snap horizontal + Arrastre) -->
        <div class="relative">
            <div id="grid-barberos" 
                 class="flex gap-8 overflow-x-auto scroll-smooth snap-x snap-mandatory py-2 no-scrollbar -mx-2 px-2 cursor-grab active:cursor-grabbing">
                <div class="min-w-[280px] p-8 border border-line-dark rounded-lg animate-pulse text-muted-dark text-sm">
                    Cargando barberos...
                </div>
            </div>
        </div>

    </div>
</section>
