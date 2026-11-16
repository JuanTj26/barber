/**
 * TECH BARBERÍA (UTN) - Frontend Interaction Logic
 * Conectado con APIs PHP: servicios, barberos, citas y autenticación.
 * Solo diseño y renderizado HTML actualizado.
 */

document.addEventListener("DOMContentLoaded", () => {
    cargarServiciosEnLanding();
    cargarBarberosEnLanding();

    // Fecha mínima de hoy en el calendario
    const now = new Date();
    const today = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().split('T')[0];
    const inputFecha = document.getElementById('fecha');
    if (inputFecha) inputFecha.min = today;

    // Listeners de Formularios
    const formCita = document.getElementById('form-cita');
    if (formCita) formCita.addEventListener('submit', enviarCita);

    const formConsulta = document.getElementById('form-consulta');
    if (formConsulta) formConsulta.addEventListener('submit', consultarCita);

    const formLoginCliente = document.getElementById('form-login-cliente');
    if (formLoginCliente) formLoginCliente.addEventListener('submit', iniciarSesionCliente);

    const btnCerrarSesion = document.getElementById('cerrar-sesion-cliente');
    if (btnCerrarSesion) btnCerrarSesion.addEventListener('click', cerrarSesionCliente);

    const citasPerfilBox = document.getElementById('citas-perfil');
    if (citasPerfilBox) {
        citasPerfilBox.addEventListener('click', gestionarCitaPerfil);
        citasPerfilBox.addEventListener('submit', reprogramarDesdePerfil);
    }

    // Menú móvil hamburguesa y panel lateral
    const btnMenuMovil = document.getElementById('btn-menu-movil');
    const panelMenuMovil = document.getElementById('panel-menu-movil');
    const btnCerrarMenuMovil = document.getElementById('btn-cerrar-menu-movil');
    const enlacesMovil = document.querySelectorAll('.enlace-movil');

    if (btnMenuMovil && panelMenuMovil) {
        const toggleMenu = (abrir) => {
            if (abrir) {
                panelMenuMovil.classList.remove('opacity-0', 'pointer-events-none');
                panelMenuMovil.classList.add('opacity-100');
                btnMenuMovil.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            } else {
                panelMenuMovil.classList.add('opacity-0', 'pointer-events-none');
                panelMenuMovil.classList.remove('opacity-100');
                btnMenuMovil.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
        };

        btnMenuMovil.addEventListener('click', () => toggleMenu(true));
        if (btnCerrarMenuMovil) btnCerrarMenuMovil.addEventListener('click', () => toggleMenu(false));
        enlacesMovil.forEach(a => a.addEventListener('click', () => toggleMenu(false)));
    }

    // Comprobar sesión de cliente activa
    fetch('api/auth.php?action=status')
        .then(r => r.json())
        .then(s => { if (s && s.cliente) cargarPerfilCliente(); })
        .catch(() => { });
});

// 1. Cargar Servicios: Lista estilo Menú con líneas finas (border-b)
async function cargarServiciosEnLanding() {
    try {
        const res = await fetch('api/servicios.php');
        const servicios = await res.json();

        const grid = document.getElementById('grid-servicios');
        const select = document.getElementById('servicio_id');

        if (!grid || !select) return;

        grid.innerHTML = '';
        select.innerHTML = '<option value="">Selecciona un servicio</option>';

        if (!servicios.length) {
            grid.innerHTML = '<p class="text-muted-light text-sm py-4">Aún no se han configurado servicios en la base de datos.</p>';
            return;
        }

        servicios.forEach((s) => {
            grid.innerHTML += `
                <div class="py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-line-light group transition-colors">
                    <div class="space-y-1">
                        <div class="flex items-center gap-3">
                            <h4 class="font-serif text-xl sm:text-2xl font-bold text-ink">${s.nombre}</h4>
                            <span class="text-xs text-muted-light">· ${s.duracion_minutos} min</span>
                        </div>
                        <p class="text-xs sm:text-sm text-muted-light font-normal max-w-lg leading-relaxed">
                            ${s.descripcion || 'Servicio especializado con navaja clásica y visagismo.'}
                        </p>
                    </div>
                    <div class="flex items-center gap-6 self-start sm:self-center">
                        <span class="text-xl sm:text-2xl font-serif font-bold text-gold">$${s.precio} <span class="text-xs font-sans text-muted-light font-normal">MXN</span></span>
                        <button onclick="seleccionarServicioEnFormulario('${s.id}')" 
                                class="px-5 py-2 rounded-full border border-line-light hover:border-gold hover:text-gold text-ink text-xs font-medium uppercase tracking-wider transition">
                            Seleccionar
                        </button>
                    </div>
                </div>
            `;

            select.innerHTML += `<option value="${s.id}">${s.nombre} — $${s.precio} MXN (${s.duracion_minutos} min)</option>`;
        });
    } catch (error) {
        console.error("Error al cargar servicios:", error);
        const grid = document.getElementById('grid-servicios');
        if (grid) grid.innerHTML = '<p class="text-red-600 text-xs py-4">Error al consultar el catálogo de servicios.</p>';
    }
}

function seleccionarServicioEnFormulario(servicioId) {
    const select = document.getElementById('servicio_id');
    if (select) select.value = servicioId;
    const formSection = document.getElementById('reservar');
    if (formSection) formSection.scrollIntoView({ behavior: 'smooth' });
}

// 2. Cargar Barberos: Carrusel Dinámico con Tarjetas y Desplazamiento Suave
async function cargarBarberosEnLanding() {
    const select = document.getElementById('barbero_id');
    const grid = document.getElementById('grid-barberos');
    try {
        let barberos = [];
        try {
            const r = await fetch('api/barberos.php');
            if (r.ok) {
                barberos = await r.json();
            }
        } catch (err) {
            console.warn("API de barberos offline, usando roster por defecto:", err);
        }

        if (select) select.innerHTML = '<option value="">Selecciona un barbero</option>';
        if (grid) grid.innerHTML = '';

        // Si la base de datos está vacía o se prueba en estático, garantizar un equipo maestro visible
        if (!Array.isArray(barberos) || !barberos.length) {
            barberos = [
                { id: '1', nombre: 'Mateo Rivera', especialidad: 'Master Visagista & Tijera Clásica' },
                { id: '2', nombre: 'Carlos Méndez', especialidad: 'Especialista en Fade & Navaja' },
                { id: '3', nombre: 'David Soto', especialidad: 'Ritual Clásico de Barba & Toalla' },
                { id: '4', nombre: 'Alejandro Peña', especialidad: 'Estilismo Urbano & Perfilado' }
            ];
        }

        const fotosBarberos = [
            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=800&q=80'
        ];

        barberos.forEach((b, idx) => {
            if (select) {
                select.insertAdjacentHTML('beforeend', `<option value="${b.id}">${b.nombre}${b.especialidad ? ' — ' + b.especialidad : ''}</option>`);
            }

            if (grid) {
                const foto = fotosBarberos[idx % fotosBarberos.length];
                grid.innerHTML += `
                    <div class="w-[85vw] sm:w-[calc(50%-16px)] md:w-[calc(33.333%-22px)] flex-shrink-0 snap-start space-y-4 group">
                        <div class="w-full aspect-[4/5] overflow-hidden rounded-lg bg-ink border border-line-dark shadow-md">
                            <img src="${foto}" alt="${b.nombre}" class="w-full h-full object-cover saturate-[1.15] contrast-[1.05] group-hover:scale-105 transition duration-500">
                        </div>
                        <div>
                            <h4 class="font-serif text-2xl font-bold text-bone">${b.nombre}</h4>
                            <p class="text-xs uppercase tracking-wider text-muted-dark mt-1">${b.especialidad || 'Especialista en Corte & Barba'}</p>
                        </div>
                        <button onclick="seleccionarBarberoEnFormulario('${b.id}')" 
                                class="w-full py-2.5 rounded-full border border-line-dark hover:border-gold text-bone hover:text-gold text-xs uppercase tracking-wider transition font-medium">
                            Agendar con ${b.nombre.split(' ')[0]}
                        </button>
                    </div>
                `;
            }
        });

        // Inicializar controles del carrusel después de renderizar
        iniciarControlesCarruselBarberos();

    } catch (e) {
        console.error("Error al inicializar carrusel de barberos:", e);
        if (select) select.innerHTML = '<option value="">No se pudieron cargar los barberos</option>';
    }
}

function iniciarControlesCarruselBarberos() {
    const carrusel = document.getElementById('grid-barberos');
    const btnPrev = document.getElementById('btn-barberos-prev');
    const btnNext = document.getElementById('btn-barberos-next');
    if (!carrusel) return;

    if (btnPrev) {
        btnPrev.onclick = () => {
            const cardWidth = carrusel.firstElementChild ? carrusel.firstElementChild.offsetWidth + 32 : 360;
            carrusel.scrollBy({ left: -cardWidth, behavior: 'smooth' });
        };
    }
    if (btnNext) {
        btnNext.onclick = () => {
            const cardWidth = carrusel.firstElementChild ? carrusel.firstElementChild.offsetWidth + 32 : 360;
            carrusel.scrollBy({ left: cardWidth, behavior: 'smooth' });
        };
    }

    // Arrastre con mouse (drag to scroll)
    let isDown = false;
    let startX;
    let scrollLeft;

    carrusel.addEventListener('mousedown', (e) => {
        isDown = true;
        startX = e.pageX - carrusel.offsetLeft;
        scrollLeft = carrusel.scrollLeft;
    });
    carrusel.addEventListener('mouseleave', () => { isDown = false; });
    carrusel.addEventListener('mouseup', () => { isDown = false; });
    carrusel.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - carrusel.offsetLeft;
        const walk = (x - startX) * 1.5;
        carrusel.scrollLeft = scrollLeft - walk;
    });
}

function seleccionarBarberoEnFormulario(barberoId) {
    const select = document.getElementById('barbero_id');
    if (select) select.value = barberoId;
    const formSection = document.getElementById('reservar');
    if (formSection) formSection.scrollIntoView({ behavior: 'smooth' });
}

// 3. Enviar Formulario de Cita con Toast sobrio
async function enviarCita(e) {
    e.preventDefault();
    const alerta = document.getElementById('mensaje-alerta');

    const datos = {
        nombre: document.getElementById('nombre').value,
        telefono: document.getElementById('telefono').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password-cliente').value,
        servicio_id: document.getElementById('servicio_id').value,
        barbero_id: document.getElementById('barbero_id').value,
        fecha: document.getElementById('fecha').value,
        hora_inicio: document.getElementById('hora_inicio').value
    };

    const password = document.getElementById('password-cliente').value;
    if (password && password.length < 8) {
        mostrarAlerta('La contraseña debe contener al menos 8 caracteres.', false);
        return;
    }

    // Validar domingo (0 = domingo)
    const fechaObj = new Date(datos.fecha + 'T12:00:00');
    if (fechaObj.getDay() === 0) {
        mostrarAlerta('Los domingos la barbería permanece cerrada. Por favor elige de lunes a sábado.', false);
        return;
    }

    // Validar horario comercial (10:00 a 19:30)
    if (datos.hora_inicio < '10:00' || datos.hora_inicio > '19:30') {
        mostrarAlerta('El horario de atención es de 10:00 a 20:00. Selecciona una hora entre 10:00 y 19:30.', false);
        return;
    }

    try {
        const res = await fetch('api/citas.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });
        const resultado = await res.json();

        alerta.classList.remove('hidden');
        if (resultado.success) {
            alerta.className = "mb-6 p-5 rounded-lg text-sm bg-stone-50 border border-line-light text-ink text-center space-y-2";
            const emailInfo = resultado.email_notification?.sent
                ? '<br><span class="text-xs text-muted-light">Notificación enviada a tu correo.</span>'
                : datos.email ? '<br><span class="text-xs text-muted-light">Guarda tu folio para consultar tu cita.</span>' : '';

            alerta.innerHTML = `
                <div class="font-medium text-ink">${resultado.message}</div>
                <div class="inline-block px-4 py-1.5 rounded-full bg-bone border border-line-light font-mono text-sm font-bold text-gold tracking-wider mt-1">
                    FOLIO: ${resultado.folio}
                </div>
                <div>${emailInfo}</div>
            `;
            document.getElementById('form-cita').reset();
            alerta.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            alerta.className = "mb-6 p-4 rounded-lg text-sm bg-red-50 text-red-900 border border-red-200 text-center";
            alerta.innerHTML = resultado.message;
        }
    } catch (error) {
        console.error("Error al enviar cita:", error);
        alerta.classList.remove('hidden');
        alerta.className = "mb-6 p-4 rounded-lg text-sm bg-red-50 text-red-900 border border-red-200 text-center";
        alerta.textContent = "Error de enlace con el servidor. Verifica que MySQL esté activo.";
    }
}

function mostrarAlerta(texto, ok) {
    const a = document.getElementById('mensaje-alerta');
    if (!a) return;
    a.classList.remove('hidden');
    a.className = "mb-6 p-4 rounded-lg text-sm text-center border " + (ok ? "bg-stone-50 text-ink border-line-light" : "bg-red-50 text-red-900 border-red-200");
    a.textContent = texto;
}

function textoNotificacionCorreo(result) {
    if (result?.sent) return ' Se envió aviso por correo.';
    if (result?.reason === 'no_email') return ' La cita no tiene correo.';
    return '';
}

// 4. Iniciar Sesión de Cliente
async function iniciarSesionCliente(e) {
    e.preventDefault();
    const out = document.getElementById('mensaje-login-cliente');
    out.textContent = "Validando credenciales...";
    try {
        const r = await fetch('api/auth.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'customer_login',
                email: document.getElementById('login-email-cliente').value,
                password: document.getElementById('login-password-cliente').value
            })
        });
        const d = await r.json();
        if (!d.success) throw new Error(d.message);

        document.getElementById('nombre').value = d.cliente.nombre;
        document.getElementById('telefono').value = d.cliente.telefono;
        document.getElementById('email').value = d.cliente.email;

        out.className = "mt-4 text-xs text-bone";
        out.textContent = `Sesión iniciada con éxito. Bienvenido, ${d.cliente.nombre}.`;
        cargarPerfilCliente();
    } catch (err) {
        out.className = "mt-4 text-xs text-red-400";
        out.textContent = err.message;
    }
}

// Función global para alternar acordeón de reprogramar en tarjeta de perfil
window.toggleFormularioReprogramar = function (folio) {
    const form = document.getElementById(`reprogramar-${folio}`);
    const flecha = document.getElementById(`flecha-${folio}`);
    if (!form) return;
    const estaOculto = form.classList.contains('hidden');
    form.classList.toggle('hidden');
    if (flecha) {
        flecha.textContent = estaOculto ? '▴' : '▾';
    }
};

// 5. Cargar Perfil de Cliente Integrado con tarjetas compactas y acordeón
async function cargarPerfilCliente() {
    const panelLogin = document.getElementById('panel-login-cliente');
    const panelPerfil = document.getElementById('perfil-cliente');

    const r = await fetch('api/clientes.php?perfil=1');
    const d = await r.json();

    if (!d.success) {
        if (panelLogin) panelLogin.classList.remove('hidden');
        if (panelPerfil) panelPerfil.classList.add('hidden');
        return;
    }

    // Ocultar formulario de login y mostrar perfil integrado en la misma columna
    if (panelLogin) panelLogin.classList.add('hidden');
    if (panelPerfil) panelPerfil.classList.remove('hidden');

    const saludoElem = document.getElementById('saludo-cliente');
    if (saludoElem) {
        const primerNombre = d.cliente.nombre ? d.cliente.nombre.split(' ')[0] : 'Cliente';
        saludoElem.textContent = `Hola, ${primerNombre}`;
    }

    const perfilElem = document.getElementById('datos-perfil');
    if (perfilElem) {
        perfilElem.textContent = `Tel. ${d.cliente.telefono}${d.cliente.email ? ' · ' + d.cliente.email : ''}`;
    }

    const contadorElem = document.getElementById('contador-citas');
    if (contadorElem) {
        contadorElem.textContent = `${d.citas.length} ${d.citas.length === 1 ? 'cita' : 'citas'}`;
    }

    const box = document.getElementById('citas-perfil');
    if (!box) return;

    if (!d.citas.length) {
        box.innerHTML = '<p class="text-muted-dark text-xs py-6 text-center border border-line-dark rounded-lg">Aún no tienes citas registradas.</p>';
        return;
    }

    box.innerHTML = d.citas.map(c => {
        const badgeColor = {
            confirmada: 'bg-emerald-950/60 text-emerald-300 border-emerald-800',
            pendiente: 'bg-amber-950/60 text-amber-300 border-amber-800',
            cancelada: 'bg-red-950/60 text-red-300 border-red-800',
            completada: 'bg-zinc-800 text-zinc-300 border-zinc-700'
        }[c.estado] || 'bg-zinc-800 text-zinc-300 border-zinc-700';

        const puedeGestionar = !['cancelada', 'completada'].includes(c.estado);

        return `
            <article class="rounded-lg border border-line-dark bg-[#121212] p-4 transition-all">
                <div class="flex justify-between items-start gap-2">
                    <div>
                        <div class="text-sm font-serif font-bold text-bone">${c.servicio_name}</div>
                        <div class="text-[11px] text-gold font-medium mt-0.5">$${c.precio} MXN · <span class="text-muted-dark font-normal">${c.barbero_nombre || 'Barbero asignado'}</span></div>
                        <div class="text-[11px] text-muted-dark mt-1 flex items-center gap-1.5 font-sans">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-gold"></span>
                            ${c.fecha} · ${c.hora_inicio.slice(0, 5)} hs
                        </div>
                    </div>
                    <div class="text-right flex flex-col items-end gap-1">
                        <span class="inline-block text-[9px] uppercase tracking-wider px-2 py-0.5 rounded-full border ${badgeColor}">
                            ${c.estado}
                        </span>
                        <span class="text-[10px] font-mono text-muted-dark">
                            #${c.folio}
                        </span>
                    </div>
                </div>

                ${puedeGestionar ? `
                    <div class="mt-3 pt-2.5 border-t border-line-dark/60">
                        <button type="button" onclick="toggleFormularioReprogramar('${c.folio}')" 
                                class="text-[11px] text-gold hover:text-amber-300 transition flex items-center justify-between w-full font-medium py-0.5">
                            <span>Reprogramar o cancelar cita</span>
                            <span id="flecha-${c.folio}" class="text-xs transition-transform duration-200">▾</span>
                        </button>

                        <form id="reprogramar-${c.folio}" class="form-perfil-cita hidden pt-3 mt-2 border-t border-line-dark/40 space-y-2.5" data-folio="${c.folio}">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-[9px] uppercase tracking-wider text-muted-dark block mb-1">Nueva Fecha</label>
                                    <input type="date" name="fecha" required min="${new Date().toLocaleDateString('en-CA')}" class="w-full bg-ink border border-line-dark rounded p-1.5 text-xs text-bone focus:border-gold focus:outline-none">
                                </div>
                                <div>
                                    <label class="text-[9px] uppercase tracking-wider text-muted-dark block mb-1">Nueva Hora</label>
                                    <input type="time" name="hora_inicio" required min="10:00" max="19:30" class="w-full bg-ink border border-line-dark rounded p-1.5 text-xs text-bone focus:border-gold focus:outline-none">
                                </div>
                            </div>
                            <button type="submit" class="w-full bg-gold hover:bg-[#b8923e] text-ink rounded-full py-1.5 text-[11px] font-medium uppercase tracking-wider transition">
                                Confirmar Cambio
                            </button>
                            <button type="button" data-cancelar="${c.folio}" class="w-full text-[11px] text-red-400 hover:text-red-300 py-1 transition text-center underline">
                                Cancelar Cita
                            </button>
                        </form>
                    </div>
                ` : ''}
            </article>
        `;
    }).join('');
}

// 6. Reprogramar desde el Perfil
async function reprogramarDesdePerfil(e) {
    if (!e.target.matches('.form-perfil-cita')) return;
    e.preventDefault();
    const f = e.target;
    const r = await fetch('api/citas.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'customer_update',
            folio: f.dataset.folio,
            fecha: f.elements.fecha.value,
            hora_inicio: f.elements.hora_inicio.value
        })
    });
    const d = await r.json();
    alert(d.message + textoNotificacionCorreo(d.email_notification));
    if (d.success) cargarPerfilCliente();
}

// 7. Cancelar desde el Perfil
async function gestionarCitaPerfil(e) {
    const folio = e.target.dataset.cancelar;
    if (!folio) return;
    if (!confirm(`¿Confirmas la cancelación de la cita con folio ${folio}?`)) return;
    const r = await fetch('api/citas.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'customer_cancel', folio })
    });
    const d = await r.json();
    alert(d.message + textoNotificacionCorreo(d.email_notification));
    if (d.success) cargarPerfilCliente();
}

// 8. Cerrar Sesión del Cliente
async function cerrarSesionCliente() {
    await fetch('api/auth.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'customer_logout' })
    });
    const panelLogin = document.getElementById('panel-login-cliente');
    const panelPerfil = document.getElementById('perfil-cliente');
    if (panelPerfil) panelPerfil.classList.add('hidden');
    if (panelLogin) panelLogin.classList.remove('hidden');
    const loginMsg = document.getElementById('mensaje-login-cliente');
    if (loginMsg) {
        loginMsg.textContent = 'Sesión cerrada exitosamente.';
        setTimeout(() => { loginMsg.textContent = ''; }, 4000);
    }
}

// 9. Consulta de Cita por Folio
async function consultarCita(e) {
    e.preventDefault();
    const folio = document.getElementById('folio-consulta').value.trim();
    const panel = document.getElementById('resultado-cita');
    if (!panel) return;

    panel.classList.remove('empty:hidden');
    panel.innerHTML = '<span class="text-gold">Consultando cita...</span>';

    try {
        const r = await fetch(`api/citas.php?folio=${encodeURIComponent(folio)}`);
        const d = await r.json();

        if (!d.success) {
            panel.innerHTML = `<span class="text-red-400">${d.message}</span>`;
            return;
        }

        const c = d.cita;
        const badgeColor = {
            confirmada: 'bg-emerald-950/60 text-emerald-300 border-emerald-800',
            pendiente: 'bg-amber-950/60 text-amber-300 border-amber-800',
            cancelada: 'bg-red-950/60 text-red-300 border-red-800',
            completada: 'bg-zinc-800 text-zinc-300 border-zinc-700'
        }[c.estado] || 'bg-zinc-800 text-zinc-300 border-zinc-700';

        panel.innerHTML = `
            <div class="space-y-3 bg-ink p-5 rounded-lg border border-line-dark">
                <div class="flex justify-between items-center">
                    <span class="text-gold font-mono font-bold text-xs">FOLIO: ${c.folio}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] uppercase tracking-wider border ${badgeColor}">${c.estado}</span>
                </div>
                <div class="font-serif text-lg font-bold text-bone">${c.servicio_name}</div>
                <div class="text-muted-dark text-xs">${c.fecha} · ${c.hora_inicio.slice(0, 5)} — ${c.hora_fin.slice(0, 5)}</div>
                <div class="text-muted-dark text-xs">Barbero: ${c.barbero_nombre || 'Sin asignar'}</div>
            </div>
        `;

        if (!['cancelada', 'completada'].includes(c.estado)) {
            panel.innerHTML += `
                <form id="form-reprogramar" class="mt-4 space-y-3">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-[10px] uppercase text-muted-dark block mb-1">Nueva Fecha</label>
                            <input type="date" id="nueva-fecha" required min="${new Date().toLocaleDateString('en-CA')}" class="w-full bg-ink border border-line-dark p-2 rounded-lg text-xs text-bone focus:border-gold focus:outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] uppercase text-muted-dark block mb-1">Nueva Hora</label>
                            <input type="time" id="nueva-hora" required class="w-full bg-ink border border-line-dark p-2 rounded-lg text-xs text-bone focus:border-gold focus:outline-none">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-gold hover:bg-[#b8923e] text-ink font-medium uppercase text-xs py-2.5 rounded-full transition">
                        Actualizar Horario
                    </button>
                </form>
                <button id="cancelar-cita" class="mt-2 text-xs text-red-400 hover:text-red-300 underline block text-center w-full">
                    Cancelar esta reservación
                </button>
            `;
        }

        const form = document.getElementById('form-reprogramar');
        if (form) {
            form.addEventListener('submit', async ev => {
                ev.preventDefault();
                const response = await fetch('api/citas.php', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'customer_update',
                        folio,
                        fecha: document.getElementById('nueva-fecha').value,
                        hora_inicio: document.getElementById('nueva-hora').value
                    })
                });
                const result = await response.json();
                alert(result.message + textoNotificacionCorreo(result.email_notification));
                if (result.success) document.getElementById('form-consulta').requestSubmit();
            });
        }

        const cancel = document.getElementById('cancelar-cita');
        if (cancel) {
            cancel.addEventListener('click', async () => {
                if (!confirm('¿Seguro que deseas cancelar esta cita?')) return;
                const response = await fetch('api/citas.php', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'customer_cancel', folio })
                });
                const result = await response.json();
                alert(result.message + textoNotificacionCorreo(result.email_notification));
                if (result.success) document.getElementById('form-consulta').requestSubmit();
            });
        }
    } catch {
        panel.innerHTML = '<span class="text-red-400">No se pudo consultar la cita.</span>';
    }
}
