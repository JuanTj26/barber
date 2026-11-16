<!-- SECCIÓN DE GESTIÓN DE CITAS Y CUENTA: Fondo negro (ink) -->
<section id="consultar-cita" class="scroll-mt-24 bg-ink text-bone py-24 sm:py-32 border-b border-line-dark">
    <div class="max-w-4xl mx-auto px-6">
        
        <div class="text-center space-y-3 mb-16">
            <h2 class="font-serif text-3xl sm:text-5xl font-bold tracking-tight text-bone">
                Consultar o Reprogramar
            </h2>
            <p class="text-muted-dark text-sm max-w-md mx-auto leading-relaxed">
                Revisa el estado de tu cita mediante tu folio o accede a tu cuenta para gestionar tus visitas en el campus UTN.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Columna 1: Consulta por Folio -->
            <div class="bg-ink p-8 rounded-lg border border-line-dark flex flex-col justify-between">
                <div>
                    <h3 class="font-serif text-xl font-bold text-bone mb-2">Búsqueda por Folio</h3>
                    <p class="text-xs text-muted-dark mb-6 leading-relaxed">
                        Ingresa el código alfanumérico de 10 caracteres que recibiste al confirmar tu reservación.
                    </p>

                    <form id="form-consulta" class="flex gap-2">
                        <input id="folio-consulta" required placeholder="Ej. A1B2C3D4E5" 
                               class="min-w-0 flex-1 bg-ink border border-line-dark rounded-lg px-4 py-2.5 text-bone text-xs placeholder-muted-dark focus:border-gold focus:outline-none transition">
                        <button type="submit" class="px-5 py-2.5 rounded-full bg-gold hover:bg-[#b8923e] text-ink text-xs font-medium uppercase tracking-wider transition">
                            Buscar
                        </button>
                    </form>
                </div>

                <div id="resultado-cita" class="mt-6 pt-6 border-t border-line-dark text-xs empty:hidden"></div>
            </div>

            <!-- Columna 2: Acceso de Clientes / Perfil Integrado -->
            <div id="cuenta-cliente" class="bg-ink p-8 rounded-lg border border-line-dark flex flex-col justify-between">
                
                <!-- Estado 1: Formulario de Login (Visible si no hay sesión iniciada) -->
                <div id="panel-login-cliente">
                    <h3 class="font-serif text-xl font-bold text-bone mb-2">Acceso de Clientes</h3>
                    <p class="text-xs text-muted-dark mb-6 leading-relaxed">
                        ¿Creaste una contraseña al reservar? Inicia sesión para consultar tu historial y gestionar citas.
                    </p>

                    <form id="form-login-cliente" class="space-y-3">
                        <input id="login-email-cliente" type="email" required placeholder="Tu correo electrónico" 
                               class="w-full bg-ink border border-line-dark rounded-lg px-4 py-2.5 text-bone text-xs placeholder-muted-dark focus:border-gold focus:outline-none transition">
                        <input id="login-password-cliente" type="password" required placeholder="Tu contraseña" 
                               class="w-full bg-ink border border-line-dark rounded-lg px-4 py-2.5 text-bone text-xs placeholder-muted-dark focus:border-gold focus:outline-none transition">
                        <button type="submit" class="w-full py-2.5 rounded-full border border-line-dark hover:border-gold text-bone hover:text-gold text-xs font-medium uppercase tracking-wider transition">
                            Iniciar sesión
                        </button>
                    </form>
                    <p id="mensaje-login-cliente" class="mt-4 text-xs text-muted-dark"></p>
                </div>

                <!-- Estado 2: Perfil del Cliente Integrado (Reemplaza el login limpiamente) -->
                <div id="perfil-cliente" class="hidden space-y-4">
                    <div class="flex justify-between items-start pb-3 border-b border-line-dark">
                        <div>
                            <span class="text-[10px] uppercase tracking-widest text-gold block font-medium">Sesión Activa</span>
                            <h4 id="saludo-cliente" class="font-serif text-xl font-bold text-bone leading-tight">Mi Perfil</h4>
                            <div id="datos-perfil" class="text-[11px] text-muted-dark mt-0.5"></div>
                        </div>
                        <button id="cerrar-sesion-cliente" class="text-[11px] text-muted-dark hover:text-red-400 underline transition whitespace-nowrap pt-1">
                            Cerrar sesión
                        </button>
                    </div>

                    <!-- Lista compacta de citas con scroll suave -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-bone font-medium">Mis Citas</span>
                            <span id="contador-citas" class="text-muted-dark font-mono text-[10px]"></span>
                        </div>
                        <div id="citas-perfil" class="max-h-[380px] overflow-y-auto space-y-3 pr-1 no-scrollbar"></div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>
