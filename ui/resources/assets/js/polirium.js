import './src/autosize'
import './src/countup'
import './src/input-mask'
import './src/dropdown'
import './src/tooltip'
import './src/popover'
import './src/switch-icon'
import './src/tab'
import './src/toast'
import './src/sortable'

// Import bootstrap and tabler
import * as bootstrap from 'bootstrap'
import * as tabler from './src/tabler'

// Re-export everything from bootstrap.js (single source of truth)
export * from './src/bootstrap'

// Re-export tabler namespace
export * as tabler from './src/tabler'


//Polirium Core
import '../../../../../../vendor/power-components/livewire-powergrid/dist/powergrid'
import 'flatpickr'
import 'tom-select'
import 'autonumeric'

// Make bootstrap and tabler available globally
globalThis.bootstrap = bootstrap
globalThis.tabler = tabler
