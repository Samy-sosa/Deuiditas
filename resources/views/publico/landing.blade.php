@extends('layouts.landing')

@section('title', 'Deuditas by Ocellated - Sistema de apartados para tu negocio')

@section('content')
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.png') }}">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="overflow-x-hidden font-sans antialiased text-white bg-[#050507] selection:bg-blue-500/30">
    
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
        <div class="absolute top-[-20%] right-[-10%] w-[70%] h-[70%] rounded-full bg-blue-900/20 blur-[150px] animate-pulse"></div>
        <div class="absolute bottom-[-20%] left-[-10%] w-[70%] h-[70%] rounded-full bg-indigo-900/20 blur-[150px] animate-pulse"></div>
    </div>

    <nav class="fixed top-0 w-full z-50 bg-[#050507]/80 backdrop-blur-xl border-b border-white/5 py-4 px-4 sm:px-8 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center font-black text-xl shadow-lg shadow-blue-600/30">D</div>
            <span class="text-xl sm:text-2xl font-black tracking-tighter">Deud<span class="text-blue-500">itas</span></span>
        </div>
        <div class="hidden md:flex gap-8 text-sm font-bold uppercase tracking-widest text-gray-400">
            <a href="#como-funciona" class="hover:text-white transition-colors">¿Cómo funciona?</a>
            <a href="#planes" class="hover:text-white transition-colors">Planes</a>
            <a href="#faq" class="hover:text-white transition-colors">FAQ</a>
            <a href="#registro" class="hover:text-white transition-colors">Registro</a>
        </div>
        <a href="#registro" class="px-4 sm:px-6 py-2 bg-white text-black rounded-full font-bold text-sm hover:scale-105 transition-transform whitespace-nowrap">Registrarse</a>
    </nav>

    <section class="relative min-h-screen flex items-center pt-20 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
            <div data-aos="fade-right" class="text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold mb-6">
                    <span>⚡</span> Hecho por Ocellated para emprendedores
                </div>
                <h1 class="text-5xl sm:text-7xl lg:text-8xl font-black leading-[1.1] lg:leading-[0.9] mb-8">
                    Tu tienda en <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-600">Orden Total.</span>
                </h1>
                <p class="text-lg sm:text-xl text-gray-400 mb-10 max-w-lg mx-auto lg:mx-0 leading-relaxed">
                                    </p>
                <div class="flex flex-wrap gap-4 justify-center lg:justify-start">
                    <a href="#registro" class="px-8 py-4 bg-blue-600 rounded-2xl font-black text-base sm:text-lg hover:shadow-2xl hover:shadow-blue-600/40 transition-all active:scale-95">
                        Comenzar ahora <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
            
            <div class="relative" data-aos="fade-left">
                <div class="relative z-10 bg-white/5 backdrop-blur-3xl border border-white/10 p-4 rounded-[2.5rem] shadow-2xl hover:skew-y-0 transition-all duration-700">
                    <img src="https://images.unsplash.com/photo-1556742044-3c52d6e88c62?auto=format&fit=crop&q=80&w=1000" class="rounded-[1.5rem] grayscale-[50%] hover:grayscale-0 transition-all" alt="App">
                    <div class="absolute -top-6 -right-6 bg-blue-600 p-4 rounded-2xl shadow-xl">
                        <i class="fas fa-check text-2xl"></i>
                    </div>
                </div>
                <div class="absolute inset-0 bg-blue-600/20 blur-[100px] -z-10"></div>
            </div>
        </div>
    </section>

    <section id="como-funciona" class="py-20 sm:py-32 relative z-10 bg-[#08080a]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <h2 class="text-center text-3xl sm:text-4xl md:text-5xl font-black mb-12 sm:mb-20 px-4" data-aos="fade-up">Así de fácil funciona <span class="text-blue-500 font-serif italic">Deuditas</span></h2>
            
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6 sm:gap-8 md:gap-12">
                <div class="step-card group" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-8xl font-black opacity-5 absolute -top-10 left-0 group-hover:opacity-20 transition-opacity">01</div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-xl sm:text-2xl mb-4 sm:mb-6 shadow-lg rotate-3 group-hover:rotate-12 transition-transform mx-auto sm:mx-0">
                            <i class="fas fa-plus"></i>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-center sm:text-left">Registra la Venta</h3>
                        <p class="text-sm sm:text-base text-gray-400 text-center sm:text-left">Anota qué se llevan y con cuánto lo apartan. Deuditas calcula el resto.</p>
                    </div>
                </div>
                
                <div class="step-card group" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-8xl font-black opacity-5 absolute -top-10 left-0 group-hover:opacity-20 transition-opacity">02</div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-indigo-600 rounded-2xl flex items-center justify-center text-xl sm:text-2xl mb-4 sm:mb-6 shadow-lg -rotate-3 group-hover:rotate-0 transition-transform mx-auto sm:mx-0">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-center sm:text-left">Envía el Folio</h3>
                        <p class="text-sm sm:text-base text-gray-400 text-center sm:text-left">Mándale un link por WhatsApp. Tu cliente verá su saldo cuando quiera.</p>
                    </div>
                </div>

                <div class="step-card group sm:col-span-2 md:col-span-1" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-8xl font-black opacity-5 absolute -top-10 left-0 group-hover:opacity-20 transition-opacity">03</div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-emerald-600 rounded-2xl flex items-center justify-center text-xl sm:text-2xl mb-4 sm:mb-6 shadow-lg rotate-6 group-hover:rotate-0 transition-transform mx-auto sm:mx-0">
                            <i class="fas fa-money-bill-trend-up"></i>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-center sm:text-left">Recibe Abonos</h3>
                        <p class="text-sm sm:text-base text-gray-400 text-center sm:text-left">Actualiza los pagos en un clic y mantén tu flujo de caja al día.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- SECCIÓN DE PLANES --}}
    <section id="planes" class="py-20 sm:py-32 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
            <h2 class="text-3xl sm:text-4xl md:text-6xl font-black mb-4 sm:mb-6 px-4">Elige tu plan</h2>
            <p class="text-sm sm:text-base text-gray-400 mb-8 sm:mb-12">Sin letras chiquitas. Un producto honesto de Ocellated.</p>

            <div class="grid md:grid-cols-3 gap-6 sm:gap-8 max-w-6xl mx-auto">
                
                {{-- PLAN MENSUAL --}}
                <div class="glass rounded-[2.5rem] p-6 sm:p-8 hover:border-blue-500/30 transition-all group" data-aos="zoom-in">
                    <div class="text-center mb-6">
                        <h3 class="text-xl font-black text-white mb-2">Mensual</h3>
                        <div class="text-5xl font-black text-blue-500">$199</div>
                        <div class="text-xs text-gray-400 mt-1">por mes</div>
                    </div>
                    
                    <div class="space-y-4 mb-8 text-left">
                        <div class="flex items-center text-gray-300">
                            <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                            <span class="text-sm sm:text-base">30 días de servicio</span>
                        </div>
                        <div class="flex items-center text-gray-300">
                            <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                            <span class="text-sm sm:text-base">1 tienda</span>
                        </div>
                        <div class="flex items-center text-gray-300">
                            <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                            <span class="text-sm sm:text-base">Apartados ilimitados</span>
                        </div>
                        <div class="flex items-center text-gray-300">
                            <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                            <span class="text-sm sm:text-base">Portal para clientes</span>
                        </div>
                    </div>
                    
                    <button onclick="seleccionarPlanYScroll('mensual')" 
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black py-4 rounded-2xl transition-all shadow-lg">
                        Contratar
                    </button>
                </div>

                {{-- PLAN TRIMESTRAL --}}
                <div class="glass rounded-[2.5rem] p-6 sm:p-8 hover:border-blue-500/30 transition-all group relative border-2 border-yellow-500/30 transform scale-105" data-aos="zoom-in" data-aos-delay="100">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-gradient-to-r from-yellow-500 to-yellow-600 text-black text-[10px] font-black py-1 px-4 rounded-full uppercase tracking-widest whitespace-nowrap">
                        Recomendado
                    </div>
                    
                    <div class="text-center mb-6 mt-4">
                        <h3 class="text-xl font-black text-white mb-2">Trimestral</h3>
                        <div class="text-5xl font-black text-blue-500">$497</div>
                        <div class="flex items-center justify-center gap-2 mt-1">
                            <span class="text-xs text-gray-400 line-through">$597</span>
                            <span class="text-xs bg-green-500/20 text-green-500 px-2 py-1 rounded-full">Ahorras $100</span>
                        </div>
                    </div>
                    
                    <div class="space-y-4 mb-8 text-left">
                        <div class="flex items-center text-gray-300">
                            <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                            <span class="text-sm sm:text-base">90 días de servicio</span>
                        </div>
                        <div class="flex items-center text-gray-300">
                            <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                            <span class="text-sm sm:text-base">Todo lo del plan mensual</span>
                        </div>
                        <div class="flex items-center text-gray-300">
                            <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                            <span class="text-sm sm:text-base">Soporte prioritario</span>
                        </div>
                    </div>
                    
                    <button onclick="seleccionarPlanYScroll('trimestral')" 
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black py-4 rounded-2xl transition-all shadow-lg">
                        Contratar
                    </button>
                </div>

                {{-- PLAN ANUAL --}}
                <div class="glass rounded-[2.5rem] p-6 sm:p-8 hover:border-blue-500/30 transition-all group" data-aos="zoom-in" data-aos-delay="200">
                    <div class="text-center mb-6">
                        <h3 class="text-xl font-black text-white mb-2">Anual</h3>
                        <div class="text-5xl font-black text-blue-500">$1,790</div>
                        <div class="flex items-center justify-center gap-2 mt-1">
                            <span class="text-xs text-gray-400 line-through">$2,388</span>
                            <span class="text-xs bg-green-500/20 text-green-500 px-2 py-1 rounded-full">Ahorras $598</span>
                        </div>
                    </div>
                    
                    <div class="space-y-4 mb-8 text-left">
                        <div class="flex items-center text-gray-300">
                            <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                            <span class="text-sm sm:text-base">365 días de servicio</span>
                        </div>
                        <div class="flex items-center text-gray-300">
                            <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                            <span class="text-sm sm:text-base">Todo lo del plan trimestral</span>
                        </div>
                        <div class="flex items-center text-gray-300">
                            <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                            <span class="text-sm sm:text-base">Reportes avanzados</span>
                        </div>
                        <div class="flex items-center text-gray-300">
                            <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                            <span class="text-sm sm:text-base">Exportación de datos</span>
                        </div>
                    </div>
                    
                    <button onclick="seleccionarPlanYScroll('anual')" 
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black py-4 rounded-2xl transition-all shadow-lg">
                        Contratar
                    </button>
                </div>
            </div>
            
            <div class="mt-8 p-4 bg-white/5 rounded-2xl inline-flex items-center gap-2 text-sm text-gray-400">
                <i class="fas fa-shield-alt text-blue-500"></i>
                <span>Todos los planes incluyen garantía de 7 días</span>
            </div>
        </div>
    </section>

    {{-- SECCIÓN DE REGISTRO --}}
    <section id="registro" class="py-20 sm:py-32 relative z-10 bg-[#08080a]">
        <div class="max-w-2xl mx-auto px-4 sm:px-6">
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[2rem] sm:rounded-[3rem] p-6 sm:p-8">
                <h3 class="text-2xl sm:text-3xl font-black text-center mb-6 sm:mb-8">Crear cuenta</h3>
                
                <form id="registroForm" onsubmit="procesarRegistro(event)" autocomplete="off">
                    @csrf
                    
                    <div class="grid sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-300 text-sm sm:text-base mb-2">Nombre de la tienda</label>
                            <input type="text" name="nombre_tienda" required
                                   autocomplete="off"
                                   class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:border-blue-500 transition text-sm sm:text-base">
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm sm:text-base mb-2">Email</label>
                            <input type="email" name="email" required
                                   autocomplete="off"
                                   class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:border-blue-500 transition text-sm sm:text-base">
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4 mb-4">
                        <div class="relative">
                            <label class="block text-gray-300 text-sm sm:text-base mb-2">Contraseña</label>
                            <div class="relative">
                                <input type="password" 
                                       name="password" 
                                       id="password"
                                       required
                                       autocomplete="new-password"
                                       class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:border-blue-500 transition text-sm sm:text-base pr-12">
                                <button type="button" 
                                        onclick="togglePassword('password', 'eyeIcon1')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition">
                                    <i id="eyeIcon1" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="relative">
                            <label class="block text-gray-300 text-sm sm:text-base mb-2">Confirmar contraseña</label>
                            <div class="relative">
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation"
                                       required
                                       autocomplete="new-password"
                                       class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:border-blue-500 transition text-sm sm:text-base pr-12">
                                <button type="button" 
                                        onclick="togglePassword('password_confirmation', 'eyeIcon2')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition">
                                    <i id="eyeIcon2" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Campo de cupón --}}
                    <div class="mb-6 p-4 bg-white/5 rounded-xl">
                        <label class="block text-gray-300 text-sm sm:text-base mb-2">¿Tienes un cupón?</label>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input type="text" id="cuponInput"
                                   autocomplete="off"
                                   class="flex-1 px-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:border-blue-500 transition text-sm sm:text-base"
                                   placeholder="Ingresa tu código">
                            <button type="button" onclick="validarCupon()" 
                                    class="px-6 py-3 bg-blue-600 rounded-xl hover:bg-blue-500 transition text-sm sm:text-base whitespace-nowrap">
                                Validar
                            </button>
                        </div>
                        <div id="cuponMensaje" class="text-xs sm:text-sm mt-2"></div>
                        
                        <div id="resumenDescuento" class="hidden mt-4 p-4 bg-green-500/10 border border-green-500/30 rounded-xl">
                            <div class="flex justify-between items-center">
                                <span class="text-green-400 text-sm">Cupón aplicado</span>
                                <span class="text-green-400 font-bold text-sm" id="descuentoMostrado">- $0</span>
                            </div>
                        </div>
                    </div>

                    {{-- Campos ocultos --}}
                    <input type="hidden" name="plan" id="planSeleccionado" value="mensual">
                    <input type="hidden" name="total_final" id="totalFinal" value="199">

                    {{-- Plan seleccionado --}}
                    <div class="mb-4 p-3 bg-blue-600/20 border border-blue-500/30 rounded-xl text-center">
                        <span class="text-sm text-gray-300">Plan seleccionado: </span>
                        <span id="planSeleccionadoTexto" class="font-bold text-blue-400">Mensual</span>
                    </div>

                    <button type="submit" id="btnPagar" 
                            class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl font-black text-base sm:text-lg hover:shadow-2xl hover:shadow-blue-600/40 transition-all">
                        Contratar $199
                    </button>

                    <p class="text-xs text-gray-500 text-center mt-4">
                        Al registrarte aceptas nuestros <a href="#" class="text-blue-400">Términos y condiciones</a>
                    </p>
                </form>
            </div>
        </div>
    </section>

    <section id="faq" class="py-20 sm:py-32 relative z-10 bg-[#08080a]">
        <div class="max-w-3xl mx-auto px-4 sm:px-6">
            <h2 class="text-3xl sm:text-4xl font-black text-center mb-12 sm:mb-16">Preguntas frecuentes</h2>
            <div class="space-y-4">
                <div class="faq-item p-4 sm:p-6 rounded-2xl bg-white/5 border border-white/10 cursor-pointer transition-all" onclick="toggleFaq(this)">
                    <div class="flex justify-between items-center">
                        <h4 class="font-bold text-base sm:text-lg">¿Cómo funciona?</h4>
                        <i class="fas fa-chevron-down transition-transform text-sm sm:text-base"></i>
                    </div>
                    <p class="hidden text-sm sm:text-base text-gray-400 mt-4 leading-relaxed">Es simple: registras la venta, envías el folio a tu cliente y recibes sus abonos. Todo automático.</p>
                </div>
                <div class="faq-item p-4 sm:p-6 rounded-2xl bg-white/5 border border-white/10 cursor-pointer transition-all" onclick="toggleFaq(this)">
                    <div class="flex justify-between items-center">
                        <h4 class="font-bold text-base sm:text-lg">¿Mis datos están seguros?</h4>
                        <i class="fas fa-chevron-down transition-transform text-sm sm:text-base"></i>
                    </div>
                    <p class="hidden text-sm sm:text-base text-gray-400 mt-4 leading-relaxed">Sí, utilizamos servidores encriptados y backups automáticos.</p>
                </div>
                <div class="faq-item p-4 sm:p-6 rounded-2xl bg-white/5 border border-white/10 cursor-pointer transition-all" onclick="toggleFaq(this)">
                    <div class="flex justify-between items-center">
                        <h4 class="font-bold text-base sm:text-lg">¿Cómo uso un cupón?</h4>
                        <i class="fas fa-chevron-down transition-transform text-sm sm:text-base"></i>
                    </div>
                    <p class="hidden text-sm sm:text-base text-gray-400 mt-4 leading-relaxed">Ingresa el código en el formulario de registro y valida antes de contratar.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-8 sm:py-12 border-t border-white/5 text-center relative z-10">
        <p class="text-xs sm:text-sm text-gray-600 font-bold uppercase tracking-widest">
            Deuditas © 2026 | Desarrollado por Ocellated
        </p>
    </footer>
</div>

<style>
    .step-card { @apply relative p-6 sm:p-10 rounded-[2rem] sm:rounded-[3rem] bg-white/5 border border-white/10 overflow-hidden hover:bg-white/10 transition-all duration-500 hover:-translate-y-4; }
    .glass { 
        background: rgba(15, 17, 23, 0.6); 
        backdrop-filter: blur(12px); 
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.04); 
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    }
    .faq-item.active { @apply bg-white/10 border-blue-500/30; }
    .faq-item.active i { @apply rotate-180 text-blue-500; }
    
    @keyframes float {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(30px, -30px) scale(1.1); }
    }
</style>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000, once: true });

    const precios = {
        mensual: 199,
        trimestral: 497,
        anual: 1790
    };
    
    const nombresPlanes = {
        mensual: 'Mensual',
        trimestral: 'Trimestral',
        anual: 'Anual'
    };
    
    let planSeleccionado = 'mensual';
    let cuponActivo = null;
    let descuentoActivo = 0;

    // Limpiar caché del formulario
    window.onload = function() {
        document.getElementById('registroForm').reset();
    }

    function seleccionarPlanYScroll(plan) {
        planSeleccionado = plan;
        document.getElementById('planSeleccionado').value = plan;
        document.getElementById('planSeleccionadoTexto').textContent = nombresPlanes[plan];
        actualizarTotal();
        document.getElementById('registro').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function validarCupon() {
        const codigo = document.getElementById('cuponInput').value;
        
        if (!codigo) {
            mostrarMensaje('Ingresa un código', 'error');
            return;
        }

        document.getElementById('cuponMensaje').innerHTML = '<span class="text-gray-400">Validando...</span>';

        const url = '{{ url("/validar-cupon") }}?codigo=' + encodeURIComponent(codigo) + '&plan=' + planSeleccionado;

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.valido) {
                cuponActivo = codigo;
                descuentoActivo = data.descuento;
                mostrarMensaje(data.mensaje, 'success');
                document.getElementById('resumenDescuento').classList.remove('hidden');
                document.getElementById('descuentoMostrado').textContent = `- $${data.descuento}`;
                actualizarTotal();
            } else {
                cuponActivo = null;
                descuentoActivo = 0;
                mostrarMensaje(data.mensaje, 'error');
                document.getElementById('resumenDescuento').classList.add('hidden');
                actualizarTotal();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al validar cupón', 'error');
        });
    }

    function mostrarMensaje(texto, tipo) {
        const colores = {
            success: 'text-green-400',
            error: 'text-red-400'
        };
        document.getElementById('cuponMensaje').innerHTML = `<span class="${colores[tipo]}">${texto}</span>`;
    }

    function actualizarTotal() {
        const montoBase = precios[planSeleccionado];
        const total = montoBase - descuentoActivo;
        document.getElementById('totalFinal').value = total;
        document.getElementById('btnPagar').innerHTML = `Contratar $${total.toFixed(2)}`;
    }

    function procesarRegistro(event) {
        event.preventDefault();
        
        const form = document.getElementById('registroForm');
        const formData = new FormData(form);
        
        formData.set('plan', planSeleccionado); 
        if(cuponActivo) formData.set('cupon', cuponActivo);
        
        const btnPagar = document.getElementById('btnPagar');
        const textoOriginal = btnPagar.innerHTML;
        
        btnPagar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        btnPagar.disabled = true;

        fetch('{{ url("/registro/procesar") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(async res => {
            const data = await res.json();
            
            if (res.status === 422) {
                let mensajeError = "Error:\n";
                Object.values(data.errors).forEach(err => {
                    mensajeError += `- ${err[0]}\n`;
                });
                throw new Error(mensajeError);
            }

            if (!res.ok) throw new Error(data.message || 'Error en el servidor');
            
            return data;
        })
        .then(data => {
            if (data.success) {
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 500);
            }
        })
        .catch(error => {
            alert(error.message);
            btnPagar.innerHTML = textoOriginal;
            btnPagar.disabled = false;
        });
    }

    function toggleFaq(element) {
        const p = element.querySelector('p');
        const icon = element.querySelector('i');
        element.classList.toggle('active');
        p.classList.toggle('hidden');
    }

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
</script>
@endsection