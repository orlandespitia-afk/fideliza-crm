<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fideliza CRM - Control de Campaña</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

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

            <div class="flex items-center gap-4 bg-blue-700/50 p-3 rounded-xl border border-blue-500/30 w-full md:w-auto md:ml-auto justify-between md:justify-end text-left md:text-right">

                @if(in_array(\Auth::user()->role, ['super_admin']))
                <a href="{{ route('users.index') }}" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-xs px-3 py-2 rounded-lg transition-all duration-200 shadow-md uppercase tracking-wider whitespace-nowrap">
                    ⚙️ Panel de Usuarios
                </a>
                @endif

                <div>
                    <p class="text-blue-200 text-[10px] font-bold uppercase tracking-wider">Usuario Activo</p>
                    <p class="text-white font-bold text-sm whitespace-nowrap">👋 ¡Hola, {{ \Auth::user()->name }}!</p>

                    @if(\Auth::user()->role == 'admin')
                    <span class="inline-block bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase mt-0.5">
                        👑 Administrador
                    </span>
                    @elseif(\Auth::user()->role == 'super_admin')
                    <span class="inline-block bg-amber-500 text-slate-900 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase mt-0.5">
                        ⚡ Líder Admin
                    </span>
                    @else
                    <span class="inline-block bg-white text-blue-600 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase mt-0.5">
                        ✍️ Digitador
                    </span>
                    @endif
                </div>
            </div>

            {{-- 🚪 BOTÓN DE SALIR (Queda inmediatamente al lado de la caja de usuario) --}}
            <form action="{{ route('logout') }}" method="POST" class="m-0 flex items-center">
                @csrf
                <button type="submit" class="bg-gray-100 hover:bg-white text-gray-700 hover:text-red-600 font-bold py-2 px-4 rounded-xl text-sm shadow-sm transition-all flex items-center gap-2 cursor-pointer border-0 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-gray-500 transition-colors">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1 0 12.728 0M12 3v9" />
                    </svg>
                    Salir
                </button>
            </form>
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

        <div x-data="{ vistaActual: 'personal' }" class="w-full">

            <div class="flex justify-between items-center mb-6 h-12 w-full">
                <h2 class="text-xl font-bold text-gray-700 flex items-center gap-2 select-none">
                    📋 Personas Registradas
                </h2>

                <div class="flex items-center gap-3">

                    <!-- NUEVO: Selector Global de Vista (Pestañas) -->
                    <div class="flex bg-gray-100 p-1 rounded-xl shadow-inner border border-gray-200">
                        <button type="button" @click="vistaActual = 'personal'" :class="{ 'bg-white text-blue-600 shadow-sm': vistaActual === 'personal', 'text-gray-500 hover:text-gray-700': vistaActual !== 'personal' }" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all">
                            👤 Personal
                        </button>
                        <button type="button" @click="vistaActual = 'geo'" :class="{ 'bg-white text-blue-600 shadow-sm': vistaActual === 'geo', 'text-gray-500 hover:text-gray-700': vistaActual !== 'geo' }" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all">
                            📍 Geografía
                        </button>
                        <button type="button" @click="vistaActual = 'laboral'" :class="{ 'bg-white text-blue-600 shadow-sm': vistaActual === 'laboral', 'text-gray-500 hover:text-gray-700': vistaActual !== 'laboral' }" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all">
                            💼 Laboral
                        </button>
                    </div>

                    <!-- Botón Eliminar Seleccionados (código original) -->
                    <button id="btn-bulk-delete" type="button" onclick="ejecutarBorradoMasivo()"
                        class="opacity-0 max-w-0 px-0 ml-0 overflow-hidden pointer-events-none transition-all duration-300 ease-in-out bg-red-100 hover:bg-red-200 text-red-700 font-bold py-2 rounded-xl text-sm flex items-center gap-2 cursor-pointer border border-red-200 shadow-sm whitespace-nowrap">
                        🗑️ Eliminar (<span id="bulk-count">0</span>)
                    </button>

                    @if(\Auth::user()->role === 'super_admin')
                    <button id="btn-bulk-reassign" type="button" onclick="abrirModalReasignacion(event)"
                        class="opacity-0 max-w-0 px-0 ml-0 overflow-hidden pointer-events-none transition-all duration-300 ease-in-out bg-purple-100 hover:bg-purple-200 text-purple-700 font-bold py-2 rounded-xl text-sm flex items-center gap-2 cursor-pointer border border-purple-200 shadow-sm whitespace-nowrap">
                        🔄 Reasignar (<span id="bulk-reassign-count">0</span>)
                    </button>
                    @endif

                    <!-- Botón Nuevo Votante (código original) -->
                    <button type="button" onclick="
                    openCreateModal(); 
                    document.querySelector('#sidePanelContent h2').innerHTML = '➕ Registrar Nuevo Votante';
                    document.getElementById('btnSubmitForm').innerHTML = '💾 Guardar Registro'; 
                    document.getElementById('voterForm').reset(); 
                    document.getElementById('voterForm').action = '/fideliza-crm/public/voters'; 
                    document.getElementById('method-container').innerHTML = '';
                    document.getElementById('calculatedAge').innerText = '0 años';
                    if(document.getElementById('dob_year')) document.getElementById('dob_year').value = '';
                    if(document.getElementById('dob_mes')) document.getElementById('dob_mes').value = '';
                    if(document.getElementById('dob_day')) document.getElementById('dob_day').value = '';
                    window.dispatchEvent(new CustomEvent('set-modo-create'));
                " class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl text-sm shadow transition-all flex items-center gap-2 cursor-pointer whitespace-nowrap">
                        ➕ Nuevo Votante
                    </button>
                </div>
            </div>

            <input type="hidden" id="voters-data-bridge" value="{{ json_encode($voters->items()) }}">

            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                        <th class="p-3 text-left w-32">Cédula</th>
                        <th class="p-3 text-left">Nombre Completo</th>

                        <th x-show="vistaActual === 'personal'" class="p-3 text-left">Teléfono</th>
                        <th x-show="vistaActual === 'personal'" class="p-3 text-left">Email</th>
                        <th x-show="vistaActual === 'personal'" class="px-6 py-3 text-left">F.Nacimiento</th>
                        <th x-show="vistaActual === 'personal'" class="px-6 py-3 text-left">Edad</th>

                        @if(\Auth::user()->role === 'lider_admin')
                        <th x-show="vistaActual === 'personal'" class="px-6 py-3 text-left">Líder</th>
                        @endif

                        <th x-show="vistaActual === 'geo'" x-cloak class="p-3 text-left text-blue-600">Ciudad/Municipio</th>
                        <th x-show="vistaActual === 'geo'" x-cloak class="p-3 text-left text-blue-600">Barrio/Vereda</th>
                        <th x-show="vistaActual === 'geo'" x-cloak class="p-3 text-left text-blue-600">Dirección</th>

                        <th x-show="vistaActual === 'laboral'" x-cloak class="p-3 text-left text-amber-600">Nivel Educativo</th>
                        <th x-show="vistaActual === 'laboral'" x-cloak class="p-3 text-left text-amber-600">Profesión</th>
                        <th x-show="vistaActual === 'laboral'" x-cloak class="p-3 text-left text-amber-600">Lugar Trabajo</th>

                        <th class="p-3 text-center">Notas</th>

                        @if(in_array(\Auth::user()->role, ['super_admin', 'admin']))
                        <th class="p-3 text-center">Acciones</th>
                        <th class="p-3 text-center">Sel.</th>
                        @endif
                    </tr>
                </thead>

                <tbody class="text-sm divide-y divide-gray-100">
                    @foreach($voters as $voter)
                    <tr data-id="{{ $voter->id }}"
                        @if(in_array(\Auth::user()->role, ['super_admin', 'admin'])) onclick="alternarSeleccionFila(this, event)" @endif
                        class="hover:bg-gray-50/50 transition-all cursor-pointer voter-row">

                        <td class="p-3 font-medium text-gray-700">
                            <div class="flex items-center gap-2">
                                @if($voter->status === 'active')
                                <span title="Activo" class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-200 animate-pulse"></span>
                                @else
                                <span title="Inactivo" class="w-2.5 h-2.5 rounded-full bg-red-500 shadow-sm shadow-red-200"></span>
                                @endif
                                {{ $voter->cedula }}
                            </div>
                        </td>
                        <td class="p-3">
                            <span class="text-gray-700 font-medium block">{{ $voter->full_name }}</span>

                            @if(\Auth::user()->role === 'super_admin')
                            <span class="text-[10px] text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded-md inline-block mt-1 font-normal">
                                ✍️ Por:
                                @if($voter->creator)
                                {{ $voter->creator->name }}
                                @else
                                <span class="text-red-500 font-bold">Eliminado ({{ $voter->creador_original_nombre ?? 'Desconocido' }})</span>
                                @endif
                            </span>

                            @elseif(\Auth::user()->role === 'admin')
                            @if($voter->creator && $voter->creator->role === 'super_admin')
                            <span class="text-[10px] text-gray-400 bg-gray-50 px-1.5 py-0.5 rounded-md inline-block mt-1 font-normal">
                                ✍️ Por: Control Central
                            </span>
                            @else
                            <span class="text-[10px] text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded-md inline-block mt-1 font-normal">
                                ✍️ Por:
                                @if($voter->creator)
                                {{ $voter->creator->name }}
                                @else
                                <span class="text-red-500 font-bold">Eliminado ({{ $voter->creador_original_nombre ?? 'Desconocido' }})</span>
                                @endif
                            </span>
                            @endif
                            @endif
                        </td>

                        <td x-show="vistaActual === 'personal'" class="p-3 text-gray-600">{{ $voter->phone }}</td>
                        <td x-show="vistaActual === 'personal'" class="p-3 text-gray-600">{{ $voter->voter_email ?? '-' }}</td>
                        <td x-show="vistaActual === 'personal'" class="px-6 py-4 text-gray-600">
                            {{ $voter->birth_date ? $voter->birth_date->format('d/m/Y') : '-' }}
                        </td>
                        <td x-show="vistaActual === 'personal'" class="px-6 py-4 text-gray-600 font-medium">
                            {{ $voter->birth_date ? $voter->birth_date->age . ' años' : '-' }}
                        </td>
                        @if(\Auth::user()->role === 'lider_admin')
                        <td x-show="vistaActual === 'personal'" class="px-6 py-4 text-gray-600 font-semibold text-blue-700">
                            {{ $voter->leader ? $voter->leader->name : '-' }}
                        </td>
                        @endif

                        <td x-show="vistaActual === 'geo'" x-cloak class="p-3 text-gray-600">{{ $voter->city ?? '-' }}</td>
                        <td x-show="vistaActual === 'geo'" x-cloak class="p-3 text-gray-600">{{ $voter->neighborhood ?? '-' }}</td>
                        <td x-show="vistaActual === 'geo'" x-cloak class="p-3 text-gray-600">{{ $voter->address ?? '-' }}</td>

                        <td x-show="vistaActual === 'laboral'" x-cloak class="p-3 text-gray-600">{{ $voter->education_level ?? '-' }}</td>
                        <td x-show="vistaActual === 'laboral'" x-cloak class="p-3 text-gray-600">{{ $voter->profession ?? '-' }}</td>
                        <td x-show="vistaActual === 'laboral'" x-cloak class="p-3 text-gray-600">{{ $voter->workplace ?? '-' }}</td>

                        <td class="p-3 text-center">
                            @if(!empty($voter->notes))
                            <button onclick="abrirNotasVotante(this, event)" data-id="{{ $voter->id }}" class="relative bg-gray-100 hover:bg-emerald-50 text-gray-700 hover:text-emerald-600 p-2 rounded-xl text-xs font-semibold">📝</button>
                            @else
                            <button disabled class="bg-gray-50 text-gray-300 p-2 rounded-xl text-xs cursor-not-allowed">🚫</button>
                            @endif
                        </td>

                        @if(in_array(\Auth::user()->role, ['super_admin', 'admin',]))
                        <td class="p-3 text-center">
                            <div class="flex items-center justify-center gap-3">
                                <button type="button" data-id="{{ $voter->id }}" onclick="event.stopPropagation(); abrirEditarVotante(this)" class="text-blue-600 hover:text-blue-800 font-medium">Editar</button>
                                <form action="{{ route('voters.destroy', $voter->id) }}" method="POST" onsubmit="return confirm('¿Eliminar a {{ $voter->full_name }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="event.stopPropagation();" title="Eliminar" class="text-gray-400 hover:text-red-500 text-sm">🗑️</button>
                                </form>
                            </div>
                        </td>
                        <td class="p-3 text-center">
                            <input type="checkbox" value="{{ $voter->id }}" onclick="event.stopPropagation(); alternarSeleccionCheckboxDirecto(this);" class="voter-checkbox rounded border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer w-4 h-4">
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
                        {!! $voters->links('pagination::simple-tailwind') !!}
                    </div>
                    <div class="hidden sm:block text-xs">
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

                        <form id="voterForm" action="{{ route('voters.store') }}" method="POST" class="space-y-4 flex flex-col h-full" onkeydown="return event.key != 'Enter';">
                            @csrf
                            <div id="method-container"></div>

                            <div x-data="{ tab: 'identidad', paso: 1, modo: 'create' }"
                                id="wizardContainer"
                                @set-modo-edit.window="modo = 'edit'; paso = 1; tab = 'identidad'"
                                @set-modo-create.window="modo = 'create'; paso = 1; tab = 'identidad'"
                                class="flex flex-col flex-grow space-y-4">

                                <div x-show="modo === 'edit'" class="flex border-b border-gray-200 text-xs font-bold uppercase tracking-wider" x-cloak>
                                    <button type="button" @click="tab = 'identidad'; paso = 1" :class="tab === 'identidad' ? 'border-blue-600 text-blue-600 border-b-2' : 'border-transparent text-gray-400 hover:text-gray-600'" class="flex-1 pb-2 text-center font-bold uppercase transition-all focus:outline-none cursor-pointer">👤 Identidad</button>
                                    <button type="button" @click="tab = 'territorial'; paso = 2" :class="tab === 'territorial' ? 'border-blue-600 text-blue-600 border-b-2' : 'border-transparent text-gray-400 hover:text-gray-600'" class="flex-1 pb-2 text-center font-bold uppercase transition-all focus:outline-none cursor-pointer">📍 Territorial</button>
                                    <button type="button" @click="tab = 'laboral'; paso = 3" :class="tab === 'laboral' ? 'border-blue-600 text-blue-600 border-b-2' : 'border-transparent text-gray-400 hover:text-gray-600'" class="flex-1 pb-2 text-center font-bold uppercase transition-all focus:outline-none cursor-pointer">💼 Laboral</button>
                                </div>

                                <div class="mt-2 flex-grow">

                                    <div x-show="modo === 'edit' ? (tab === 'identidad') : (paso === 1)" class="space-y-3">
                                        <p class="text-[10px] text-amber-600 font-bold bg-amber-50/60 px-2.5 py-1 rounded-lg flex items-center gap-1 select-none">
                                            🔒 Datos Personales Protegidos
                                        </p>

                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Cédula <span class="text-red-500">*</span></label>
                                                <input type="text" name="cedula" id="field_cedula" required placeholder="Ej. 10234567" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full border border-gray-200 px-3.5 py-2 rounded-xl text-sm bg-gray-50/50 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Teléfono <span class="text-red-500">*</span></label>
                                                <input type="text" name="phone" id="field_phone" required placeholder="Ej. 3123456789" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full border border-gray-200 px-3.5 py-2 rounded-xl text-sm bg-gray-50/50 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Nombre Completo <span class="text-red-500">*</span></label>
                                            <input type="text" name="full_name" id="field_fullname" required placeholder="Ej. María Pérez" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')" class="w-full border border-gray-200 px-3.5 py-2 rounded-xl text-sm bg-gray-50/50 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Correo Electrónico <span class="text-gray-400 font-normal">(Opcional)</span></label>
                                            <input type="email" name="voter_email" placeholder="Ej. correo@correo.com" class="w-full border border-gray-200 px-3.5 py-2 rounded-xl text-sm bg-gray-50/50">
                                        </div>

                                        <div>
                                            <div class="flex justify-between items-center mb-1">
                                                <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider">Fecha de Nacimiento <span class="text-red-500">*</span></label>
                                                <span id="calculatedAge" class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">0 años</span>
                                            </div>
                                            <input type="hidden" name="fecha_nacimiento" id="fecha_nacimiento" required>
                                            <div class="grid grid-cols-3 gap-2">
                                                <select id="dob_day" onchange="sincronizarFechaSelects()" class="border border-gray-200 px-2 py-2 rounded-xl text-sm bg-gray-50/50 text-gray-600 font-medium">
                                                    <option value="">Día</option>
                                                    @for ($i = 1; $i <= 31; $i++) <option value="{{ sprintf('%02d', $i) }}">{{ $i }}</option> @endfor
                                                </select>
                                                <select id="dob_mes" onchange="sincronizarFechaSelects()" class="border border-gray-200 px-2 py-2 rounded-xl text-sm bg-gray-50/50 text-gray-600 font-medium">
                                                    <option value="">Mes</option>
                                                    <option value="01">Enero</option>
                                                    <option value="02">Febrero</option>
                                                    <option value="03">Marzo</option>
                                                    <option value="04">Abril</option>
                                                    <option value="05">Mayo</option>
                                                    <option value="06">Junio</option>
                                                    <option value="07">Julio</option>
                                                    <option value="08">Agosto</option>
                                                    <option value="09">Septiembre</option>
                                                    <option value="10">Octubre</option>
                                                    <option value="11">Noviembre</option>
                                                    <option value="12">Diciembre</option>
                                                </select>
                                                <select id="dob_year" onchange="sincronizarFechaSelects()" class="border border-gray-200 px-2 py-2 rounded-xl text-sm bg-gray-50/50 text-gray-600 font-medium">
                                                    <option value="">Año</option>
                                                    @for ($i = date('Y'); $i >= date('Y') - 100; $i--) <option value="{{ $i }}">{{ $i }}</option> @endfor
                                                </select>
                                            </div>
                                        </div>

                                        <div x-show="modo === 'create'" class="pt-4">
                                            <button type="button"
                                                @click="
                                                    const c = document.getElementById('field_cedula').value.trim();
                                                    const t = document.getElementById('field_phone').value.trim();
                                                    const n = document.getElementById('field_fullname').value.trim();
                                                    const f = document.getElementById('fecha_nacimiento').value;

                                                    if (!c || !t || !n || !f) {
                                                        alert('⚠️ Faltan datos obligatorios:\nPor favor rellena Cédula, Teléfono, Nombre Completo y la Fecha de Nacimiento (Día, Mes y Año).');
                                                        return;
                                                    }

                                                    const btn = $event.currentTarget;
                                                    const textoOriginal = btn.innerHTML;
                                                    btn.innerHTML = '⏳ Verificando...';
                                                    btn.disabled = true;
                                                    btn.classList.add('opacity-75', 'cursor-wait');

                                                    fetch(`/fideliza-crm/public/voters/check-cedula?cedula=${c}`)
                                                        .then(res => res.json())
                                                        .then(data => {
                                                            if (data.exists) {
                                                                alert('⚠️ ¡ALTO! Esta cédula ya se encuentra registrada en el sistema.\n\nPor favor verifica el número o busca a la persona en la tabla principal.');
                                                                btn.innerHTML = textoOriginal;
                                                                btn.disabled = false;
                                                                btn.classList.remove('opacity-75', 'cursor-wait');
                                                            } else {
                                                                btn.innerHTML = textoOriginal;
                                                                btn.disabled = false;
                                                                btn.classList.remove('opacity-75', 'cursor-wait');
                                                                paso = 2; 
                                                                tab = 'territorial';
                                                            }
                                                        })
                                                        .catch(err => {
                                                            console.error('Error verificando cédula:', err);
                                                            btn.innerHTML = textoOriginal;
                                                            btn.disabled = false;
                                                            btn.classList.remove('opacity-75', 'cursor-wait');
                                                            paso = 2; 
                                                            tab = 'territorial';
                                                        });
                                                "
                                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl text-sm shadow-sm text-center cursor-pointer">
                                                Siguiente Paso ➡️
                                            </button>
                                        </div>
                                    </div>

                                    <div x-show="modo === 'edit' ? (tab === 'territorial') : (paso === 2)" x-cloak class="space-y-3">
                                        <div class="space-y-3 pt-3 border-t border-gray-100 mt-3">
                                            <p class="text-[10px] font-bold uppercase text-blue-600 tracking-wider">📍 Ubicación Geográfica</p>

                                            <div>
                                                <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Ciudad / Municipio</label>
                                                <input list="ciudades_lista" type="text" name="city" id="voter_city" placeholder="Ej. Cali (Valle del Cauca)" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s\(\)]/g, '')" class="w-full border border-gray-200 px-3.5 py-2 rounded-xl text-sm bg-gray-50/50 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                                <datalist id="ciudades_lista">
                                                    @isset($municipios)
                                                    @foreach($municipios as $mun)
                                                    <option value="{{ $mun->nombre }} ({{ $mun->departamento }})"></option>
                                                    @endforeach
                                                    @endisset
                                                </datalist>
                                            </div>

                                            <div>
                                                <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Barrio / Vereda</label>
                                                <input type="text" name="neighborhood" id="voter_neighborhood" placeholder="Ej. Los Álamos" class="w-full border border-gray-200 px-3.5 py-2 rounded-xl text-sm bg-gray-50/50">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Dirección Residencial <span class="text-gray-400 font-normal">(Opcional)</span></label>
                                            <input type="text" name="address" id="voter_address" placeholder="Ej. Calle 10 #4-50" class="w-full border border-gray-200 px-3.5 py-2 rounded-xl text-sm bg-gray-50/50">
                                        </div>

                                        <div x-show="modo === 'create'" class="pt-4 grid grid-cols-2 gap-3">
                                            <button type="button" @click="paso = 1; tab = 'identidad'" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2.5 px-4 rounded-xl text-sm text-center cursor-pointer transition-all">
                                                ⬅️ Regresar
                                            </button>

                                            <button type="button"
                                                @click="
                                                    const ciudad = document.getElementById('voter_city').value;
                                                    const barrio = document.getElementById('voter_neighborhood').value.trim();
                                                    
                                                    if (!ciudad || !barrio) {
                                                        alert('⚠️ Faltan datos territoriales:\nPor favor selecciona la Ciudad / Municipio y escribe el Barrio / Vereda.');
                                                    } else {
                                                        paso = 3; tab = 'laboral';
                                                    }
                                                "
                                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl text-sm text-center shadow-sm cursor-pointer transition-all">
                                                Siguiente ➡️
                                            </button>
                                        </div>
                                    </div>

                                    <div x-show="modo === 'edit' ? (tab === 'laboral') : (paso === 3)" x-cloak class="space-y-3">
                                        <div class="space-y-3 pt-3 border-t border-gray-100 mt-3">
                                            <p class="text-[10px] font-bold uppercase text-amber-600 tracking-wider">💼 Perfil Ocupacional</p>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Nivel de Estudio</label>
                                                    <select name="education_level" id="voter_education_level" class="w-full border border-gray-200 px-3.5 py-2 rounded-xl text-sm bg-gray-50/50 text-gray-600">
                                                        <option value="">Seleccione...</option>
                                                        <option value="Primaria">Primaria</option>
                                                        <option value="Secundaria">Bachiller</option>
                                                        <option value="Técnico">Técnico</option>
                                                        <option value="Tecnólogo">Tecnólogo</option>
                                                        <option value="Profesional">Profesional</option>
                                                        <option value="Postgrado">Postgrado</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Profesión u Oficio</label>
                                                    <input type="text" name="profession" id="voter_profession" placeholder="Ej. Contador, Mecánico" class="w-full border border-gray-200 px-3.5 py-2 rounded-xl text-sm bg-gray-50/50">
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Lugar de Trabajo</label>
                                                    <input type="text" name="workplace" id="voter_workplace" placeholder="Ej. Almacén Central" class="w-full border border-gray-200 px-3.5 py-2 rounded-xl text-sm bg-gray-50/50">
                                                </div>
                                                <div>
                                                    <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Fecha de Ingreso</label>
                                                    <input type="date" name="hire_date" id="voter_hire_date" class="w-full border border-gray-200 px-3.5 py-2 rounded-xl text-sm bg-gray-50/50 text-gray-600">
                                                </div>
                                            </div>

                                            @if(\Auth::user()->role === 'digitador')
                                            <div class="pt-2">
                                                <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Líder Responsable <span class="text-red-500">*</span></label>
                                                <select name="leader_id" required class="w-full border border-gray-200 px-3.5 py-2 rounded-xl text-sm bg-gray-50/50 text-gray-600">
                                                    <option value="">-- Selecciona el Líder --</option>
                                                    @if(isset($posiblesLideres))
                                                    @foreach($posiblesLideres as $lider) <option value="{{ $lider->id }}">{{ $lider->name }}</option> @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            @else
                                            <input type="hidden" name="leader_id" value="{{ \Auth::user()->id }}">
                                            @endif

                                            <div class="pt-2">
                                                <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Estado en Campaña</label>
                                                <div class="flex gap-4 mt-1 select-none">
                                                    <label class="flex items-center gap-1.5 text-xs font-semibold text-gray-600 cursor-pointer"><input type="radio" name="status" value="active" checked class="w-4 h-4"> 🟢 Activo</label>
                                                    <label class="flex items-center gap-1.5 text-xs font-semibold text-gray-600 cursor-pointer"><input type="radio" name="status" value="inactive" class="w-4 h-4"> 🔴 Inactivo</label>
                                                </div>
                                            </div>

                                            <div class="pt-2">
                                                <label class="block text-[11px] font-bold uppercase text-gray-400 tracking-wider mb-1">Notas / Observaciones</label>
                                                <textarea name="notes" id="voter_notes" rows="2" placeholder="Información adicional relevante..." class="w-full border border-gray-200 px-3.5 py-2 rounded-xl text-sm bg-gray-50/50 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"></textarea>
                                            </div>
                                        </div>

                                        <div x-show="modo === 'create'" class="pt-4 grid grid-cols-2 gap-3 mt-2 border-t border-gray-200 pb-4">
                                            <button type="button" @click="paso = 2; tab = 'territorial'" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-3 px-4 rounded-xl text-sm shadow transition-all flex items-center justify-center gap-2 cursor-pointer">
                                                ⬅️ Regresar al Paso 2
                                            </button>

                                            <button type="button" id="btnSubmitForm"
                                                @click="
                                                    sincronizarFechaSelects();
                                                    const form = document.getElementById('voterForm');
                                                    
                                                    const estudio = document.getElementById('voter_education_level')?.value?.trim() || '';
                                                    const profesion = document.getElementById('voter_profession')?.value?.trim() || '';
                                                    const lugar = document.getElementById('voter_workplace')?.value?.trim() || '';
                                                    const liderInput = form.querySelector('[name=\'leader_id\']');
                                                    const liderVal = liderInput ? liderInput.value : '';

                                                    if (!estudio || !profesion || !lugar || !liderVal) {
                                                        alert('⚠️ Información Incompleta en el Paso 3:\nPor favor completa el Nivel de Estudio, la Profesión, el Lugar de Trabajo y el Líder asignado antes de guardar.');
                                                        return;
                                                    }

                                                    if (!form.reportValidity()) return;

                                                    const btn = $event.currentTarget;
                                                    btn.disabled = true;
                                                    btn.innerHTML = '⏳ Guardando...';
                                                    btn.classList.add('opacity-75', 'cursor-wait');

                                                    form.submit();
                                                "
                                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl text-sm shadow transition-all flex items-center justify-center gap-2 cursor-pointer">
                                                💾 Guardar Nuevo
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div x-show="modo === 'edit'"
                                    class="mt-0 pt-2 border-t border-gray-200 w-full"
                                    x-cloak>

                                    <button type="button"
                                        @click="
                                                sincronizarFechaSelects();
                                                const form = document.getElementById('voterForm');
                                                
                                                if (!form.reportValidity()) return;

                                                const btn = $event.currentTarget;
                                                btn.disabled = true;
                                                btn.innerHTML = '⏳ Actualizando...';
                                                btn.classList.add('opacity-75', 'cursor-wait');

                                                form.submit();
                                            "
                                        class="w-full px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer text-sm">
                                        💾 Actualizar Cambios
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
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
                    todosLosVotantes = JSON.parse(bridgeInput.value);
                } catch (e) {
                    console.error("Error al procesar JSON:", e);
                    return;
                }

                if (!Array.isArray(todosLosVotantes)) return;

                const voter = todosLosVotantes.find(function(v) {
                    return v.id == voterId;
                });
                if (!voter) return;

                openCreateModal();

                window.dispatchEvent(new CustomEvent('set-modo-edit'));
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

                // 🧠 Sincronización Inteligente de la Fecha de Nacimiento al Editar (birth_date)
                const inputFecha = form.querySelector('input[name="fecha_nacimiento"]');
                if (inputFecha && voter.birth_date) {

                    // 🌟 FIX: Nos aseguramos de limpiar la fecha si trae 'T' o si viene limpia tipo "1990-05-24"
                    const fechaLimpia = voter.birth_date.includes('T') ?
                        voter.birth_date.split('T')[0] :
                        voter.birth_date;

                    const partesFecha = fechaLimpia.split('-');

                    if (partesFecha.length === 3) {
                        const anio = partesFecha[0];
                        const mes = partesFecha[1];
                        const dia = partesFecha[2];

                        // Asignamos el valor al input oculto
                        inputFecha.value = `${anio}-${mes}-${dia}`;

                        // Seleccionamos los desplegables visuales automáticamente
                        if (document.getElementById('dob_year')) document.getElementById('dob_year').value = anio;
                        if (document.getElementById('dob_mes')) document.getElementById('dob_mes').value = mes;
                        if (document.getElementById('dob_day')) document.getElementById('dob_day').value = dia;

                        // Ejecutamos el cálculo de la edad para que pinte los años reales
                        calcularEdadDinamica(inputFecha.value);
                    }
                } else if (inputFecha) {
                    inputFecha.value = '';
                    if (document.getElementById('dob_year')) document.getElementById('dob_year').value = '';
                    if (document.getElementById('dob_mes')) document.getElementById('dob_mes').value = '';
                    if (document.getElementById('dob_day')) document.getElementById('dob_day').value = '';
                    document.getElementById('calculatedAge').innerText = '0 años';
                }
                // Sincronizar los campos nuevos al editar
                if (form.querySelector('[name="city"]')) form.querySelector('[name="city"]').value = voter.city || '';
                if (form.querySelector('[name="neighborhood"]')) form.querySelector('[name="neighborhood"]').value = voter.neighborhood || '';
                if (form.querySelector('[name="education_level"]')) form.querySelector('[name="education_level"]').value = voter.education_level || '';
                if (form.querySelector('[name="profession"]')) form.querySelector('[name="profession"]').value = voter.profession || '';
                if (form.querySelector('[name="workplace"]')) form.querySelector('[name="workplace"]').value = voter.workplace || '';
                if (form.querySelector('[name="hire_date"]')) form.querySelector('[name="hire_date"]').value = voter.hire_date || '';
                if (form.querySelector('[name="status"]')) {
                    const radioStatus = form.querySelectorAll('[name="status"]');
                    radioStatus.forEach(radio => {
                        radio.checked = (radio.value === (voter.status || 'active'));
                    });
                }
                if (form.querySelector('textarea[name="notes"]')) {
                    form.querySelector('textarea[name="notes"]').value = voter.notes || '';
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

            const successMessage = "{{ session('success') ?? '' }}";

            // Identificamos explícitamente qué es una creación
            const esCreacion = (successMessage.toLowerCase().includes('registr') ||
                    successMessage.toLowerCase().includes('crear')) &&
                !successMessage.toLowerCase().includes('transferi'); // 👈 AGREGAMOS ESTO

            const esEliminacion = successMessage.toLowerCase().includes('elimin') ||
                successMessage.toLowerCase().includes('borrar');

            // Ahora, el modal solo se abrirá si es creación Y NO es una transferencia
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

            function abrirNotasVotante(boton, evento) {
                if (evento) evento.stopPropagation();

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
                    todosLosVotantes = JSON.parse(bridgeInput.value);
                } catch (e) {
                    return;
                }

                const voter = todosLosVotantes.find(v => v.id == voterId);
                if (!voter) return;

                const textoNotas = (voter.notes && voter.notes.trim() !== '') ? voter.notes : 'Sin observaciones registradas.';

                const tooltip = document.createElement('div');
                tooltip.id = 'voter-notes-tooltip';
                tooltip.className = 'absolute bg-slate-900 text-slate-100 text-xs rounded-xl p-4 shadow-2xl z-50 w-72 md:w-96 text-left border border-slate-700 font-normal leading-relaxed';

                // Aquí agregamos el botón de Borrar (Rojo) y el de Cerrar (Gris)
                tooltip.innerHTML = `
        <div style="width: 320px; max-width: 90vw; position: relative;">
        <div class="flex justify-between items-center mb-2 pb-1.5 border-b border-slate-700 font-bold text-slate-300">
            <span class="flex items-center gap-1.5">📝 Notas de ${voter.full_name}</span>
            <div class="flex gap-2">
                <button type="button" onclick="borrarNotaVotante(${voter.id}, this)" class="text-[10px] text-red-400 hover:text-white font-normal bg-red-950 hover:bg-red-800 px-2 py-0.5 rounded-md cursor-pointer transition-colors border border-red-900">🗑️ Borrar</button>
                <button type="button" onclick="cerrarGloboNotas()" class="text-[10px] text-slate-400 hover:text-white font-normal bg-slate-800 hover:bg-slate-700 px-2 py-0.5 rounded-md cursor-pointer transition-colors">✖️ Cerrar</button>
            </div>
        </div>
        
        <div style="word-break: break-all; overflow-wrap: break-word; white-space: pre-wrap; max-height: 250px; overflow-y: auto;" class="text-slate-300 text-sm">${textoNotas}</div>
    </div>
`;

                boton.classList.add('active-notes');
                document.body.appendChild(tooltip);
                // Listener para cerrar al hacer scroll en la tabla
                const contenedorTabla = document.querySelector('.table-responsive'); // Ajusta este selector según tu HTML
                if (contenedorTabla) {
                    contenedorTabla.addEventListener('scroll', cerrarGloboNotas, {
                        once: true
                    });
                }

                // Matemáticas para posicionarlo perfecto (ANCLADO AL DOCUMENTO)
                const rect = boton.getBoundingClientRect();

                // AGREGAMOS window.scrollY y window.scrollX para que el tooltip sepa 
                // dónde está el botón REALMENTE, incluso si hiciste scroll.
                let topPos = rect.top + window.scrollY - (tooltip.offsetHeight / 2) + (rect.height / 2);
                let leftPos = rect.left + window.scrollX - tooltip.offsetWidth - 15;

                // Ajustes de límites para que no se salga de la pantalla
                if (leftPos < 10) leftPos = rect.right + window.scrollX + 15;
                if (topPos < 10) topPos = 10 + window.scrollY; // También ajustamos el tope al scroll

                // Ajuste del límite inferior
                if ((topPos + tooltip.offsetHeight) > (window.innerHeight + window.scrollY)) {
                    topPos = window.innerHeight + window.scrollY - tooltip.offsetHeight - 10;
                }

                tooltip.style.top = `${topPos}px`;
                tooltip.style.left = `${leftPos}px`;
            }

            // Cierra el globo únicamente cuando se hace clic en "Cerrar"
            function cerrarGloboNotas() {
                const globoViejo = document.getElementById('voter-notes-tooltip');
                if (globoViejo) globoViejo.remove();
                document.querySelectorAll('button.active-notes').forEach(b => b.classList.remove('active-notes'));
            }

            // Nueva función: Borra la nota en tiempo real
            async function borrarNotaVotante(voterId, botonBorrar) {
                if (!confirm('¿Estás seguro de que deseas borrar esta nota de forma permanente?')) {
                    return;
                }

                botonBorrar.innerHTML = '⏳...';
                botonBorrar.disabled = true;

                // Obtenemos el token de seguridad de Laravel desde algún formulario de tu página
                const csrfToken = document.querySelector('input[name="_token"]').value;

                try {
                    const response = await fetch(`/fideliza-crm/public/voters/${voterId}/clear-note`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    if (response.ok) {
                        // 1. Cerramos el globo
                        cerrarGloboNotas();

                        // 2. Apagamos el botón en la tabla visualmente (lo volvemos gris y con el icono 🚫)
                        const botonTabla = document.querySelector(`button[data-id="${voterId}"][onclick*="abrirNotasVotante"]`);
                        if (botonTabla) {
                            botonTabla.outerHTML = '<button disabled class="bg-gray-50 text-gray-300 p-2 rounded-xl text-xs cursor-not-allowed inline-flex items-center justify-center" title="Sin observaciones para mostrar">🚫</button>';
                        }

                        // 3. Actualizamos los datos ocultos para que al editar ya salga vacío
                        const bridgeInput = document.getElementById('voters-data-bridge');
                        let votantes = JSON.parse(bridgeInput.value);
                        const index = votantes.findIndex(v => v.id == voterId);
                        if (index > -1) {
                            votantes[index].notes = null;
                            bridgeInput.value = JSON.stringify(votantes);
                        }

                    } else {
                        alert('Hubo un error al intentar borrar la nota.');
                        botonBorrar.innerHTML = '🗑️ Borrar';
                        botonBorrar.disabled = false;
                    }
                } catch (error) {
                    console.error(error);
                    alert('Error de conexión.');
                    botonBorrar.innerHTML = '🗑️ Borrar';
                    botonBorrar.disabled = false;
                }
            }

            let filasSeleccionadasIds = [];

            function alternarSeleccionFila(fila, evento) {
                if (evento.target.closest('button') || evento.target.closest('form') || evento.target.closest('input[type="checkbox"]')) {
                    return;
                }

                const votanteId = fila.getAttribute('data-id');
                const checkbox = fila.querySelector('.voter-checkbox');

                if (fila.getAttribute('data-seleccionada') === 'true') {
                    fila.setAttribute('data-seleccionada', 'false');
                    fila.style.backgroundColor = '';
                    fila.style.boxShadow = '';
                    if (checkbox) checkbox.checked = false;
                    filasSeleccionadasIds = filasSeleccionadasIds.filter(id => id !== votanteId);
                } else {
                    fila.setAttribute('data-seleccionada', 'true');
                    fila.style.backgroundColor = '#fef2f2';
                    fila.style.boxShadow = 'inset 4px 0 0 0 #ef4444';
                    if (checkbox) checkbox.checked = true;
                    filasSeleccionadasIds.push(votanteId);
                }

                actualizarBotonMasivo();
                sincronizarCheckAll();
            }

            function alternarSeleccionCheckboxDirecto(checkbox) {
                const fila = checkbox.closest('tr');
                const votanteId = fila.getAttribute('data-id');

                if (checkbox.checked) {
                    fila.setAttribute('data-seleccionada', 'true');
                    fila.style.backgroundColor = '#fef2f2';
                    fila.style.boxShadow = 'inset 4px 0 0 0 #ef4444';
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

            function seleccionarTodo(masterCheckbox) {
                const filas = document.querySelectorAll('.voter-row');
                filasSeleccionadasIds = [];

                filas.forEach(fila => {
                    const votanteId = fila.getAttribute('data-id');
                    const cb = fila.querySelector('.voter-checkbox');

                    if (masterCheckbox.checked) {
                        fila.setAttribute('data-seleccionada', 'true');
                        fila.style.backgroundColor = '#fef2f2';
                        fila.style.boxShadow = 'inset 4px 0 0 0 #ef4444';
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

            function actualizarBotonMasivo() {
                const conteo = filasSeleccionadasIds.length;

                // Controles Eliminar
                const btnBorrar = document.getElementById('btn-bulk-delete');
                const countSpan = document.getElementById('bulk-count');

                // Controles Reasignar
                const btnReasignar = document.getElementById('btn-bulk-reassign');
                const reassignCountSpan = document.getElementById('bulk-reassign-count');

                if (conteo > 0) {
                    if (btnBorrar) {
                        countSpan.innerText = conteo;
                        btnBorrar.classList.remove('max-w-0', 'px-0', 'ml-0', 'opacity-0', 'pointer-events-none');
                        btnBorrar.classList.add('max-w-xs', 'px-4', 'ml-2', 'opacity-100', 'pointer-events-auto');
                    }
                    if (btnReasignar) {
                        reassignCountSpan.innerText = conteo;
                        btnReasignar.classList.remove('max-w-0', 'px-0', 'ml-0', 'opacity-0', 'pointer-events-none');
                        btnReasignar.classList.add('max-w-xs', 'px-4', 'ml-2', 'opacity-100', 'pointer-events-auto');
                    }
                } else {
                    if (btnBorrar) {
                        btnBorrar.classList.remove('max-w-xs', 'px-4', 'ml-2', 'opacity-100', 'pointer-events-auto');
                        btnBorrar.classList.add('max-w-0', 'px-0', 'ml-0', 'opacity-0', 'pointer-events-none');
                    }
                    if (btnReasignar) {
                        btnReasignar.classList.remove('max-w-xs', 'px-4', 'ml-2', 'opacity-100', 'pointer-events-auto');
                        btnReasignar.classList.add('max-w-0', 'px-0', 'ml-0', 'opacity-0', 'pointer-events-none');
                    }
                }
            }

            function abrirModalReasignacion() {
                // 1. Creamos un modal desde cero que no pertenece a ningún form existente
                const modal = document.createElement('div');
                modal.id = 'modal-dinamico';
                modal.className = 'fixed inset-0 z-[9999] bg-black/60 flex items-center justify-center p-4';

                modal.innerHTML = `
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full">
        <div class="p-4 border-b border-gray-100 bg-purple-600 rounded-t-2xl text-white">
            <h3 class="text-lg font-bold">🔄 Transferir Votantes</h3>
        </div>
        <div class="p-5">
            <p class="text-sm text-gray-600 mb-3">Has seleccionado ${filasSeleccionadasIds.length} registros. ¿A qué usuario activo deseas transferirlos?</p>
            <select id="new_user_select" class="w-full border border-gray-200 px-3.5 py-2 rounded-xl text-sm bg-gray-50 text-gray-700">
                <option value="" disabled selected>-- Selecciona el nuevo responsable --</option>
                @foreach(\App\Models\User::all() as $usuario)
                    <option value="{{ $usuario->id }}">{{ $usuario->name }} ({{ $usuario->role }})</option>
                @endforeach
            </select>
        </div>
        <div class="p-4 border-t border-gray-100 text-right bg-gray-50 rounded-b-2xl">
            <button onclick="document.getElementById('modal-dinamico').remove()" class="bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-xl text-sm mr-2">Cancelar</button>
            <button id="btn-ejecutar-transferencia" class="bg-purple-600 text-white font-bold py-2 px-4 rounded-xl text-sm">Transferir</button>
        </div>
    </div>`;

                document.body.appendChild(modal);

                // 2. Ejecutamos la transferencia mediante un FORMulario "fantasma" que vive solo 1 segundo
                document.getElementById('btn-ejecutar-transferencia').addEventListener('click', function() {
                    const userId = document.getElementById('new_user_select').value;
                    if (!userId) {
                        alert('Selecciona un usuario');
                        return;
                    }

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('voters.bulkReassign') }}";

                    // Agregar token CSRF
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    form.appendChild(csrf);

                    // Agregar IDs seleccionados
                    filasSeleccionadasIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });

                    // Agregar usuario destino
                    const userInp = document.createElement('input');
                    userInp.type = 'hidden';
                    userInp.name = 'new_user_id';
                    userInp.value = userId;
                    form.appendChild(userInp);

                    document.body.appendChild(form);
                    form.submit(); // Enviar formulario independiente
                });
            }

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

            // 🗑️ Función de Borrado Masivo (Corregida y Cerrada Correctamente)
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
            } // 👈 Aquí cierra de forma perfecta la función de borrado

            // 🗓️ Función de acople para armar la fecha (¡Ahora Libre y Global!)
            function sincronizarFechaSelects() {
                const d = document.getElementById('dob_day').value;
                const m = document.getElementById('dob_mes').value;
                const y = document.getElementById('dob_year').value;
                const inputOculto = document.getElementById('fecha_nacimiento');

                if (d && m && y) {
                    const fechaArmada = `${y}-${m}-${d}`;
                    inputOculto.value = fechaArmada;
                    calcularEdadDinamica(fechaArmada);
                } else {
                    inputOculto.value = '';
                    if (document.getElementById('calculatedAge')) {
                        document.getElementById('calculatedAge').innerText = '0 años';
                    }
                }
            }
        </script>
</body>

</html>