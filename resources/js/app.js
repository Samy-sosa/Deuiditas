import './bootstrap';
import { driver } from "driver.js";
import "driver.js/dist/driver.css";

const tour = driver({
    showProgress: true,
    nextBtnText: 'Siguiente —>',
    prevBtnText: '<— Atrás',
    doneBtnText: '¡Entendido!',
    popoverClass: 'glass-popover',
    steps: [
        { 
            element: '#logo-tienda', 
            popover: { title: 'Tu Marca', description: 'Aquí verás el nombre de tu negocio configurado.', side: "right"} 
        },
        { 
            element: '#step-buscador', 
            popover: { title: 'Buscador Pro', description: 'Encuentra clientes o códigos al instante con ⌘K.', side: "bottom"} 
        },
        { 
            element: '#step-nuevo-registro', 
            popover: { title: 'Crea deudas', description: 'Desde aquí puedes registrar un nuevo apartado rápidamente.', side: "right"} 
        },
        { 
            element: '#step-recaudado', 
            popover: { title: 'Caja Real', description: 'Este es el dinero que ya tienes físicamente en mano.', side: "top"} 
        }
    ]
});

window.ejecutarTour = () => tour.drive();