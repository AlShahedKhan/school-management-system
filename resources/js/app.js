import './bootstrap';
import "tailwindcss";
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

import './public-home';
import './teacher-table';

// Existing Blade scripts use the global SweetAlert API.
window.Swal = Swal;
