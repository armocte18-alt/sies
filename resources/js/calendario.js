import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin, { Draggable } from '@fullcalendar/interaction';
import esLocale from '@fullcalendar/core/locales/es';

const ETIQUETAS_CONVOCANTE = {
    area_central: 'Área Central',
    gerencia: 'Gerencia CDMX',
    sucursal: 'Sucursal',
    externo: 'Externo',
};

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]').content;
}

async function peticionJson(url, options = {}) {
    const response = await fetch(url, {
        ...options,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            Accept: 'application/json',
            ...(options.headers || {}),
        },
    });

    if (!response.ok) {
        const datos = await response.json().catch(() => ({}));
        const mensaje = datos.errors ? Object.values(datos.errors).flat().join('\n') : (datos.message || 'Ocurrió un error.');
        throw new Error(mensaje);
    }

    return response.json();
}

function formatoFecha(f) {
    return f ? f.split('-').reverse().join('/') : '';
}

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('calendario');

    if (!el) {
        return;
    }

    const puedeGestionar = el.dataset.puedeGestionar === '1';
    const rutaEventos = el.dataset.rutaEventos;
    const rutaEventosStore = el.dataset.rutaEventosStore;
    const rutaEventosUpdateBase = el.dataset.rutaEventosUpdateBase;
    const rutaRapidos = el.dataset.rutaRapidos;
    const rutaRapidosStore = el.dataset.rutaRapidosStore;
    const rutaRapidosUpdateBase = el.dataset.rutaRapidosUpdateBase;

    const modalEvento = document.getElementById('modal-evento');
    const formEvento = document.getElementById('form-evento');
    const modalInfo = document.getElementById('modal-info-evento');
    const modalRapido = document.getElementById('modal-evento-rapido');
    const formRapido = document.getElementById('form-evento-rapido');

    const abrirModal = (nombre) => window.dispatchEvent(new CustomEvent('open-modal', { detail: nombre }));
    const cerrarModal = (nombre) => window.dispatchEvent(new CustomEvent('close-modal', { detail: nombre }));
    const avisar = (tipo, mensaje) => window.Alpine?.store('toasts')?.push(tipo, mensaje);

    let draggableRapidos = null;

    const calendar = new Calendar(el, {
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
        locale: esLocale,
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
        },
        height: 'auto',
        editable: puedeGestionar,
        selectable: puedeGestionar,
        droppable: puedeGestionar,
        events: rutaEventos,

        // "block" en vez del "list-item" por defecto: cada evento se pinta
        // como una píldora de color completa (igual que los eventos rápidos)
        // que puede crecer a varias líneas en vez de cortarse en una sola.
        eventDisplay: 'block',

        // Por defecto FullCalendar omite los minutos en punto ("10" en vez
        // de "10:00"), lo que se confunde fácilmente con un título cortado.
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },

        eventDidMount(info) {
            // Refuerza con el título completo en el atributo nativo "title"
            // por si el texto todavía no cupiera del todo en la celda.
            const hora = info.timeText ? `${info.timeText} — ` : '';
            info.el.title = `${hora}${info.event.title}`;
        },

        dateClick(info) {
            if (puedeGestionar) {
                abrirModalNuevo(info.dateStr);
            }
        },

        eventClick(info) {
            if (puedeGestionar) {
                abrirModalEditar(info.event);
            } else {
                abrirModalInfo(info.event);
            }
        },

        eventDrop(info) {
            guardarMovimiento(info.event, info.revert);
        },

        eventResize(info) {
            guardarMovimiento(info.event, info.revert);
        },

        drop(info) {
            abrirModalNuevo(info.dateStr);
            document.getElementById('evento-nombre').value = info.draggedEl.dataset.title;
            document.getElementById('evento-color').value = info.draggedEl.dataset.color;

            if (document.getElementById('quitar-despues-soltar')?.checked) {
                eliminarEventoRapido(info.draggedEl.dataset.id, false);
            }
        },
    });

    calendar.render();

    function limpiarFormulario() {
        formEvento.reset();
        document.getElementById('evento-id').value = '';
        document.getElementById('evento-grupo-recurrencia').value = '';
        document.getElementById('evento-color').value = '#1e5b4f';
        document.getElementById('fila-horas').classList.remove('hidden');
        document.getElementById('btn-eliminar-evento').classList.add('hidden');
        document.getElementById('bloque-recurrencia').classList.add('hidden');
        document.getElementById('campo-recurrente').classList.remove('hidden');
        document.getElementById('evento-recurrente').checked = false;
        document.querySelectorAll('.dia-recurrencia').forEach((c) => { c.checked = false; });
    }

    function abrirModalNuevo(fechaStr) {
        limpiarFormulario();
        document.getElementById('titulo-modal-evento').textContent = 'Nuevo evento';
        document.getElementById('evento-fecha-inicio').value = fechaStr;
        abrirModal('modal-evento');
    }

    function abrirModalEditar(event) {
        limpiarFormulario();
        document.getElementById('titulo-modal-evento').textContent = 'Editar evento';
        document.getElementById('btn-eliminar-evento').classList.remove('hidden');

        const props = event.extendedProps;
        const fechaInicio = props.fecha_inicio_real || event.startStr.substring(0, 10);

        document.getElementById('evento-id').value = event.id;
        document.getElementById('evento-grupo-recurrencia').value = props.grupo_recurrencia || '';
        document.getElementById('campo-recurrente').classList.toggle('hidden', !!props.grupo_recurrencia);
        document.getElementById('evento-nombre').value = event.title;
        document.getElementById('evento-fecha-inicio').value = fechaInicio;
        document.getElementById('evento-fecha-fin').value = (props.fecha_fin_real && props.fecha_fin_real !== fechaInicio) ? props.fecha_fin_real : '';
        document.getElementById('evento-ubicacion').value = props.ubicacion || '';
        document.getElementById('evento-notas').value = props.notas || '';
        document.getElementById('evento-color').value = props.color || event.backgroundColor || '#1e5b4f';
        document.getElementById('evento-tipo-asociado').value = props.asociado_tipo || '';
        document.getElementById('evento-invitados').value = (props.invitados || []).join(', ');

        if (props.hora_inicio) {
            document.getElementById('evento-todo-el-dia').checked = false;
            document.getElementById('fila-horas').classList.remove('hidden');
            document.getElementById('evento-hora-inicio').value = props.hora_inicio.substring(0, 5);
            document.getElementById('evento-hora-fin').value = props.hora_fin ? props.hora_fin.substring(0, 5) : '';
        } else {
            document.getElementById('evento-todo-el-dia').checked = true;
            document.getElementById('fila-horas').classList.add('hidden');
        }

        abrirModal('modal-evento');
    }

    function abrirModalInfo(event) {
        const props = event.extendedProps;
        const fechaInicio = props.fecha_inicio_real || event.startStr.substring(0, 10);
        const fechaFin = (props.fecha_fin_real && props.fecha_fin_real !== fechaInicio) ? props.fecha_fin_real : null;

        document.getElementById('info-nombre-evento').textContent = event.title;
        document.getElementById('info-fecha').textContent = fechaFin
            ? `${formatoFecha(fechaInicio)} — ${formatoFecha(fechaFin)}`
            : formatoFecha(fechaInicio);

        document.getElementById('info-hora').textContent = props.hora_inicio
            ? `${props.hora_inicio.substring(0, 5)}${props.hora_fin ? ` - ${props.hora_fin.substring(0, 5)}` : ''}`
            : 'Todo el día';

        document.getElementById('info-ubicacion').textContent = props.ubicacion || '—';
        document.getElementById('info-convocado-por').textContent = ETIQUETAS_CONVOCANTE[props.asociado_tipo] || '—';
        document.getElementById('info-invitados').textContent = (props.invitados && props.invitados.length) ? props.invitados.join(', ') : '—';

        const notasContenedor = document.getElementById('info-notas-contenedor');
        if (props.notas && props.notas.trim() !== '') {
            notasContenedor.classList.remove('hidden');
            document.getElementById('info-notas').textContent = props.notas;
        } else {
            notasContenedor.classList.add('hidden');
        }

        abrirModal('modal-info-evento');
    }

    function guardarMovimiento(event, revertFn) {
        const props = event.extendedProps;
        const esTodoElDia = event.allDay;

        let fechaFin = null;
        if (event.end) {
            const fin = new Date(event.end);
            if (esTodoElDia) fin.setDate(fin.getDate() - 1);
            fechaFin = fin.toISOString().substring(0, 10);
        }

        const payload = {
            nombre: event.title,
            fecha_inicio: event.startStr.substring(0, 10),
            fecha_fin: fechaFin,
            hora_inicio: esTodoElDia ? null : event.startStr.substring(11, 16),
            hora_fin: esTodoElDia ? null : (event.endStr ? event.endStr.substring(11, 16) : null),
            ubicacion: props.ubicacion,
            color: props.color || event.backgroundColor,
            tipo_asociado: props.asociado_tipo,
            invitados: props.invitados,
            notas: props.notas,
        };

        peticionJson(`${rutaEventosUpdateBase}/${event.id}`, { method: 'PUT', body: JSON.stringify(payload) })
            .then(() => avisar('success', 'Evento reprogramado.'))
            .catch((err) => {
                window.alert(`No se pudo mover el evento: ${err.message}`);
                revertFn();
            });
    }

    if (puedeGestionar) {
        formEvento.addEventListener('submit', (e) => {
            e.preventDefault();

            const recurrente = document.getElementById('evento-recurrente').checked;
            const fechaFin = document.getElementById('evento-fecha-fin').value;

            if (recurrente && !fechaFin) {
                window.alert('Para eventos recurrentes, "Fecha de fin" define hasta cuándo se repite.');
                return;
            }

            const id = document.getElementById('evento-id').value;
            const todoElDia = document.getElementById('evento-todo-el-dia').checked;
            const invitados = document.getElementById('evento-invitados').value
                .split(',').map((s) => s.trim()).filter(Boolean);

            const payload = {
                nombre: document.getElementById('evento-nombre').value,
                fecha_inicio: document.getElementById('evento-fecha-inicio').value,
                fecha_fin: fechaFin || null,
                hora_inicio: todoElDia ? null : (document.getElementById('evento-hora-inicio').value || null),
                hora_fin: todoElDia ? null : (document.getElementById('evento-hora-fin').value || null),
                ubicacion: document.getElementById('evento-ubicacion').value,
                color: document.getElementById('evento-color').value,
                tipo_asociado: document.getElementById('evento-tipo-asociado').value || null,
                invitados,
                notas: document.getElementById('evento-notas').value,
                es_recurrente: !id && recurrente,
                dias_semana: Array.from(document.querySelectorAll('.dia-recurrencia:checked')).map((c) => Number(c.value)),
            };

            const url = id ? `${rutaEventosUpdateBase}/${id}` : rutaEventosStore;
            const metodo = id ? 'PUT' : 'POST';

            peticionJson(url, { method: metodo, body: JSON.stringify(payload) })
                .then((resp) => {
                    cerrarModal('modal-evento');
                    calendar.refetchEvents();
                    avisar('success', resp && resp.creados ? `Se crearon ${resp.creados} eventos de la serie.` : 'Evento guardado correctamente.');
                })
                .catch((err) => window.alert(`No se pudo guardar: ${err.message}`));
        });

        document.getElementById('evento-todo-el-dia').addEventListener('change', (e) => {
            document.getElementById('fila-horas').classList.toggle('hidden', e.target.checked);
            if (e.target.checked) {
                document.getElementById('evento-hora-inicio').value = '';
                document.getElementById('evento-hora-fin').value = '';
            }
        });

        document.getElementById('evento-recurrente').addEventListener('change', (e) => {
            document.getElementById('bloque-recurrencia').classList.toggle('hidden', !e.target.checked);
        });

        document.getElementById('btn-eliminar-evento').addEventListener('click', () => {
            const id = document.getElementById('evento-id').value;
            const grupoRecurrencia = document.getElementById('evento-grupo-recurrencia').value;
            if (!id) return;

            if (grupoRecurrencia) {
                const eliminarSerie = window.confirm('Este evento forma parte de una serie recurrente.\n\nAceptar = eliminar TODA la serie.\nCancelar = se te preguntará si quieres eliminar solo esta ocurrencia.');
                if (eliminarSerie) {
                    eliminarEventoPorUrl(`${rutaEventosUpdateBase}/${id}/serie`);
                    return;
                }
                if (!window.confirm('¿Eliminar solo esta ocurrencia?')) return;
                eliminarEventoPorUrl(`${rutaEventosUpdateBase}/${id}`);
                return;
            }

            if (window.confirm('¿Eliminar este evento? Esta acción no se puede deshacer.')) {
                eliminarEventoPorUrl(`${rutaEventosUpdateBase}/${id}`);
            }
        });

        function eliminarEventoPorUrl(url) {
            peticionJson(url, { method: 'DELETE' }).then(() => {
                cerrarModal('modal-evento');
                calendar.refetchEvents();
                avisar('success', 'Evento eliminado.');
            });
        }

        document.getElementById('btn-agregar-rapido').addEventListener('click', () => {
            formRapido.reset();
            document.getElementById('rapido-id').value = '';
            document.getElementById('rapido-color').value = '#1e5b4f';
            document.getElementById('titulo-modal-rapido').textContent = 'Nuevo evento rápido';
            document.getElementById('btn-eliminar-rapido').classList.add('hidden');
            abrirModal('modal-evento-rapido');
        });

        formRapido.addEventListener('submit', (e) => {
            e.preventDefault();
            const id = document.getElementById('rapido-id').value;
            const payload = {
                nombre: document.getElementById('rapido-nombre').value,
                color: document.getElementById('rapido-color').value,
            };
            const url = id ? `${rutaRapidosUpdateBase}/${id}` : rutaRapidosStore;
            const metodo = id ? 'PUT' : 'POST';

            peticionJson(url, { method: metodo, body: JSON.stringify(payload) }).then(() => {
                cerrarModal('modal-evento-rapido');
                cargarEventosRapidos();
                avisar('success', 'Evento rápido guardado.');
            }).catch((err) => window.alert(`No se pudo guardar: ${err.message}`));
        });

        document.getElementById('btn-eliminar-rapido').addEventListener('click', () => {
            const id = document.getElementById('rapido-id').value;
            if (!id) return;
            if (!window.confirm('¿Eliminar este evento rápido? Se quitará de la lista.')) return;
            eliminarEventoRapido(id, true);
            cerrarModal('modal-evento-rapido');
        });

        function eliminarEventoRapido(id, mostrarAviso) {
            peticionJson(`${rutaRapidosUpdateBase}/${id}`, { method: 'DELETE' }).then(() => {
                cargarEventosRapidos();
                if (mostrarAviso) avisar('success', 'Evento rápido eliminado.');
            });
        }

        function cargarEventosRapidos() {
            fetch(rutaRapidos, { headers: { Accept: 'application/json' } })
                .then((r) => r.json())
                .then((lista) => {
                    const contenedor = document.getElementById('eventos-arrastrables');
                    contenedor.innerHTML = '';

                    lista.forEach((ev) => {
                        const chip = document.createElement('div');
                        chip.className = 'evento-rapido-chip external-event';
                        chip.style.backgroundColor = ev.color;
                        chip.dataset.id = ev.id;
                        chip.dataset.title = ev.nombre;
                        chip.dataset.color = ev.color;
                        chip.innerHTML = `<span class="truncate">${ev.nombre}</span>`;

                        const btnEditar = document.createElement('button');
                        btnEditar.type = 'button';
                        btnEditar.className = 'btn-editar-rapido';
                        btnEditar.setAttribute('aria-label', 'Editar evento rápido');
                        btnEditar.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>';
                        btnEditar.addEventListener('click', (e) => {
                            e.stopPropagation();
                            document.getElementById('rapido-id').value = ev.id;
                            document.getElementById('rapido-nombre').value = ev.nombre;
                            document.getElementById('rapido-color').value = ev.color;
                            document.getElementById('titulo-modal-rapido').textContent = 'Editar evento rápido';
                            document.getElementById('btn-eliminar-rapido').classList.remove('hidden');
                            abrirModal('modal-evento-rapido');
                        });
                        chip.appendChild(btnEditar);
                        contenedor.appendChild(chip);
                    });

                    if (!draggableRapidos) {
                        draggableRapidos = new Draggable(contenedor, {
                            itemSelector: '.external-event',
                            eventData: (eventEl) => ({ title: eventEl.dataset.title, create: false }),
                        });
                    }
                });
        }

        cargarEventosRapidos();
    }
});
