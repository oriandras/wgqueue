import './bootstrap';
import Swal from 'sweetalert2'; // <--- EZT ADD HOZZÁ
window.Swal = Swal; // Hogy globálisan is elérhető legyen

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('livewire:initialized', () => {
    // Általános figyelő minden típusú swal eseményhez
    Livewire.on('swal:success', (data) => {
        // Livewire 3-ban az adat egy tömb első elemeként érkezik
        const message = data[0]?.message || data.message;

        Swal.fire({
            title: 'Siker!',
            text: message,
            icon: 'success',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    });

    Livewire.on('swal:error', (data) => {
        const message = data[0]?.message || data.message;

        Swal.fire({
            title: 'Hiba!',
            text: message,
            icon: 'error',
            confirmButtonColor: '#d33'
        });
    });
});
