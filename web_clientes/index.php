<!DOCTYPE html>
<html lang="es" class="overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stefy Barroso | Consultora Oficial</title>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#00a876',
                        secondary: '#d4af37',
                        brandText: '#111827',
                        brandSubtext: '#4B5563',
                    },
                    fontFamily: {
                        raleway: ['Raleway', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Raleway', sans-serif; scroll-behavior: smooth; }
        .glass-header {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            transition: all 0.3s ease;
        }
        .header-scrolled {
            padding: 0.25rem 0;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }
        .gradient-gold {
            background: linear-gradient(135deg, #d4af37 0%, #fef3c7 100%);
        }
    </style>
</head>
<body class="bg-[#fcfdfd] text-brandText antialiased scroll-smooth font-sans overflow-x-hidden w-full m-0 p-0">

    <!-- Background Gradient (Subtle) -->
    <div class="fixed inset-0 bg-gradient-to-br from-[#f0f9f6] via-white to-[#fdfbf5] -z-10 pointer-events-none"></div>

    <!-- Mobile Menu Drawer (Already updated with Contacto) -->
    <div id="mobile-menu" class="fixed inset-0 bg-brandText/95 z-[60] flex flex-col items-center justify-center space-y-8 text-2xl text-white font-bold transition-all transform translate-x-full">
        <button id="close-menu" class="absolute top-6 right-6 text-white focus:outline-none">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <a href="#inicio" class="mobile-link hover:text-primary transition-colors">INICIO</a>
        <a href="#catalogo" class="mobile-link hover:text-primary transition-colors">CATÁLOGO</a>
        <a href="#sumate" class="mobile-link hover:text-primary transition-colors">SUMATE</a>
        <a href="#contacto" class="mobile-link hover:text-primary transition-colors">CONTACTO</a>
    </div>

    <!-- Header -->
    <header id="main-header" class="fixed top-0 left-0 w-full z-50 glass-header border-b border-gray-100">
        <nav class="w-full max-w-7xl mx-auto px-2 lg:px-5 py-2 flex justify-between items-center transition-all duration-300" id="nav-container">
            
            <!-- Mobile Menu Toggle (Izquierda) -->
            <div class="md:hidden flex-1">
                <button id="menu-btn" class="text-brandText focus:outline-none">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h8"></path>
                    </svg>
                </button>
            </div>

            <!-- Desktop Menu -->
            <ul class="hidden md:flex flex-1 justify-end space-x-10 font-bold text-xs tracking-[0.1em] text-gray-500 items-center">
                <li><a href="#inicio" class="hover:text-primary transition-colors">INICIO</a></li>
                <li><a href="#catalogo" class="hover:text-primary transition-colors">CATÁLOGO</a></li>
                <li><a href="#sumate" class="hover:text-primary transition-colors">SUMATE</a></li>
                <li><a href="#contacto" class="hover:text-primary transition-colors">CONTACTO</a></li>
            </ul>
            
        </nav>
    </header>

    <!-- Hero Section (Margin tightened to fixed header) -->
    <section id="inicio" class="w-full max-w-7xl mx-auto px-2 lg:px-5 pt-16 pb-6 md:pt-20 flex flex-col lg:flex-row items-stretch gap-5 lg:gap-8">
        
        <!-- Columna Izquierda: Información en Placas -->
        <div class="lg:w-1/2 flex flex-col w-full gap-3">
            
            <!-- Placa 1: Identidad (Nombre + Logo) -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 px-5 py-3 md:px-8 md:py-4 flex items-center justify-between">
                <div class="flex flex-col justify-center">
                    <span class="text-[3.5rem] md:text-[4.5rem] lg:text-[5.5rem] font-black leading-[0.85] tracking-tight text-primary">
                        Stefy
                    </span>
                    <span class="text-[3.5rem] md:text-[4.5rem] lg:text-[5.5rem] font-black leading-[0.85] tracking-tight text-secondary">
                        Barroso
                    </span>
                </div>
                <!-- Negative margin compensates for the natural transparent padding inside the png image file to make the border visually symmetrical -->
                <img src="assets/logo.png" alt="Logo Stefy Barroso" class="h-36 md:h-44 lg:h-[13rem] w-auto object-contain -mr-4 md:-mr-5">
            </div>
            
            <!-- Placa 2: Presentación y Servicios -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-5 md:p-8 flex-1 text-brandSubtext">
                <p class="flex items-start text-lg font-medium text-brandText mb-4 pb-4 border-b border-gray-100">
                    <span class="mr-3 text-secondary text-xl">✨</span>
                    <span>¡Bienvenidos a mi página web! Soy Stefy, tu mejor consultora de belleza.</span>
                </p>
                
                <ul class="space-y-4">
                    <li class="flex items-center">
                        <span class="mr-3 text-2xl bg-secondary/10 p-2.5 rounded-2xl w-11 h-11 flex items-center justify-center shrink-0">💇</span> 
                        <div class="flex flex-col">
                            <strong class="text-brandText text-lg leading-tight">Peluquería profesional</strong>
                            <span class="text-sm">para resaltar tu estilo.</span>
                        </div>
                    </li>
                    <li class="flex items-center">
                        <span class="mr-3 text-2xl bg-secondary/10 p-2.5 rounded-2xl w-11 h-11 flex items-center justify-center shrink-0">💄</span> 
                        <div class="flex flex-col">
                            <strong class="text-brandText text-lg leading-tight">Maquillaje social y artístico</strong>
                            <span class="text-sm">para cada ocasión.</span>
                        </div>
                    </li>
                    <li class="flex items-center">
                        <span class="mr-3 text-2xl bg-secondary/10 p-2.5 rounded-2xl w-11 h-11 flex items-center justify-center shrink-0">💅</span> 
                        <div class="flex flex-col">
                            <strong class="text-brandText text-lg leading-tight">Manicura y uñas</strong>
                            <span class="text-sm">con las últimas tendencias.</span>
                        </div>
                    </li>
                    <li class="flex items-center">
                        <span class="mr-3 text-2xl bg-primary/10 p-2.5 rounded-2xl w-11 h-11 flex items-center justify-center shrink-0">🛍️</span> 
                        <div class="flex flex-col">
                            <strong class="text-brandText text-lg leading-tight">Venta de productos</strong>
                            <span class="text-sm">con asesoramiento personalizado.</span>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Botones de Acción -->
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="#catalogo" class="bg-primary text-white px-8 py-4 rounded-[2rem] text-lg font-black shadow-lg shadow-green-200 hover:scale-[1.02] transition-all text-center flex-1">
                    Descubrí el catálogo
                </a>
                
                <!-- TODO/FUTURO: Botón real para el sistema de turnos cuando esté integrado. (Oculto temporalmente) -->
                <!-- 
                <button onclick="abrirModalTurnos()" class="border-2 border-primary text-primary px-8 py-4 rounded-[2rem] font-bold hover:bg-primary hover:text-white transition-all text-center flex-1">
                    Agendar Turno
                </button> 
                -->
                
                <!-- Botón temporal derivando a WhatsApp -->
                <a href="https://wa.me/5492235869878?text=Hola%20Stefy.%20Quiero%20agendar%20un%20turno." target="_blank" class="border-2 border-primary text-primary px-8 py-4 rounded-[2rem] font-bold hover:bg-primary hover:text-white transition-all text-center flex-1">
                    Agendar Turno
                </a>
            </div>
        </div>

        <!-- Columna Derecha: Gráfica Publicitaria -->
        <div class="lg:w-1/2 relative flex justify-center items-center w-full mt-8 lg:mt-0">
            <div class="rounded-[2.5rem] shadow-2xl relative z-10 w-full max-w-md bg-transparent overflow-hidden">
                <img src="assets/Look_good_with_confidence.png" alt="Stefy Barroso - Consultora" class="w-full h-auto object-contain transform hover:scale-[1.02] transition-transform duration-700">
            </div>
            <!-- Decorative blobs -->
            <div class="absolute -bottom-10 -right-10 w-72 h-72 bg-secondary rounded-full filter blur-3xl opacity-10 -z-0"></div>
            <div class="absolute -top-10 -left-10 w-56 h-56 bg-primary/10 rounded-full filter blur-2xl opacity-40 -z-0"></div>
        </div>
    </section>

    <!-- CTA Banner -->
    <section class="bg-primary py-12">
        <div class="w-full max-w-7xl mx-auto px-2 lg:px-5 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                ¿Querés ser parte de nuestro equipo?
            </h2>
            <p class="text-white/90 text-lg mb-8 max-w-2xl mx-auto">
                Sumate como Consultora Independiente y empezá a construir tu propio negocio con el respaldo de una marca líder.
            </p>
            <button class="bg-white text-primary px-10 py-4 rounded-full font-bold shadow-xl hover:bg-gray-50 transition-colors">
                ¡Quiero sumarme ahora!
            </button>
        </div>
    </section>

    <!-- Feature Cards / Subplacas -->
    <section class="py-12 bg-gray-50">
        <div class="w-full max-w-7xl mx-auto px-2 lg:px-5">
            <h2 class="text-3xl font-bold text-center mb-10">Nuestros Servicios</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Subplaca -->
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl transition-shadow group">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Catálogo Digital</h3>
                    <p class="text-brandSubtext">Accedé a todas nuestras líneas de productos con un solo click, siempre actualizado.</p>
                </div>

                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl transition-shadow group">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Asesoría Personalizada</h3>
                    <p class="text-brandSubtext">Te ayudamos a elegir las piezas que mejor se adaptan a tu estilo y presupuesto.</p>
                </div>

                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl transition-shadow group">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Pagos Flexibles</h3>
                    <p class="text-brandSubtext">Diferentes medios de pago para que no te quedes sin lo que te gusta.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Recruitment Section -->
    <section class="py-12 bg-white">
        <div class="w-full max-w-7xl mx-auto px-2 lg:px-5 flex flex-col md:flex-row items-center gap-10">
            <div class="md:w-1/2">
                <div class="rounded-3xl bg-primary/5 p-8 border border-primary/10 relative overflow-hidden group">
                    <div class="relative z-10">
                        <h2 class="text-4xl font-bold mb-6 italic"><span class="text-primary">¡Deseo</span> sumarme!</h2>
                        <p class="text-lg text-brandSubtext mb-8">
                            Unite a nuestra comunidad de consultoras y empezá a transformar tu futuro. Te acompañamos en cada paso con capacitación y herramientas exclusivas.
                        </p>
                        <a href="https://wa.me/5491112345678?text=Hola!%20Me%20interesa%20sumarme%20como%20consultora" target="_blank" class="inline-block bg-primary text-white px-10 py-4 rounded-full font-bold hover:scale-105 transition-transform">
                            Contactar por WhatsApp
                        </a>
                    </div>
                </div>
            </div>
            <div class="md:w-1/2">
                <div class="aspect-video bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200 flex items-center justify-center p-8">
                    <div class="text-center">
                        <p class="text-gray-400 font-semibold mb-2">ESPACIO PARA VIDEO / IMAGEN</p>
                        <p class="text-gray-300 text-sm">Contenido de capacitación Perla Negra</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contacto / Social Section -->
    <section id="contacto" class="py-12 bg-gray-50">
        <div class="w-full max-w-7xl mx-auto px-2 lg:px-5 max-w-xl">
            <div class="text-center mb-8">
                <h2 class="text-4xl font-black text-brandText mb-3">¿Hablamos?</h2>
                <p class="text-brandSubtext">Elegí tu medio de contacto preferido</p>
            </div>
            
            <div class="flex flex-col gap-5">
                <!-- WhatsApp -->
                <a href="https://wa.me/5492235869878?text=Hola!%20Deseo%20hacerte%20una%20consulta" class="flex items-center p-6 bg-white rounded-3xl shadow-sm border border-gray-100 hover:scale-105 transition-all">
                    <div class="bg-[#25D366]/10 p-4 rounded-2xl mr-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#25D366" viewBox="0 0 16 16">
                            <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93a7.898 7.898 0 0 0-2.327-5.594l.008-.008zm-5.606 12.23c-1.25 0-2.456-.33-3.509-.96l-.25-.15-2.61.685.698-2.541-.165-.262a6.839 6.839 0 0 1-1.05-3.69c.002-3.791 3.086-6.877 6.88-6.877a6.853 6.853 0 0 1 4.856 2.013 6.856 6.856 0 0 1 2.015 4.857c-.004 3.792-3.089 6.878-6.884 6.878zM11.56 10.29c-.194-.097-1.144-.565-1.32-.63-.176-.064-.304-.097-.432.097-.128.193-.496.63-.608.756-.112.126-.224.141-.418.045-.194-.097-.82-.303-1.562-.963-.578-.517-.968-1.156-1.082-1.35-.112-.193-.012-.298.085-.395.087-.087.194-.224.29-.337.098-.113.13-.193.194-.322.065-.13.032-.242-.016-.337-.048-.097-.432-1.043-.593-1.433-.153-.374-.32-.322-.432-.328-.11-.006-.239-.007-.367-.007a.703.703 0 0 0-.51.242c-.176.193-.672.657-.672 1.603 0 .945.69 1.86 1.786 2.007.194.027 1.153.303 1.956.657.34.15.64.218.88.254.214.033.613.02 1.103-.053.547-.082 1.144-.468 1.304-.928.16-.46.16-.853.112-.928-.048-.076-.176-.112-.37-.209z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-xl">WhatsApp</h3>
                        <p class="text-brandSubtext text-sm">Escribime ahora</p>
                    </div>
                </a>

                <!-- Instagram -->
                <a href="https://instagram.com/stefybarroso" target="_blank" class="flex items-center p-6 bg-white rounded-3xl shadow-sm border border-gray-100 hover:scale-105 transition-all">
                    <div class="bg-[#E4405F]/10 p-4 rounded-2xl mr-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#E4405F" viewBox="0 0 16 16">
                            <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.282.11-.705.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-xl">Instagram</h3>
                        <p class="text-brandSubtext text-sm">@stefybarroso</p>
                    </div>
                </a>

                <!-- TikTok -->
                <a href="#" target="_blank" class="flex items-center p-6 bg-white rounded-3xl shadow-sm border border-gray-100 hover:scale-105 transition-all">
                    <div class="bg-black/10 p-4 rounded-2xl mr-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3V0Z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-xl">TikTok</h3>
                        <p class="text-brandSubtext text-sm">Encontrame como Stefy Barroso</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-brandText text-white pt-8 pb-4">
        <div class="w-full max-w-7xl mx-auto px-2 lg:px-5 flex flex-col md:flex-row justify-between items-center md:items-start border-b border-white/10 pb-6">
            <div class="mb-6 md:mb-0 text-center md:text-left flex flex-col items-center md:items-start">
                <img src="assets/logo.png" alt="Perla Negra" class="h-10 w-auto mb-2 brightness-0 invert mx-auto md:mx-0">
                <p class="max-w-xs text-gray-400 text-sm">Transformá tu belleza y crecé con el asesoramiento profesional de Stefy Barroso.</p>
            </div>
            <div class="flex flex-row gap-12 sm:gap-20 text-left">
                <div class="mb-4 md:mb-0">
                    <h3 class="text-base font-bold mb-2">Servicios</h3>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#catalogo" class="hover:text-white transition-colors">Peluquería</a></li>
                        <li><a href="#catalogo" class="hover:text-white transition-colors">Maquillaje</a></li>
                        <li><a href="#catalogo" class="hover:text-white transition-colors">Uñas</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-base font-bold mb-2">Contacto</h3>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="https://wa.me/5492235869878" class="hover:text-white transition-colors">WhatsApp</a></li>
                        <li><a href="https://instagram.com/stefybarroso" class="hover:text-white transition-colors">Instagram</a></li>
                        <li><a href="https://tiktok.com/@stefybarroso" class="hover:text-white transition-colors">TikTok</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="w-full max-w-7xl mx-auto px-2 lg:px-5 mt-4 text-center text-gray-500 text-xs">
            <p>&copy; 2026 Stefy Barroso - Consultora de Belleza. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/5492235869878?text=Hola!%20Me%20contacto%20desde%20tu%20página%20web" target="_blank" class="fixed bottom-8 right-8 bg-[#25D366] text-white p-4 rounded-full shadow-2xl hover:scale-110 transition-transform z-[100] flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
            <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93a7.898 7.898 0 0 0-2.327-5.594l.008-.008zm-5.606 12.23c-1.25 0-2.456-.33-3.509-.96l-.25-.15-2.61.685.698-2.541-.165-.262a6.839 6.839 0 0 1-1.05-3.69c.002-3.791 3.086-6.877 6.88-6.877a6.853 6.853 0 0 1 4.856 2.013 6.856 6.856 0 0 1 2.015 4.857c-.004 3.792-3.089 6.878-6.884 6.878zM11.56 10.29c-.194-.097-1.144-.565-1.32-.63-.176-.064-.304-.097-.432.097-.128.193-.496.63-.608.756-.112.126-.224.141-.418.045-.194-.097-.82-.303-1.562-.963-.578-.517-.968-1.156-1.082-1.35-.112-.193-.012-.298.085-.395.087-.087.194-.224.29-.337.098-.113.13-.193.194-.322.065-.13.032-.242-.016-.337-.048-.097-.432-1.043-.593-1.433-.153-.374-.32-.322-.432-.328-.11-.006-.239-.007-.367-.007a.703.703 0 0 0-.51.242c-.176.193-.672.657-.672 1.603 0 .945.69 1.86 1.786 2.007.194.027 1.153.303 1.956.657.34.15.64.218.88.254.214.033.613.02 1.103-.053.547-.082 1.144-.468 1.304-.928.16-.46.16-.853.112-.928-.048-.076-.176-.112-.37-.209z"/>
        </svg>
    </a>

    <!-- Footer Scripts -->
    <script>
        // Scroll Effect
        const header = document.getElementById('main-header');
        const nav = document.getElementById('nav-container');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) {
                header.classList.add('header-scrolled');
                nav.classList.replace('py-2', 'py-1');
            } else {
                header.classList.remove('header-scrolled');
                nav.classList.replace('py-1', 'py-2');
            }
        });

        // Mobile Menu Toggle
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const closeBtn = document.getElementById('close-menu');

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.remove('translate-x-full');
        });

        closeBtn.addEventListener('click', () => {
            mobileMenu.classList.add('translate-x-full');
        });

        // Close menu on link click
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('translate-x-full');
            });
        });
    </script>
</body>
</html>
