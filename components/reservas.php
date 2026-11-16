<!-- SECCIÓN DE RESERVAS: Terminal de Agendamiento en fondo blanco hueso (bone) -->
<section id="reservar" class="scroll-mt-24 bg-bone text-ink py-24 sm:py-32 border-b border-line-light">
    <div class="max-w-2xl mx-auto px-6">
        
        <div class="text-center space-y-3 mb-12">
            <h2 class="font-serif text-3xl sm:text-5xl font-bold tracking-tight text-ink">
                Agendar tu Cita
            </h2>
            <p class="text-muted-light text-sm max-w-md mx-auto leading-relaxed">
                Elige tu barbero y servicio preferido. Recibirás tu folio de atención de inmediato para consultar o cambiar tu cita en cualquier momento.
            </p>
        </div>

        <!-- Formulario en tarjeta blanca con bordes finos -->
        <div class="bg-white p-8 sm:p-12 rounded-lg border border-line-light shadow-sm">
            
            <!-- Alerta Toast limpia y sobria -->
            <div id="mensaje-alerta" class="hidden mb-6 p-4 rounded-lg text-sm text-center"></div>

            <form id="form-cita" class="space-y-6">
                
                <!-- Datos Personales -->
                <div class="space-y-4">
                    <div>
                        <label for="nombre" class="block text-xs uppercase tracking-wider text-muted-light font-medium mb-2">Nombre completo *</label>
                        <input type="text" id="nombre" required placeholder="Tu nombre" 
                               class="w-full bg-bone border border-line-light rounded-lg px-4 py-3 text-ink placeholder-muted-light text-sm focus:border-gold focus:outline-none transition">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="telefono" class="block text-xs uppercase tracking-wider text-muted-light font-medium mb-2">Teléfono (10 dígitos) *</label>
                            <input type="tel" id="telefono" required placeholder="Ej. 5512345678" 
                                   class="w-full bg-bone border border-line-light rounded-lg px-4 py-3 text-ink placeholder-muted-light text-sm focus:border-gold focus:outline-none transition">
                        </div>
                        <div>
                            <label for="email" class="block text-xs uppercase tracking-wider text-muted-light font-medium mb-2">Correo electrónico (Opcional)</label>
                            <input type="email" id="email" placeholder="correo@ejemplo.com" 
                                   class="w-full bg-bone border border-line-light rounded-lg px-4 py-3 text-ink placeholder-muted-light text-sm focus:border-gold focus:outline-none transition">
                        </div>
                    </div>

                    <!-- Acordeón discreto para cuenta opcional -->
                    <details class="rounded-lg bg-bone border border-line-light p-4 text-xs transition">
                        <summary class="cursor-pointer text-ink font-medium select-none flex justify-between items-center">
                            <span>Crear cuenta de cliente (opcional)</span>
                            <span class="text-muted-light">+</span>
                        </summary>
                        <p class="mt-2 text-muted-light leading-relaxed">
                            Agrega una contraseña si deseas consultar tu historial y reprogramar citas futuras sin volver a escribir tus datos.
                        </p>
                        <input type="password" id="password-cliente" minlength="8" placeholder="Contraseña (mínimo 8 caracteres)" 
                               class="mt-3 w-full bg-white border border-line-light rounded-lg px-4 py-2.5 text-ink text-xs focus:border-gold focus:outline-none transition">
                    </details>
                </div>

                <!-- Selección de Servicio y Barbero -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label for="servicio_id" class="block text-xs uppercase tracking-wider text-muted-light font-medium mb-2">Servicio deseado *</label>
                        <select id="servicio_id" required 
                                class="w-full bg-bone border border-line-light rounded-lg px-4 py-3 text-ink text-sm focus:border-gold focus:outline-none transition">
                            <option value="">Cargando servicios...</option>
                        </select>
                    </div>

                    <div>
                        <label for="barbero_id" class="block text-xs uppercase tracking-wider text-muted-light font-medium mb-2">Barbero asignado *</label>
                        <select id="barbero_id" required 
                                class="w-full bg-bone border border-line-light rounded-lg px-4 py-3 text-ink text-sm focus:border-gold focus:outline-none transition">
                            <option value="">Cargando barberos...</option>
                        </select>
                    </div>
                </div>

                <!-- Selección de Fecha y Hora -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="fecha" class="block text-xs uppercase tracking-wider text-muted-light font-medium mb-2">Fecha de cita *</label>
                        <input type="date" id="fecha" required 
                               class="w-full bg-bone border border-line-light rounded-lg px-4 py-3 text-ink text-sm focus:border-gold focus:outline-none transition">
                    </div>
                    <div>
                        <label for="hora_inicio" class="block text-xs uppercase tracking-wider text-muted-light font-medium mb-2">Hora de entrada *</label>
                        <input type="time" id="hora_inicio" required 
                               class="w-full bg-bone border border-line-light rounded-lg px-4 py-3 text-ink text-sm focus:border-gold focus:outline-none transition">
                    </div>
                </div>

                <!-- Botón de Confirmación -->
                <button type="submit" 
                        class="w-full py-4 px-6 rounded-full bg-gold hover:bg-[#b8923e] text-ink font-medium uppercase tracking-wider text-xs transition duration-200 shadow-sm">
                    Confirmar reservación
                </button>
            </form>
        </div>

    </div>
</section>
