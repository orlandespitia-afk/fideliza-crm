<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fideliza CRM - Control de Campaña</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style>
        .modal-backdrop {
            display: none !important;
        }

        body {
            overflow: auto !important;
            padding-right: 0 !important;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800 font-sans">

    <div class="max-w-6xl mx-auto p-4 md:p-8">

        <header class="bg-blue-600 text-white p-6 rounded-2xl shadow-md mb-6 flex flex-col md:flex-row justify-between items-center gap-4 text-center md:text-left">
            <div>
                <h1 class="text-3xl font-bold">Fideliza CRM 🗳️</h1>
                <p class="text-blue-100 mt-1 text-sm md:text-base">Control Territorial y Redes de Liderazgo</p>
            </div>

            <div class="flex items-center gap-4 bg-blue-700/50 p-3 rounded-xl border border-blue-500/30 w-full md:w-auto justify-between md:justify-end">
                <div class="text-left md:text-right">
                    <p class="text-blue-200 text-xs font-bold uppercase tracking-wider">Usuario Activo</p>
                    <p class="text-white font-bold text-base">👋 ¡Hola, {{ \Auth::user()->name }}!</p>
                    <span class="inline-block bg-white text-blue-600 text-xs font-bold px-2 py-0.5 rounded-full uppercase mt-1">
                        {{ \Auth::user()->role == 'admin' ? '👑 Administrador' : '✍️ Digitador' }}
                    </span>
                </div>

                <div class="h-10 w-px bg-blue-500/50 hidden md:block"></div>

                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="bg-gray-100 hover:bg-white text-gray-700 hover:text-red-600 font-bold py-2 px-4 rounded-xl text-sm shadow-sm transition-all flex items-center gap-2 cursor-pointer group border-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-gray-500 group-hover:text-red-600 transition-colors">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1 0 12.728 0M12 3v9" />
                        </svg>
                        Salir
                    </button>
                </form>
            </div>
        </header>

        @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 shadow-sm text-sm">
            <p class="font-bold">Por favor corrige los siguientes errores:</p>
            <ul class="list-disc pl-5 mt-1 text-xs">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-semibold mb-6 flex items-center gap-2 shadow-xs">
            ✅ {{ session('success') }}
        </div>
        @endif

        <div class="flex justify-between items-center mb-6 h-12 w-full">
            <h2 class="text-xl font-bold text-gray-700 flex items-center gap-2 select-none">
                📋 Personas Registradas
            </h2>

            <div class="flex items-center gap-3">
                @if(\Auth::user()->role == 'admin')
                <button id="btn-bulk-delete" type="button" onclick="ejecutarBorradoMasivo()" style="display: none;" class="bg-red-100 hover:bg-red-200 text-red-700 font-bold py-2 px-4 rounded-xl text-sm transition-all flex items-center gap-2 cursor-pointer border border-red-200 shadow-sm">
                    🗑️ Eliminar Seleccionados (<span id="bulk-count">0</span>)
                </button>
                @endif

                <button type="button"
                    onclick="openCreateModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl text-sm shadow transition-all flex items-center gap-2 cursor-pointer whitespace-nowrap">
                    ➕ Nuevo Votante
                </button>
            </div>
        </div>

        <input type="hidden" id="voters-data-bridge" value="{{ json_encode($voters) }}">

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                    <th class="p-3 text-left">Cédula</th>
                    <th class="p-3 text-left">Nombre</th>
                    <th class="p-3 text-left">Contacto</th>
                    <th class="p-3 text-left">Correo</th>
                    <th class="p-3 text-left">Ubicación</th>
                    <th class="px-6 py-3 text-left">F. Nacimiento</th>
                    <th class="px-6 py-3 text-left">Edad</th>
                    <th class="px-6 py-3 text-left">Líder</th>
                    <th class="p-3 text-center">Notas</th>

                    @if(\Auth::user()->role == 'admin')
                    <th class="p-3 text-center">Acciones</th>
                    <th class="p-3 text-center w-12 select-none">
                        <input type="checkbox" id="check-all" onclick="seleccionarTodo(this); event.stopPropagation();" class="rounded border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer shadow-sm w-4 h-4" title="Seleccionar todos los registros">
                    </th>
                    @endif
                </tr>
            </thead>

            <tbody class="text-sm divide-y divide-gray-100">
                @foreach($voters as $voter)
                <tr data-id="{{ $voter->id }}"
                    @if(\Auth::user()->role == 'admin') onclick="alternarSeleccionFila(this, event)" @endif
                    class="hover:bg-gray-50/50 transition-all cursor-pointer voter-row">

                    <td class="p-3 font-medium text-gray-700">{{ $voter->cedula }}</td>
                    <td class="p-3 text-gray-600">{{ $voter->full_name }}</td>
                    <td class="p-3 text-gray-600">{{ $voter->phone }}</td>
                    <td class="p-3 text-gray-600">{{ $voter->voter_email ?? '-' }}</td>
                    <td class="p-3 text-gray-600">{{ $voter->address ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $voter->fecha_nacimiento ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600 font-medium">
                        @if($voter->fecha_nacimiento)
                        {{ \DateTime::createFromFormat('Y-m-d', $voter->fecha_nacimiento) ? date_diff(date_create($voter->fecha_nacimiento), date_create('today'))->y : '-' }} años
                        @else - @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        @if($voter->leader_id == 1) Ramiro "R" @elseif($voter->leader_id == 2) Mónica Tobón @else - @endif
                    </td>

                    <td class="p-3 text-center relative">
                        <div class="inline-block relative">
                            <button type="button" data-id="{{ $voter->id }}" onclick="event.stopPropagation(); abrirNotasVotante(this)" title="{{ $voter->notas && trim($voter->notas) !== '' ? 'Ver Notas' : 'Sin Notas' }}" class="{{ $voter->notas && trim($voter->notas) !== '' ? 'text-amber-500 scale-110' : 'text-gray-300 opacity-40 hover:opacity-100' }} transition-all cursor-pointer text-base">
                                📋
                            </button>
                            @if($voter->notas && trim($voter->notas) !== '')
                            <span class="absolute -top-1 -right-1 flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            @endif
                        </div>
                    </td>

                    @if(\Auth::user()->role == 'admin')
                    <td class="p-3 text-center">
                        <div class="flex items-center justify-center gap-3">
                            <button type="button" data-id="{{ $voter->id }}" onclick="event.stopPropagation(); abrirEditarVotante(this)" class="text-blue-600 hover:text-blue-800 font-medium transition-colors cursor-pointer">
                                Editar
                            </button>

                            <form action="{{ route('voters.destroy', $voter->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar a {{ $voter->full_name }}? Esta acción no se puede deshacer.');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="event.stopPropagation();" title="Eliminar Votante" class="text-gray-400 hover:text-red-500 transition-colors cursor-pointer text-sm">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>

                    <td class="p-3 text-center">
                        <input type="checkbox"
                            value="{{ $voter->id }}"
                            onclick="event.stopPropagation(); alternarSeleccionCheckboxDirecto(this);"
                            class="voter-checkbox rounded border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer w-4 h-4 shadow-sm">
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 px-4 py-3 bg-white border border-gray-100 rounded-xl flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm select-none">

            <div class="text-xs text-gray-500 order-2 sm:order-1">
                Mostrando del <span class="font-medium text-gray-700">{{ $voters->firstItem() ?? 0 }}</span> al <span class="font-medium text-gray-700">{{ $voters->lastItem() ?? 0 }}</span> de <span class="font-medium text-gray-700">{{ $voters->total() }}</span> personas
            </div>

            <div class="order-1 sm:order-2 flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-1.5 text-xs text-gray-500">
                <label for="per_page_select" class="font-medium">Filas por página:</label>
                <select id="per_page_select" onchange="window.location.href = '?per_page=' + this.value" class="bg-transparent border-none focus:ring-0 font-semibold text-gray-700 cursor-pointer p-0 pr-6">
                    <option value="10" {{ $porPagina == 10 ? 'selected' : '' }}>10</option>
                    <option value="30" {{ $porPagina == 30 ? 'selected' : '' }}>30</option>
                    <option value="50" {{ $porPagina == 50 ? 'selected' : '' }}>50</option>
                </select>
            </div>

            <div class="order-3">
                <div class="sm:hidden">
                    {{-- Versión simple para celulares --}}
                    {!! $voters->links('pagination::simple-tailwind') !!}
                </div>
                <div class="hidden sm:block text-xs">
                    {{-- Versión numerada para computadores --}}
                    {!! $voters->links('pagination::tailwind') !!}
                </div>
            </div>

        </div>

        <div id="createModal" class="fixed inset-0 flex justify-end opacity-0 pointer-events-none invisible z-50 transition-all duration-300">

            <div id="sidePanelContent" class="bg-white h-[calc(100vh-2rem)] my-4 mr-4 max-w-sm w-full p-6 shadow-2xl rounded-2xl border border-gray-100 relative transform translate-x-full transition-all duration-300 flex flex-col justify-between overflow-y-auto pointer-events-auto">

                <div>
                    <button type="button" onclick="closeCreateModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-sm font-bold cursor-pointer transition-colors">
                        ✕
                    </button>

                    <h2 class="text-lg font-bold mb-5 text-gray-800 flex items-center gap-2">📝 Registrar Votante</h2>

                    <form id="voterForm" action="{{ route('voters.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div id="method-container"></div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Cédula <span class="text-red-500">*</span></label>
                            <input type="text" name="cedula" required placeholder="Ej. 10234567"
                                class="w-full border border-gray-200 px-3.5 py-2 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none text-sm transition-all bg-gray-50/50">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Nombre Completo <span class="text-red-500">*</span></label>
                            <input type="text" name="full_name" required placeholder="Ej. María Pérez"
                                class="w-full border border-gray-200 px-3.5 py-2 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none text-sm transition-all bg-gray-50/50">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Número de Teléfono <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" required placeholder="Ej. 3123456789"
                                class="w-full border border-gray-200 px-3.5 py-2 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none text-sm transition-all bg-gray-50/50">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Correo Electrónico <span class="text-gray-400 font-normal">(Opcional)</span></label>
                            <input type="email" name="voter_email" placeholder="Ej. correo@correo.com"
                                class="w-full border border-gray-200 px-3.5 py-2 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none text-sm transition-all bg-gray-50/50">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Ubicación / Dirección <span class="text-gray-400 font-normal">(Opcional)</span></label>
                            <input type="text" name="address" placeholder="Ej. Calle 10 #4-50 o Barrio Centro"
                                class="w-full border border-gray-200 px-3.5 py-2 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none text-sm transition-all bg-gray-50/50">
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider">Fecha de Nacimiento <span class="text-red-500">*</span></label>
                                <span id="calculatedAge" class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md hidden">0 años</span>
                            </div>
                            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required onchange="calcularEdadDinamica(this.value)"
                                class="w-full border border-gray-200 px-3.5 py-2 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none text-sm transition-all bg-gray-50/50 text-gray-600">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Líder Asignado <span class="text-gray-400 font-normal">(Opcional)</span></label>
                            <select name="leader_id" class="w-full border border-gray-200 px-3.5 py-2 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none text-sm transition-all bg-gray-50/50 text-gray-600 cursor-pointer">
                                <option value="">-- Seleccionar Líder --</option>
                                <option value="1">Ramiro "R"</option>
                                <option value="2">Mónica Tobón</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Notas / Observaciones Importantes</label>
                            <textarea name="notas" rows="3" placeholder="Apunta detalles clave sobre este votante o líder..."
                                class="w-full border border-gray-200 px-3.5 py-2 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none text-sm transition-all bg-gray-50/50 resize-none"></textarea>
                        </div>

                        <button type="submit" id="btnSubmitForm" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all text-sm cursor-pointer mt-2">
                            💾 Guardar Registro
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function abrirEditarVotante(boton) {
                const voterId = boton.getAttribute('data-id');
                if (!voterId) return;

                const bridgeInput = document.getElementById('voters-data-bridge');
                if (!bridgeInput) return;

                let todosLosVotantes = [];
                try {
                    const datosRaw = JSON.parse(bridgeInput.value);

                    // 🟢 CORREGIDO: Si viene paginado extrae .data, si no, usa el array plano directo
                    todosLosVotantes = datosRaw.data ? datosRaw.data : datosRaw;
                } catch (e) {
                    console.error("Error al procesar JSON:", e);
                    return;
                }

                // Aseguramos que sea un array válido antes de buscar
                if (!Array.isArray(todosLosVotantes)) return;

                const voter = todosLosVotantes.find(function(v) {
                    return v.id == voterId;
                });
                if (!voter) return;

                openCreateModal();

                const tituloPanel = document.querySelector('#sidePanelContent h2');
                if (tituloPanel) tituloPanel.innerHTML = '✏️ Editar Votante';

                const btnSubmit = document.getElementById('btnSubmitForm');
                if (btnSubmit) btnSubmit.innerHTML = '💾 Actualizar Cambios';

                const form = document.getElementById('voterForm');
                if (form) {
                    form.action = '/fideliza-crm/public/voters/' + voter.id;
                    const container = document.getElementById('method-container');
                    if (container) {
                        container.innerHTML = '<input type="hidden" name="_method" value="PUT">';
                    }
                }

                form.querySelector('input[name="cedula"]').value = voter.cedula || '';
                form.querySelector('input[name="full_name"]').value = voter.full_name || '';
                form.querySelector('input[name="phone"]').value = voter.phone || '';

                if (form.querySelector('input[name="voter_email"]')) {
                    form.querySelector('input[name="voter_email"]').value = voter.voter_email || '';
                }
                if (form.querySelector('input[name="address"]')) {
                    form.querySelector('input[name="address"]').value = voter.address || '';
                }

                const inputFecha = form.querySelector('input[name="fecha_nacimiento"]');
                if (inputFecha) {
                    inputFecha.value = voter.fecha_nacimiento || '';
                    calcularEdadDinamica(voter.fecha_nacimiento);
                }

                if (form.querySelector('select[name="leader_id"]')) {
                    form.querySelector('select[name="leader_id"]').value = voter.leader_id || '';
                }
                if (form.querySelector('textarea[name="notas"]')) {
                    form.querySelector('textarea[name="notas"]').value = voter.notas || '';
                }
            }

            function openCreateModal() {
                const modal = document.getElementById('createModal');
                const panelContent = document.getElementById('sidePanelContent');

                if (modal) {
                    modal.classList.remove('opacity-0', 'pointer-events-none', 'invisible');
                    modal.classList.add('opacity-100', 'pointer-events-auto');
                }
                if (panelContent) {
                    panelContent.classList.remove('translate-x-full');
                }
            }

            function closeCreateModal() {
                const modal = document.getElementById('createModal');
                const panelContent = document.getElementById('sidePanelContent');

                if (modal) {
                    modal.classList.add('opacity-0', 'pointer-events-none', 'invisible');
                    modal.classList.remove('opacity-100', 'pointer-events-auto');
                }
                if (panelContent) {
                    panelContent.classList.add('translate-x-full');
                }

                const form = document.getElementById('voterForm');
                if (form) {
                    form.reset();
                    form.action = "/fideliza-crm/public/voters";
                    const container = document.getElementById('method-container');
                    if (container) container.innerHTML = '';
                }

                const mensajeExito = document.querySelector('#sidePanelContent .bg-emerald-50');
                if (mensajeExito) {
                    mensajeExito.remove();
                }

                const tituloPanel = document.querySelector('#sidePanelContent h2');
                if (tituloPanel) tituloPanel.innerHTML = '📝 Registrar Votante';

                const btnSubmit = document.getElementById('btnSubmitForm');
                if (btnSubmit) btnSubmit.innerHTML = '💾 Guardar Registro';

                const indicadorEdad = document.getElementById('calculatedAge');
                if (indicadorEdad) indicadorEdad.classList.add('hidden');
            }

            // Recogemos el mensaje de éxito de la sesión de Laravel
            const successMessage = "{{ session('success') ?? '' }}";

            // 🟢 CORREGIDO: Solo abre el formulario automáticamente si el mensaje habla de "registrar", "crear" o "añadir"
            const esCreacion = successMessage.toLowerCase().includes('registr') ||
                successMessage.toLowerCase().includes('crear') ||
                successMessage.toLowerCase().includes('exito');

            // Pero si habla de eliminar o borrar, explícitamente NO debe abrir el modal
            const esEliminacion = successMessage.toLowerCase().includes('elimin') ||
                successMessage.toLowerCase().includes('borrar');

            if (successMessage !== '' && esCreacion && !esEliminacion) {
                document.addEventListener("DOMContentLoaded", function() {
                    openCreateModal();
                });
            }

            function calcularEdadDinamica(fechaNacimiento) {
                const indicadorEdad = document.getElementById('calculatedAge');
                if (!indicadorEdad) return;

                if (!fechaNacimiento) {
                    indicadorEdad.classList.add('hidden');
                    return;
                }

                const hoy = new Date();
                const cumpleanos = new Date(fechaNacimiento);

                let edad = hoy.getFullYear() - cumpleanos.getFullYear();
                const diferenciaMeses = hoy.getMonth() - cumpleanos.getMonth();

                if (diferenciaMeses < 0 || (diferenciaMeses === 0 && hoy.getDate() < cumpleanos.getDate())) {
                    edad--;
                }

                if (!isNaN(edad) && edad >= 0) {
                    indicadorEdad.innerText = edad + " años";
                    indicadorEdad.classList.remove('hidden');
                } else {
                    indicadorEdad.classList.add('hidden');
                }
            }

            function abrirNotasVotante(boton) {
                const globoViejo = document.getElementById('voter-notes-tooltip');
                if (globoViejo) {
                    globoViejo.remove();
                    if (boton.classList.contains('active-notes')) {
                        boton.classList.remove('active-notes');
                        return;
                    }
                }

                document.querySelectorAll('button').forEach(b => b.classList.remove('active-notes'));

                const voterId = boton.getAttribute('data-id');
                if (!voterId) return;

                const bridgeInput = document.getElementById('voters-data-bridge');
                if (!bridgeInput) return;

                let todosLosVotantes = [];
                try {
                    const datosRaw = JSON.parse(bridgeInput.value);

                    // 🟢 EL TRUCO: Si los datos vienen de la paginación de Laravel, la lista real está en '.data'. 
                    // Si no, significa que es un array plano normal. ¡Soporta ambos mundos!
                    todosLosVotantes = datosRaw.data ? datosRaw.data : datosRaw;

                } catch (e) {
                    return;
                }

                // Aseguramos que sea un array antes de usar .find() para evitar cualquier error en consola
                if (!Array.isArray(todosLosVotantes)) return;

                const voter = todosLosVotantes.find(function(v) {
                    return v.id == voterId;
                });
                if (!voter) return;

                const textoNotas = (voter.notas && voter.notas.trim() !== '') ? voter.notas : 'Sin observaciones registradas.';

                const tooltip = document.createElement('div');
                tooltip.id = 'voter-notes-tooltip';
                tooltip.className = 'absolute right-14 top-1/2 -translate-y-1/2 bg-slate-900 text-slate-100 text-xs rounded-2xl p-4 shadow-2xl z-50 w-72 md:w-96 text-left border border-slate-700 animate-fade-in font-normal leading-relaxed';

                tooltip.innerHTML = `
                <div class="flex justify-between items-center mb-2 pb-1.5 border-b border-slate-700 font-bold text-slate-300">
                    <span class="flex items-center gap-1.5">📝 Notas de ${voter.full_name}</span>
                    <span class="text-[10px] text-slate-400 font-normal bg-slate-800 px-2 py-0.5 rounded-md">Clic fuera para cerrar</span>
                </div>
                <div class="max-h-32 overflow-y-auto pr-1 text-slate-300 whitespace-pre-line text-sm">
                    ${textoNotas}
                </div>
            `;

                boton.classList.add('active-notes');
                boton.parentElement.appendChild(tooltip);
            }

            // 🟢 VIGILANTE GLOBAL: Cierra el globo de notas al hacer clic en cualquier parte de la pantalla
            document.addEventListener('click', function(evento) {
                // Buscamos si el globo existe físicamente en la pantalla en este momento
                const globoNotas = document.getElementById('voter-notes-tooltip');

                if (globoNotas) {
                    // Si el clic NO fue adentro del globo Y tampoco fue en el botón de la carpeta 📋
                    if (!globoNotas.contains(evento.target) && !evento.target.closest('button[onclick*="abrirNotasVotante"]')) {

                        globoNotas.remove(); // Destruimos el globo de inmediato

                        // Limpiamos la clase activa de todos los botones de notas para resetear el estado
                        document.querySelectorAll('button').forEach(b => b.classList.remove('active-notes'));
                    }
                }
            });


            // --- LÓGICA INTUITIVA DE EXCELENCIA PARA SELECCIÓN Y BORRADO MASIVO ---

            let filasSeleccionadasIds = [];

            // Accionador 1: Cuando se hace clic en cualquier parte de la fila
            function alternarSeleccionFila(fila, evento) {
                if (evento.target.closest('button') || evento.target.closest('form') || evento.target.closest('input[type="checkbox"]')) {
                    return;
                }

                const votanteId = fila.getAttribute('data-id');
                const checkbox = fila.querySelector('.voter-checkbox');

                if (fila.getAttribute('data-seleccionada') === 'true') {
                    fila.setAttribute('data-seleccionada', 'false');
                    fila.style.backgroundColor = '';
                    fila.style.boxShadow = ''; // 🟢 Quitamos la marca
                    if (checkbox) checkbox.checked = false;
                    filasSeleccionadasIds = filasSeleccionadasIds.filter(id => id !== votanteId);
                } else {
                    fila.setAttribute('data-seleccionada', 'true');
                    fila.style.backgroundColor = '#fef2f2'; // Fondo rosado suave
                    // 🟢 EL TRUCO: Dibuja una línea roja de 4px a la izquierda hacia adentro. ¡Mide 0px para la tabla!
                    fila.style.boxShadow = 'inset 4px 0 0 0 #ef4444';
                    if (checkbox) checkbox.checked = true;
                    filasSeleccionadasIds.push(votanteId);
                }

                actualizarBotonMasivo();
                sincronizarCheckAll();
            }

            // Accionador 2: Cuando se hace clic directamente sobre el checkbox individual
            function alternarSeleccionCheckboxDirecto(checkbox) {
                const fila = checkbox.closest('tr');
                const votanteId = fila.getAttribute('data-id');

                if (checkbox.checked) {
                    fila.setAttribute('data-seleccionada', 'true');
                    fila.style.backgroundColor = '#fef2f2';
                    fila.style.boxShadow = 'inset 4px 0 0 0 #ef4444'; // 🟢 Sombra interna roja
                    if (!filasSeleccionadasIds.includes(votanteId)) filasSeleccionadasIds.push(votanteId);
                } else {
                    fila.setAttribute('data-seleccionada', 'false');
                    fila.style.backgroundColor = '';
                    fila.style.boxShadow = '';
                    filasSeleccionadasIds = filasSeleccionadasIds.filter(id => id !== votanteId);
                }

                actualizarBotonMasivo();
                sincronizarCheckAll();
            }

            // Accionador 3: Selección masiva desde la cabecera
            function seleccionarTodo(masterCheckbox) {
                const filas = document.querySelectorAll('.voter-row');
                filasSeleccionadasIds = [];

                filas.forEach(fila => {
                    const votanteId = fila.getAttribute('data-id');
                    const cb = fila.querySelector('.voter-checkbox');

                    if (masterCheckbox.checked) {
                        fila.setAttribute('data-seleccionada', 'true');
                        fila.style.backgroundColor = '#fef2f2';
                        fila.style.boxShadow = 'inset 4px 0 0 0 #ef4444'; // 🟢 Sombra interna roja masiva
                        if (cb) cb.checked = true;
                        filasSeleccionadasIds.push(votanteId);
                    } else {
                        fila.setAttribute('data-seleccionada', 'false');
                        fila.style.backgroundColor = '';
                        fila.style.boxShadow = '';
                        if (cb) cb.checked = false;
                    }
                });

                actualizarBotonMasivo();
            }


            // Control dinámico de visibilidad del botón de arriba
            function actualizarBotonMasivo() {
                const btnBulk = document.getElementById('btn-bulk-delete');
                const countSpan = document.getElementById('bulk-count');

                if (btnBulk && countSpan) {
                    if (filasSeleccionadasIds.length > 0) {
                        countSpan.innerText = filasSeleccionadasIds.length;
                        btnBulk.style.display = 'flex';
                    } else {
                        btnBulk.style.display = 'none';
                    }
                }
            }

            // Mantiene sincronizado el checkbox "Seleccionar Todo" del título si marcas uno a uno manualmente
            function sincronizarCheckAll() {
                const checkAll = document.getElementById('check-all');
                if (!checkAll) return;

                const totalCheckboxes = document.querySelectorAll('.voter-checkbox').length;
                if (totalCheckboxes > 0 && filasSeleccionadasIds.length === totalCheckboxes) {
                    checkAll.checked = true;
                } else {
                    checkAll.checked = false;
                }
            }

            function ejecutarBorradoMasivo() {
                if (filasSeleccionadasIds.length === 0) return;

                if (confirm(`¿Estás completamente seguro de eliminar los ${filasSeleccionadasIds.length} votantes seleccionados? Esta acción limpiará los registros de forma permanente.`)) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('voters.bulkDestroy') }}";

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = "{{ csrf_token() }}";
                    form.appendChild(csrfInput);

                    filasSeleccionadasIds.forEach(id => {
                        const idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = 'ids[]';
                        idInput.value = id;
                        form.appendChild(idInput);
                    });

                    document.body.appendChild(form);
                    form.submit();
                }

            }
        </script>
</body>

</html>