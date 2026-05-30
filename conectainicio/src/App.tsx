/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import { useState, type ReactNode } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { 
  Shield, 
  Flame, 
  HeartHandshake, 
  Stethoscope, 
  Users, 
  Headphones, 
  ArrowRight, 
  MapPin, 
  Phone, 
  Mail, 
  Clock, 
  Bell, 
  UserCircle,
  CheckCircle2,
  Menu,
  X
} from 'lucide-react';

interface ServiceCardProps {
  icon: ReactNode;
  title: string;
  description: string;
  protocol: string[];
}

function ServiceCard({ icon, title, description, protocol }: ServiceCardProps) {
  const [isOverlayOpen, setIsOverlayOpen] = useState(false);

  return (
    <div 
      className="group relative overflow-hidden bg-surface-container-lowest p-6 rounded-xl border border-outline-variant hover:shadow-lg transition-all duration-300"
      id={`service-${title.toLowerCase().replace(/\s+/g, '-')}`}
    >
      <div className="w-12 h-12 rounded-lg bg-surface-container-low flex items-center justify-center mb-6 text-primary">
        {icon}
      </div>
      <h3 className="text-xl font-semibold text-primary mb-3">{title}</h3>
      <p className="text-on-surface-variant text-sm mb-6 leading-relaxed">{description}</p>
      <button 
        onClick={() => setIsOverlayOpen(true)}
        className="text-sm font-semibold text-primary flex items-center gap-2 hover:underline focus:outline-none"
      >
        Ver protocolo <ArrowRight className="w-4 h-4" />
      </button>

      <AnimatePresence>
        {isOverlayOpen && (
          <motion.div 
            initial={{ opacity: 0, x: '100%' }}
            animate={{ opacity: 1, x: 0 }}
            exit={{ opacity: 0, x: '100%' }}
            transition={{ type: 'spring', damping: 25, stiffness: 200 }}
            className="absolute inset-0 brand-gradient p-6 flex flex-col justify-center z-10"
          >
            <button 
              onClick={() => setIsOverlayOpen(false)}
              className="absolute top-4 right-4 text-white hover:bg-white/20 p-1 rounded-full transition-colors"
            >
              <X className="w-5 h-5" />
            </button>
            <h4 className="text-xl font-semibold text-white mb-4 border-b border-white/20 pb-2">Protocolo</h4>
            <ul className="space-y-3">
              {protocol.map((step, index) => (
                <li key={index} className="flex gap-2 text-white/90 text-sm items-start">
                  <CheckCircle2 className="w-5 h-5 flex-shrink-0 mt-0.5" />
                  <span>{step}</span>
                </li>
              ))}
            </ul>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}

export default function App() {
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  const emergencyNumbers = [
    { number: '123', label: 'Línea Única', color: 'text-secondary' },
    { number: '119', label: 'Bomberos', color: 'text-primary' },
    { number: '132', label: 'Cruz Roja', color: 'text-primary' },
    { number: '144', label: 'Defensa Civil', color: 'text-primary' },
    { number: '195', label: 'Atención Ciudadana', color: 'text-primary' },
  ];

  const services = [
    {
      icon: <Shield className="w-8 h-8" />,
      title: 'Policía Nacional',
      description: 'Vigilancia comunitaria y atención inmediata ante situaciones de inseguridad ciudadana.',
      protocol: [
        'Reportar situaciones sospechosas',
        'Mantener la calma en todo momento',
        'Evitar confrontaciones directas',
        'Seguir las instrucciones policiales',
        'Registrar evidencia de forma segura'
      ]
    },
    {
      icon: <Flame className="w-8 h-8" />,
      title: 'Bomberos',
      description: 'Prevención y atención de incendios, rescates y manejo de materiales peligrosos.',
      protocol: [
        'Activar alarma de incendios',
        'No utilizar ascensores',
        'Mantener rutas de evacuación libres',
        'Reportar personas atrapadas',
        'Esperar autorización de bomberos'
      ]
    },
    {
      icon: <HeartHandshake className="w-8 h-8" />,
      title: 'Defensa Civil',
      description: 'Gestión del riesgo, desastres y acción social comunitaria en todo el territorio.',
      protocol: [
        'Identificar zonas de seguridad',
        'Seguir alertas tempranas',
        'Participar en simulacros',
        'Priorizar personas vulnerables',
        'Informar daños estructurales'
      ]
    },
    {
      icon: <Stethoscope className="w-8 h-8" />,
      title: 'Red Médica',
      description: 'Atención de urgencias, ambulancias y red hospitalaria de alta complejidad.',
      protocol: [
        'Solicitar asistencia médica',
        'No mover a los lesionados',
        'Aplicar primeros auxilios básicos',
        'Despejar el área de atención',
        'Proporcionar historial médico'
      ]
    },
    {
      icon: <Users className="w-8 h-8" />,
      title: 'Apoyo Comunitario',
      description: 'Red de voluntarios y vecinos capacitados para la primera respuesta local.',
      protocol: [
        'Activar red de comunicación',
        'Brindar apoyo inicial inmediato',
        'Coordinar puntos de encuentro',
        'Fomentar la solidaridad activa',
        'Reportar información verificada'
      ]
    },
    {
      icon: <Headphones className="w-8 h-8" />,
      title: 'Línea de Escucha',
      description: 'Apoyo psicológico y contención emocional profesional 24 horas al día.',
      protocol: [
        'Establecer escucha activa',
        'Identificar señales de crisis',
        'Garantizar confidencialidad',
        'Remitir a especialistas',
        'Acompañamiento emocional'
      ]
    },
  ];

  return (
    <div className="min-h-screen bg-surface flex flex-col font-sans overflow-x-hidden">
      {/* TopAppBar */}
      <header className="bg-surface border-b border-outline-variant shadow-sm sticky top-0 z-50">
        <div className="max-w-container-max mx-auto px-4 md:px-8 h-16 flex justify-between items-center">
          <div className="flex items-center gap-8">
            <span className="text-2xl font-bold text-primary tracking-tight">ConectaVida</span>
            <nav className="hidden md:flex gap-6">
              {['Emergencias', 'Recursos', 'Mapa', 'Voluntarios', 'Noticias'].map((item) => (
                <a key={item} href="#" className="text-sm font-medium text-on-surface-variant hover:text-primary transition-colors">
                  {item}
                </a>
              ))}
            </nav>
          </div>
          <div className="flex items-center gap-4">
            <button className="hidden sm:block brand-gradient text-white px-6 py-2 rounded-lg text-sm font-semibold hover:opacity-90 active:scale-95 transition-all shadow-md">
              Pedir Ayuda
            </button>
            <div className="flex gap-2 items-center">
              <button className="p-2 rounded-full hover:bg-surface-container-low text-on-surface-variant">
                <Bell className="w-5 h-5" />
              </button>
              <button className="p-2 rounded-full hover:bg-surface-container-low text-on-surface-variant">
                <UserCircle className="w-6 h-6" />
              </button>
              <button 
                className="md:hidden p-2 rounded-full hover:bg-surface-container-low text-on-surface-variant"
                onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
              >
                {isMobileMenuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
              </button>
            </div>
          </div>
        </div>
        
        {/* Mobile menu */}
        <AnimatePresence>
          {isMobileMenuOpen && (
            <motion.div 
              initial={{ height: 0, opacity: 0 }}
              animate={{ height: 'auto', opacity: 1 }}
              exit={{ height: 0, opacity: 0 }}
              className="md:hidden bg-surface border-b border-outline-variant overflow-hidden"
            >
              <div className="px-4 py-6 flex flex-col gap-4">
                {['Emergencias', 'Recursos', 'Mapa', 'Voluntarios', 'Noticias'].map((item) => (
                  <a key={item} href="#" className="text-lg font-medium text-on-surface-variant py-2 border-b border-outline-variant/30">
                    {item}
                  </a>
                ))}
                <button className="w-full brand-gradient text-white py-3 rounded-lg font-semibold mt-4">
                  Pedir Ayuda
                </button>
              </div>
            </motion.div>
          )}
        </AnimatePresence>
      </header>

      <main>
        {/* Hero Section */}
        <section className="relative h-[500px] md:h-[600px] flex items-center overflow-hidden">
          <div className="absolute inset-0 z-0">
            <img 
              src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?auto=format&fit=crop&q=80&w=2000" 
              className="w-full h-full object-cover" 
              alt="Professional responders" 
            />
            <div className="absolute inset-0 hero-overlay"></div>
          </div>
          <div className="relative z-10 w-full max-w-container-max mx-auto px-4 md:px-8">
            <motion.div 
              initial={{ opacity: 0, y: 30 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8 }}
              className="max-w-2xl"
            >
              <h1 className="text-4xl md:text-6xl font-extrabold text-white mb-4 leading-tight tracking-tight">
                Tu comunidad, <br className="hidden md:block" /> tu fortaleza
              </h1>
              <p className="text-lg md:text-xl text-white/95 mb-8 max-w-lg leading-relaxed font-normal">
                Sistema de respuesta comunitaria de precisión diseñado para garantizar la seguridad y salud de cada ciudadano a través de una red integrada de profesionales.
              </p>
              <div className="flex flex-wrap gap-4">
                <button className="bg-white text-primary px-8 py-3 rounded-xl font-bold shadow-xl hover:shadow-2xl hover:scale-105 active:scale-95 transition-all text-lg">
                  Ver Servicios
                </button>
                <button className="border-2 border-white text-white px-8 py-3 rounded-xl font-bold hover:bg-white/10 active:scale-95 transition-all text-lg backdrop-blur-sm">
                  Sedes Bogotá
                </button>
              </div>
            </motion.div>
          </div>
        </section>

        {/* Emergency Numbers */}
        <section className="py-16 bg-surface-container-low">
          <div className="max-w-container-max mx-auto px-4 md:px-8">
            <div className="flex items-center justify-between mb-8 gap-4 flex-wrap">
              <h2 className="text-2xl md:text-3xl font-bold text-primary">Números de Emergencia</h2>
              <span className="text-sm font-semibold text-secondary-container bg-secondary px-4 py-1.5 rounded-full inline-flex items-center gap-2">
                <span className="w-2 h-2 rounded-full bg-white animate-pulse" />
                Atención 24/7
              </span>
            </div>
            <div className="grid grid-cols-2 md:grid-cols-5 gap-6">
              {emergencyNumbers.map((item) => (
                <motion.div 
                  key={item.number}
                  whileHover={{ y: -8 }}
                  className="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant flex flex-col items-center justify-center text-center shadow-sm hover:shadow-md transition-all"
                >
                  <span className={`text-4xl font-bold ${item.color} mb-2`}>{item.number}</span>
                  <span className="text-sm font-medium text-on-surface-variant uppercase tracking-wider">{item.label}</span>
                </motion.div>
              ))}
            </div>
          </div>
        </section>

        {/* Support Services Grid */}
        <section className="py-24">
          <div className="max-w-container-max mx-auto px-4 md:px-8">
            <div className="mb-12">
              <h2 className="text-3xl md:text-4xl font-bold text-primary mb-3">Servicios de Apoyo</h2>
              <p className="text-lg text-on-surface-variant max-w-3xl">
                Red de respuesta integral coordinada para su tranquilidad. Interactúe con "Ver protocolo" para conocer los pasos a seguir.
              </p>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              {services.map((service, idx) => (
                <motion.div
                  key={service.title}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ delay: idx * 0.1 }}
                >
                  <ServiceCard {...service} />
                </motion.div>
              ))}
            </div>
          </div>
        </section>

        {/* Emergency Protocol */}
        <section className="py-24 brand-gradient text-white relative overflow-hidden">
          <div className="max-w-container-max mx-auto px-4 md:px-8 relative z-10">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
              <div>
                <h2 className="text-3xl md:text-5xl font-bold mb-8 tracking-tight">Protocolo de Emergencia</h2>
                <p className="text-lg md:text-xl mb-12 text-white/90 leading-relaxed font-light">
                  Siga estas instrucciones precisas para maximizar la efectividad de la respuesta comunitaria en situaciones críticas.
                </p>
                <div className="space-y-8">
                  {[
                    { title: 'Mantenga la calma', info: 'Evalúe el entorno rápidamente para identificar riesgos inmediatos antes de actuar.' },
                    { title: 'Comuníquese de inmediato', info: 'Llame a la línea 123 o use el botón "Pedir Ayuda" en la aplicación móvil.' },
                    { title: 'Proporcione su ubicación', info: 'Sea exacto con su posición y describa brevemente la naturaleza de la emergencia.' },
                    { title: 'Siga instrucciones', info: 'Permanezca en línea si es seguro y siga las indicaciones del operador profesional.' }
                  ].map((step, idx) => (
                    <motion.div 
                      key={step.title}
                      initial={{ opacity: 0, x: -20 }}
                      whileInView={{ opacity: 1, x: 0 }}
                      viewport={{ once: true }}
                      transition={{ delay: idx * 0.1 }}
                      className="flex gap-6"
                    >
                      <div className="flex-shrink-0 w-12 h-12 rounded-full border-2 border-white/40 flex items-center justify-center text-xl font-bold">
                        {idx + 1}
                      </div>
                      <div>
                        <h4 className="text-xl font-bold mb-2">{step.title}</h4>
                        <p className="text-white/80 leading-relaxed">{step.info}</p>
                      </div>
                    </motion.div>
                  ))}
                </div>
              </div>
              <motion.div 
                initial={{ opacity: 0, scale: 0.9 }}
                whileInView={{ opacity: 1, scale: 1 }}
                viewport={{ once: true }}
                className="relative rounded-3xl overflow-hidden shadow-2xl border-8 border-white/10"
              >
                <img 
                  src="https://images.unsplash.com/photo-1551288049-bbbda50a5f4a?auto=format&fit=crop&q=80&w=1200" 
                  alt="Dispatch interface" 
                  className="w-full aspect-[4/3] object-cover"
                />
                <div className="absolute inset-0 bg-primary/20 backdrop-brightness-50" />
              </motion.div>
            </div>
          </div>
          <div className="absolute right-0 bottom-0 opacity-10 pointer-events-none translate-x-1/4 translate-y-1/4">
             <Shield size={600} className="text-white" />
          </div>
        </section>

        {/* Contact and Map Section */}
        <section className="py-24">
          <div className="max-w-container-max mx-auto px-4 md:px-8">
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
              <div className="lg:col-span-4 space-y-10">
                <div>
                  <h2 className="text-3xl font-bold text-primary mb-4">Información de Contacto</h2>
                  <p className="text-on-surface-variant font-medium">Sede Administrativa Principal</p>
                </div>
                <div className="space-y-6">
                  {[
                    { icon: <MapPin className="w-5 h-5" />, title: 'Dirección', detail: 'Calle 100 #15-32, Edificio Sentinel\nBogotá D.C., Colombia' },
                    { icon: <Phone className="w-5 h-5" />, title: 'Teléfono Administrativo', detail: '+57 (601) 456 7890' },
                    { icon: <Mail className="w-5 h-5" />, title: 'Correo de Emergencia', detail: 'contacto@conectavida.org' },
                    { icon: <Clock className="w-5 h-5" />, title: 'Horario Administrativo', detail: 'Lunes - Viernes: 8:00 AM - 5:00 PM' }
                  ].map((item) => (
                    <div key={item.title} className="flex items-start gap-4">
                      <div className="mt-1 text-primary">{item.icon}</div>
                      <div>
                        <h5 className="text-sm font-bold text-primary mb-1">{item.title}</h5>
                        <p className="text-on-surface-variant text-sm whitespace-pre-line leading-relaxed">{item.detail}</p>
                      </div>
                    </div>
                  ))}
                </div>
                <button className="w-full border-2 border-primary text-primary py-3.5 rounded-xl text-sm font-bold hover:bg-surface-container-low transition-all active:scale-95 shadow-sm">
                  Ver todas las sedes
                </button>
              </div>
              <div className="lg:col-span-8 h-[500px] bg-surface-container-low rounded-3xl overflow-hidden border border-outline-variant relative shadow-inner">
                <img 
                  src="https://images.unsplash.com/photo-1619468129361-605ebea04b44?auto=format&fit=crop&q=80&w=1500" 
                  alt="City Map" 
                  className="w-full h-full object-cover mix-blend-overlay opacity-80"
                />
                <div className="absolute inset-0 bg-primary/5" />
                <div className="absolute inset-0 flex items-center justify-center">
                  <motion.div 
                    initial={{ scale: 0.8, opacity: 0 }}
                    whileInView={{ scale: 1, opacity: 1 }}
                    className="brand-gradient text-white p-4 rounded-xl shadow-2xl flex items-center gap-3"
                  >
                    <div className="bg-white/20 p-2 rounded-lg">
                      <MapPin className="w-6 h-6" />
                    </div>
                    <div className="pr-2">
                       <p className="text-[10px] uppercase font-bold text-white/70 tracking-widest">Sede Central</p>
                       <p className="text-sm font-bold">ConectaVida Bogotá</p>
                    </div>
                  </motion.div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>

      {/* Footer */}
      <footer className="bg-primary text-white mt-auto">
        <div className="max-w-container-max mx-auto px-4 md:px-8 py-12 flex flex-col items-center gap-8">
          <span className="text-2xl font-bold tracking-tight">ConectaVida</span>
          <div className="flex flex-wrap justify-center gap-x-8 gap-y-4">
            {['Privacidad', 'Términos de Servicio', 'Contacto Emergente', 'Red de Centros', 'Documentación'].map((link) => (
              <a key={link} href="#" className="text-xs font-semibold text-white/70 hover:text-white transition-colors uppercase tracking-widest">
                {link}
              </a>
            ))}
          </div>
          <p className="text-sm text-white/50 text-center font-medium max-w-md">
            © 2026 ConectaVida. Sistema de Respuesta Comunitaria de Precisión. <br />
            Comprometidos con la seguridad ciudadana.
          </p>
        </div>
      </footer>
    </div>
  );
}

